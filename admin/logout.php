<?
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

$Auth   =   new Auth;
$Auth->logout();
?>