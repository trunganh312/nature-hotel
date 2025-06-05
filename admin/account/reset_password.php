<?php

use src\Facades\DB;
use src\Models\ConfigUser;
use src\Services\CommonService;
require_once("config_module.php");
require_once(PATH_CORE .'/Classes/FakeLogin.php');

$config = DB::pass()->query("SELECT * FROM config_crm WHERE 1")->getOne();
$password = PWD_DEFAULT;
if(isset($config) && !empty($config['cocr_password_default'])) {
    $password = $config['cocr_password_default'];
}

$user_id = getValue('id', GET_INT, GET_GET);
$FakeLogin  =   new FakeLogin;
$result = $FakeLogin->resetPassword('users', 'user', $user_id, $password);

if ($result) {
    CommonService::resJson();
}else {
    CommonService::resJson(['Có lỗi xảy ra'], STATUS_INACTIVE, 'Error');
}

?>