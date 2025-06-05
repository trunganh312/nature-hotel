<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('account_admin_create');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Thêm mới tài khoản Admin';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('admins');
$Query->add('adm_name', DATA_STRING, '', 'Bạn chưa nhập họ tên')
        ->add('adm_email', DATA_STRING, '', 'Bạn chưa nhập Email', 'Email này đã tồn tại')
        ->add('adm_phone', DATA_STRING, '', 'Bạn chưa nhập số ĐT', 'Số ĐT này đã tồn tại')
        ->add('adm_link_fb', DATA_STRING, '')
        ->add('adm_time_create', DATA_INTEGER, CURRENT_TIME)
        ->add('adm_last_update', DATA_INTEGER, CURRENT_TIME);
/** --- End of Class query để insert dữ liệu --- **/

//Lấy ID cua cac group
$arr_value_group    =   getValue('group', GET_ARRAY, GET_POST, []);

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    /** --- Password --- **/
    //Admin random number
    $adm_random =   $Auth->generateRandom();
    $Query->add('adm_random', DATA_INTEGER, $adm_random);
    $Query->add('adm_password', DATA_STRING, $Auth->generatePassword(PWD_DEFAULT, $adm_random));
    
    //Kiểm tra Email
    $email  =   getValue('adm_email', GET_STRING, GET_POST, '');
    $phone  =   getValue('adm_phone', GET_STRING, GET_POST, '');
    if (!validate_email($email)) {
        $Query->addError('Email không hợp lệ');
    }

    if (!validate_phone($phone)) {
        $Query->addError('Số ĐT không hợp lệ');
    }
    
    //Check nhóm
    if (empty($arr_value_group)) {
        $Query->addError('Bạn chưa chọn Nhóm');
    }
    
    //Validate form
    if ($Query->validate()) {
        
        $admin_id   =   $DB->executeReturn($Query->generateQueryInsert());
        
        if ($admin_id > 0) {
            
            //Insert các group của Admin
            $group_value    =   [];
            foreach ($arr_value_group as $id) {
                $group_value[]  =   '(' . $admin_id . ',' . $id . ')';
            }
            
            if (!empty($group_value))   $DB->execute("INSERT INTO admins_group_admins VALUES" . implode(',', $group_value));
            
            /** Lưu log **/
            $data_new   =   $DB->query("SELECT * FROM admins WHERE adm_id = $admin_id")->getOne();
            $data_new['group']  =   $group_value;
            unset($data_new['adm_password']);
            $Log->setDataNew($data_new)->setContent('Tạo mới tài khoản')->createLog('admin', $admin_id, LOG_CREATE);
            
            CommonService::resJson();
            
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
        }
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
}
