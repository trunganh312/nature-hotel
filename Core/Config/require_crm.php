<?php

use src\Facades\DB;
use src\Libs\Utils;

session_start();
mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ob_start();

define('PATH_ROOT', realpath(__DIR__ .'/../..'));
define('PATH_CORE', PATH_ROOT . '/Core');

require_once(PATH_ROOT .'/vendor/autoload.php');

include_once(PATH_CORE .'/Config/constants.php');
require_once(PATH_CORE .'/Function/functions.php');
require_once(PATH_CORE .'/Model/Model.php');
require_once(PATH_CORE .'/Classes/GenerateQuery.php');
require_once(PATH_CORE .'/Classes/Router.php');
require_once(PATH_CORE .'/Classes/Image.php');
require_once(PATH_CORE .'/Classes/Upload.php');
require_once(PATH_CORE .'/Classes/GenerateForm.php');
require_once(PATH_CORE .'/Classes/Layout.php');
require_once(PATH_CORE .'/Model/LogModel.php');
require_once(PATH_CORE .'/Classes/DataTable.php');
require_once(PATH_CORE .'/Classes/Auth.php');
include_once(PATH_CORE .'/Config/config.php');
include_once(PATH_CORE .'/Config/config_log.php');

//Class Account login
$Auth   =   new Auth;

$Router         =   new Router;
$Layout         =   new Layout;
$Log            =   new LogModel;

//Note đang làm dở
//exit('Đang làm chọn khách sạn ở trang /home');

?>
