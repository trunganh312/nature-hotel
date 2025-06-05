<?php

use src\Libs\Utils;
use src\Libs\Vue;
use src\Services\CommonService;

ob_start();
session_start();

define('PATH_ROOT', realpath(__DIR__ .'/..'));
define('PATH_CORE', PATH_ROOT . '/Core');
require_once(PATH_ROOT .'/vendor/autoload.php');

require_once(PATH_CORE . '/Config/constants.php');
include_once(PATH_CORE . '/Function/functions.php');
require_once(PATH_CORE . '/Model/Model.php');
require_once(PATH_CORE . '/Classes/GenerateQuery.php');
require_once(PATH_CORE . '/Classes/Auth.php');

if (isset($_SESSION['login_error'])) unset($_SESSION['login_error']);
$error  =   [];

$email     =   getValue('email', GET_STRING, GET_POST, '', 1);
$password  =   getValue('password', GET_STRING, GET_POST, '', 1);

if(CommonService::isPost()) {
	$Auth   =   new Auth($email, $password);
	if ($Auth->logged()){
        CommonService::resJson();
	}
    $error  =   $Auth->getError();
    if(!empty($error)) {
        CommonService::resJson($error, STATUS_INACTIVE, 'Dữ liệu không hợp lệ');
    } else {
        CommonService::resJson();
    }
} else {
    $Auth   =   new Auth('', '', false);
    if ($Auth->logged()) {
        redirect_url('/');
    }
}

Vue::setTitle(Utils::env('BRAND_NAME'));
Vue::setSidebar(false);
Vue::setHeader(false);
Vue::render('a-crm-login', 'admin');
