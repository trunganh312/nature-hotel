<?
session_start();
mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ob_start();

$path_root      =   $_SERVER['DOCUMENT_ROOT'] . '/';
$path_core      =   $path_root . '../Vietgoing_Core/';
$path_config    =   $path_root . 'Core/';

require_once($path_core . '/Env/ConfigEnv.php');
include_once($path_core . 'Config/Constant.php');
include_once($path_config . 'Config/constant_web.php');
require_once($path_core . 'Function/Function.php');
require_once($path_core . 'Classes/Database.php');
require_once($path_core . 'Model/Model.php');

require_once($path_core . 'Classes/GenerateQuery.php');
require_once($path_core . 'Classes/User.php');

(new User)->fakeLogin(getValue("id"));
?>