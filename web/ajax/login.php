<?
include('../Core/Config/require_web.php');

$email      =   getValue('email', GET_STRING, GET_POST, '', 1);
$password   =   getValue('password', GET_STRING, GET_POST, '', 1);

$User   =   new User($email, $password);
$response   =   [
                'status'    =>  0,
                'message'   =>  ''
                ];

if ($User->logged) {
    
    $remember   =   getValue('remember', GET_INT, GET_POST);
    
    $User->saveCookie($remember == 1 ? TIME_REMEMBER : 0);
    
    $response['status'] =   1;
    
} else {
    $response['message']    =   $User->errMsg;
}

echo    json_encode($response);
exit;
?>