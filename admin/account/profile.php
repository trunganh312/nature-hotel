<?

use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'admins';
$field_id   =   'adm_id';
$page_name  =   'Tài khoản';    //Hiển thị trên Tab trình duyệt cho ngắn gọn
$page_title =   'Cập nhật thông tin tài khoản'; //Thẻ H1 của page
$record_id  =   $Auth->id;

$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('adm_phone', DATA_STRING, '', 'Bạn chưa nhập số điện thoại')
        ->add('adm_sex', DATA_INTEGER, 0, 'Bạn chưa chọn giới tính')
        ->add('adm_address', DATA_STRING, '', 'Bạn chưa nhập địa chỉ');

/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {

    /** --- Đổi password --- **/
    $new_password       =   getValue('new_password', 'str', 'POST', '');

    if ($new_password != '') {
        $current_pwd        =   getValue('current_pwd', 'str', 'POST', '');
        $new_password_cf    =   getValue('new_password_confirm', 'str', 'POST', '');

        if ($Auth->generatePassword($current_pwd, $record_info['adm_random']) != $record_info['adm_password']) {
            $Query->addError('Mật khẩu hiện tại không đúng');
        }

        if ($new_password != $new_password_cf) {
            $Query->addError('Mật khẩu xác nhận không trùng khớp');
        }

        if (strlen($new_password) < 6) {
            $Query->addError('Mật khẩu mới phải có tối thiểu 6 ký tự');
        } else {
            $adm_password   =   $Auth->generatePassword($new_password, $record_info['adm_random']);
            $Query->add('adm_password', DATA_STRING, $adm_password, 'Vui lòng nhập Mật khẩu');
        }
    }
    
    /** Avatar **/
    $adm_avatar = '';
    if(isset($_FILES['adm_avatar'])) {
        $Upload     =   new Upload('adm_avatar', $path_image_admin, 450, 450);
        $adm_avatar =   $Upload->new_name;
        $Query->addError($Upload->error);
    }
    if ($adm_avatar != '') {
        $Query->add('adm_avatar', DATA_STRING, $adm_avatar);
    }
    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            
            //Xóa ảnh cũ
            $Image->deleteFile($path_image_admin, $record_info['adm_avatar']);
            
            CommonService::resJson();
            
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
        }
        
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
    
}
$record_info['adm_avatar'] = $Router->srcAdminAvatar($record_info['adm_avatar']);
$others['adm_sex'] = [];
foreach ($cfg_sex as $k => $v) {
    $others['adm_sex'][] = [
        "value" => $k,
        "label" => $v
    ];
}

if(getValue('json')) {
    CommonService::resJson($res);
}

Vue::setData(compact('record_info', 'others'));
Vue::setTitle($page_title);
Vue::render('crm-account-profile', 'admin');    
