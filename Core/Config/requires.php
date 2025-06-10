<?php

use src\Facades\DB;

define('PATH_ROOT', realpath(__DIR__ .'/../..'));
define('PATH_CORE', PATH_ROOT . '/Core');

require_once(PATH_ROOT .'/vendor/autoload.php');
require_once(PATH_CORE .'/Model/Model.php');
require_once(PATH_CORE .'/Classes/GenerateQuery.php');
require_once(PATH_CORE .'/Classes/Router.php');
require_once(PATH_CORE .'/Classes/Image.php');
require_once(PATH_CORE .'/Classes/Upload.php');
require_once(PATH_CORE .'/Classes/GenerateForm.php');
require_once(PATH_CORE .'/Model/LogModel.php');
require_once(PATH_CORE .'/Model/HotelModel.php');
require_once(PATH_CORE .'/Classes/DataTable.php');
require_once(PATH_CORE .'/Model/UserModel.php');
include_once(PATH_CORE . '/Config/constants.php');
include_once(PATH_CORE .'/Config/config.php');
require_once(PATH_CORE . '/Function/functions.php');
require_once(PATH_CORE . '/Classes/User.php');
require_once(PATH_CORE . '/Classes/Search.php');
require_once(PATH_CORE . '/Model/AttributeModel.php');
require_once(PATH_CORE . '/Model/PlaceModel.php');

$cfg_website    =   DB::query("SELECT * FROM configuration WHERE con_id = 1")->getOne();
if (empty($cfg_website)) {
    exit('<p style="text-align:center">Website đang được bảo trì, vui lòng quay lại sau ít phút!</p>');
}

$Router         =   new Router;
$HotelModel     =   new HotelModel;
$cfg_time_start =  get_microtime();
?>
