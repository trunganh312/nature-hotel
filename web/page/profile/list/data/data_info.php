<?
require_once($path_core . 'Classes/GenerateQuery.php');
require_once($path_core . 'Classes/Upload.php');

$path_upload    =   $path_root . 'image/user/';

$name       =   getValue('name', GET_STRING, GET_POST, $User->info['use_name']);
$phone      =   convert_phone_number(getValue('phone', GET_STRING, GET_POST, $User->info['use_phone']));
$address    =   getValue('address', GET_STRING, GET_POST, $User->info['use_address']);
$ward       =   getValue('ward', GET_INT, GET_POST, $User->info['use_ward']);
$district   =   getValue('district', GET_INT, GET_POST, $User->info['use_district']);
$city       =   getValue('city', GET_INT, GET_POST, $User->info['use_city']);

//Lấy DS các xã/phường để fill vào form
$list_district  =   $list_ward  =   [];
if ($city > 0) {
    $list_district  =   $Model->getListData('district', 'dis_id, dis_name', "dis_city = $city", 'dis_name');
}
if ($district > 0) {
    $list_ward      =   $Model->getListData('ward', 'war_id, war_name', "war_district = $district", 'war_name');
}

$action =   getValue('action', GET_STRING, GET_POST, '');
$error  =   [];
$array_resize   =   [
                    SIZE_LARGE  =>  ["maxwidth" => 500, "maxheight" => 500],
                    SIZE_MEDIUM =>  ["maxwidth" => 250, "maxheight" => 250],
                    SIZE_SMALL  =>  ["maxwidth" => 120, "maxheight" => 120]
                    ];

/** Cập nhật thông tin cá nhân **/
if ($action == 'info') {
    
    $Query  =   new GenerateQuery('user');
    $Query->add('use_name', DATA_STRING, $name, 'Bạn chưa nhập Họ và tên')
            ->add('use_phone', DATA_STRING, convert_phone_number($phone, '84'), 'Số điện thoại không hợp lệ')
            ->add('use_address', DATA_STRING, $address)
            ->add('use_ward', DATA_INTEGER, $ward)
            ->add('use_district', DATA_INTEGER, $district)
            ->add('use_city', DATA_INTEGER, $city)
            ->add('use_last_update_info', DATA_INTEGER, CURRENT_TIME);
    
    if (!validate_phone($phone)) {
        $Query->addError('Số điện thoại không hợp lệ');
    }
    
    if ($Query->validate()) {
    
        //Ảnh đại diện
        $Upload     =   new Upload('avatar', $path_upload, 500, 500);
        $use_avatar =   $Upload->new_name;
        $Query->addError($Upload->error);
        if (!empty($use_avatar))    $Query->add('use_avatar', DATA_STRING, $use_avatar);
        
        if ($DB->execute($Query->generateQueryUpdate('use_id', $User->id))) {
            
            //Resize ảnh
            if (!empty($use_avatar)) {
                $Upload->resizeImage($array_resize);
            }
            
            set_session_toastr();
            redirect_url($_SERVER['REQUEST_URI']);
            
        } else {
            //Nếu cập nhật lỗi mà có upload ảnh thì xóa ảnh cũ đi
            if (!empty($use_avatar)) {
                if (file_exists($path_upload . $use_avatar)) {
                    unlink($path_upload . $use_avatar);
                }
            }
            
        }
    }
    $error  =   array_merge($error, $Query->error);
}

/** Đổi mật khẩu **/
$error_password =   [];
if ($action == 'password') {
    
    $current_password       =   getValue('current_password', GET_STRING, GET_POST, '');
    $new_password           =   getValue('new_password', GET_STRING, GET_POST, '');
    $new_password_confirm   =   getValue('new_password_confirm', GET_STRING, GET_POST, '');
    
    if ($new_password != $new_password_confirm) {
        $error_password[]   =   'Xác nhận mật khẩu mới không trùng khớp';
    }
    
    if (empty($error_password)) {
        //Update password
        $result =   $User->changePassword($current_password, $new_password);
        
        switch ($result) {
            case 0:
                $error_password[]   =   'Bạn chưa đăng nhập';
                break;
                
            case 1:
                //Nếu thành công thì logout để cho đăng nhập lại
                set_session_toastr('update_password', 'success');
                redirect_url($_SERVER['REQUEST_URI']);
                break;
                
            case 2:
                $error_password[]   =   'Mật khẩu hiện tại không đúng';
                break;
                
            case 3:
                $error_password[]   =   'Mật khẩu mới phải có tối thiểu 6 ký tự';
                break;
        }
    }
}

?>