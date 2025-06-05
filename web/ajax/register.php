<?
include('../Core/Config/require_web.php');

$name       =   getValue('name', GET_STRING, GET_POST, '');
$email      =   getValue('email', GET_STRING, GET_POST, '');
$password   =   getValue('password', GET_STRING, GET_POST, '');
$response   =   [
                'status'    =>  0,
                'message'   =>  ''
                ];

//Kiểm tra password
if (!validate_password($password)) {
    $response['message']    =   'Mật khẩu phải có ít nhất 6 ký tự';
    echo    json_encode($response);
    exit();
}

$data_register  =   [
                    'name'  =>  $name,
                    'email' =>  $email,
                    'password'  =>  $password
                    ];

//Tạo TK
if ($User->register($data_register)) {
    //Lưu cookie đăng nhập
    $User->saveCookie(TIME_REMEMBER);
    
    //Set session để sang trang profile cảm ơn
    $_SESSION['new_register']   =   'success';
    
    $response['status'] =   1;
    
} else {
    $response['message']    =   $User->errMsg;
}

echo    json_encode($response);
exit;
?>