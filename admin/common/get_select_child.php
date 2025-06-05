<?php

use src\Services\CommonService;

include('config_module.php');

/** --- Lấy các data con của 1 đối tượng. VD lấy các Quận/Huyện của 1 Tỉnh/TP --- **/

//Lấy type xem load cái gì
$type   =   getValue('type', GET_STRING, GET_GET, 'district');
$id     =   getValue('id');
$json   =   getValue('json');
$html   =   '<option value="0">-- Chọn --</option>';

//Các type khác nhau lấy ở các bảng khác nhau
switch ($type) {
    case 'area':
        $data   =   $Model->getListData('areas', 'are_id, are_name', 'are_country = ' . $id, 'are_name');
        $html   =   '<option value="0">-- Chọn khu vực --</option>';
        foreach ($data as $k => $v) {
            $html   .=  '<option value="' . $k . '">' . $v . '</option>';
        }
        break;
        
    case 'district':
        $data   =   $Model->getListData('districts', 'dis_id, dis_name', 'dis_city = ' . $id, 'dis_name');
        $html   =   '<option value="0">-- Chọn Quận/Huyện --</option>';
        if ($json) {
            CommonService::resJson($data);
        }
        foreach ($data as $k => $v) {
            $html   .=  '<option value="' . $k . '">' . $v . '</option>';
        }
        break;
        
    case 'ward':
        $data   =   $Model->getListData('wards', 'war_id, war_name', 'war_district = ' . $id, 'war_name');
        $html   =   '<option value="0">-- Chọn Xã/Phường --</option>';
        if ($json) {
            CommonService::resJson($data);
        }
        foreach ($data as $k => $v) {
            $html   .=  '<option value="' . $k . '">' . $v . '</option>';
        }
        break;
        
    case 'module':
        $data   =   $Model->getListData('modules_file', 'modf_id, modf_name', 'modf_module_id = ' . $id . ' AND modf_is_parent = 0', 'modf_order');
        $html   =   '<option value="0">-- Chọn Tính năng --</option>';
        if ($json) {
            CommonService::resJson($data);
        }
        foreach ($data as $k => $v) {
            $html   .=  '<option value="' . $k . '">' . $v . '</option>';
        }
        break;
}

echo    $html;