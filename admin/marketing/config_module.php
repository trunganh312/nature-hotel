<?
include('../../Core/Config/require_crm.php');
require_once($path_core . "Model/VoucherModel.php");

$cfg_city   =   $Model->getListData('city', 'cit_id, cit_name', '', 'cit_name');

$cfg_group_collection   =   [
    GROUP_HOTEL         =>  'Khách sạn',
    GROUP_TOUR          =>  'Tour',
    GROUP_DESTINATION   =>  'Địa danh'
];

//Model
$VoucherModel   =   new VoucherModel;

$path_upload_banner =   '../../image/banner/';
?>