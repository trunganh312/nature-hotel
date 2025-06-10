<?
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ob_start();

$path_root      =   $_SERVER['DOCUMENT_ROOT'] . '/';
$path_core      =   $path_root . '../Core/';
$path_config    =   $path_root . 'Core/';

include_once($path_core . 'Config/requires.php');
include_once($path_config . 'Config/constant_web.php');
require_once($path_config . 'Classes/Layout.php');
include_once($path_config . 'Config/config_theme_version.php');
include_once($path_config . 'Config/config_web.php');



?>