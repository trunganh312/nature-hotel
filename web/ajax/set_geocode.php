<?
include('../Core/Config/require_web.php');

//Lấy tọa độ và địa chỉ để set session
$location   =   [
    'lat'       =>  getValue('lat', GET_DOUBLE, GET_POST, 0),
    'lng'       =>  getValue('lng', GET_DOUBLE, GET_POST, 0),
    'address'   =>  getValue('address', GET_STRING, GET_POST, '')
];
$_SESSION['vg_location']    =   json_encode($location);
?>