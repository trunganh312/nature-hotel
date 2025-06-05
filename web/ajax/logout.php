<?
include('../Core/Config/require_web.php');

$User->logout();

$response   =   [
                'status'    =>  1,
                'message'   =>  ''
                ];

echo    json_encode($response);
exit;
?>