<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('account_admin_admin_edit');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'admins';
$field_id   =   'adm_id';
$page_title =   'Sửa thông tin Tài khoản Admin';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

//Lấy ra các Nhóm hiện tại của Admin
$list_current_group     =   $Auth->getListIDGroupOfAccount($record_id, true);
$record_info['group']   =   $list_current_group;

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('adm_name', DATA_STRING, '', 'Bạn chưa nhập họ tên')
        ->add('adm_email', DATA_STRING, '', 'Bạn chưa nhập Email', 'Email này đã tồn tại')
        ->add('adm_phone', DATA_STRING, '', 'Bạn chưa nhập số ĐT', 'Số ĐT này đã tồn tại')
        ->add('adm_link_fb', DATA_STRING, '')
        ->add('adm_last_update', DATA_INTEGER, CURRENT_TIME);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    $reset_pw   =   getValue('reset_pw', GET_INT, GET_POST, 0);
    if ($reset_pw == 1) {
        $Query->add('adm_password', DATA_STRING, $Auth->generatePassword(PWD_DEFAULT, $record_info['adm_random']));
    }
    
    //Kiểm tra Email
    $email  =   getValue('adm_email', GET_STRING, GET_POST, '');
    $phone  =   getValue('adm_phone', GET_STRING, GET_POST, '');
    if (!validate_email($email)) {
        $Query->addError('Email không hợp lệ');
    }

    if (!validate_phone($phone)) {
        $Query->addError('Số ĐT không hợp lệ');
    }
    
    /** Update lại các group của Admin **/
    //Lấy ID cua cac group
    $arr_value_group    =   getValue('group', GET_ARRAY, GET_POST, []);
    
    if (empty($arr_value_group)) {
        $Query->addError('Bạn chưa chọn Nhóm quyền');
    }
        
    //Update các group của Admin
    $group_insert   =   [];
    $group_delete   =   [];
    
    //Lấy ra các Group Thêm/Xóa
    foreach ($list_current_group as $id) {
        if (!in_array($id, $arr_value_group)) $group_delete[] =   $id;
    }
    foreach ($arr_value_group as $id) {
        if (!in_array($id, $list_current_group)) $group_insert[] =   '(' . $record_id . ',' . $id . ')';
    }
    
    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            
            //Xóa hoặc Thêm các Nhóm của Admin
            if (!empty($group_delete)) {
                $DB->execute("DELETE FROM admins_group_admins WHERE grac_account_id = " . $record_id . " AND grac_group_id IN(" . implode(',', $group_delete) . ")");
            }
            if (!empty($group_insert)) {
                $DB->execute("INSERT INTO admins_group_admins VALUES" . implode(',', $group_insert));
            }
            
            /** Lưu log **/
            $data_new   =   $DB->query("SELECT * FROM admins WHERE adm_id = $record_id")->getOne();
            $data_new['group']  =   $group_insert;
            unset($record_info['adm_password']);
            unset($data_new['adm_password']);
            $Log->setTable('admins')->genContent($record_info, $data_new)->createLog($record_id);
            
            CommonService::resJson();
            
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
        }
        
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
    
}