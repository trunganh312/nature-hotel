<?
include('../../Core/Config/require_crm.php');
require_once(PATH_CORE . '/Model/DataLevelModel.php');

$DataLevel  =   new DataLevelModel();
$path_picture_category  =   '../../image/category/';
$list_city  =   $Model->getListData('cities', 'cit_id, cit_name', '', 'cit_name');
$path_image_city        =   PATH_ROOT . '/static/image/city/';
$path_image_district        =   PATH_ROOT . '/static/image/district/';
?>