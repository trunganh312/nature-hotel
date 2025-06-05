<?
include('../Core/Config/require_web.php');
require_once($path_core . 'Classes/Mailer.php');

$email  =   getValue('email', GET_STRING, GET_POST, '');
$response   =   [
                'status'    =>  0,
                'message'   =>  ''
                ];
                
if ($email != '') {
    if ($User->confirmResetPassword($email)) {
        $response['status'] =   1;
    } else {
        $response['message']    =   $User->errMsg;
    }
}

echo    json_encode($response);
exit;
?>