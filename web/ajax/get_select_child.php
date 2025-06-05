<?
include('../Core/Config/require_web.php');

/** --- Lấy các data con của 1 đối tượng. VD lấy các Quận/Huyện của 1 Tỉnh/TP --- **/

//Lấy type xem load cái gì
$type   =   getValue('type', 'str', 'GET', 'district');
$id     =   getValue('id');
$html   =   '<option value="0">Chọn</option>';

//Các type khác nhau lấy ở các bảng khác nhau
switch ($type) {

    case 'area':
        $data   =   $Model->getListData('area', 'are_id, are_name', 'are_country = ' . $id, 'are_name');
        $html   =   '<option value="0">--Chọn khu vực--</option>';
        foreach ($data as $k => $v) {
            $html   .=  '<option value="' . $k . '">' . $v . '</option>';
        }
        break;
        
    case 'district':
        $data   =   $Model->getListData('district', 'dis_id, dis_name', 'dis_city = ' . $id, 'dis_name');
        $html   =   '<option value="0">--Chọn Quận/Huyện--</option>';
        foreach ($data as $k => $v) {
            $html   .=  '<option value="' . $k . '">' . $v . '</option>';
        }
        break;

    case 'ward':
        $data   =   $Model->getListData('ward', 'war_id, war_name', 'war_district = ' . $id, 'war_name');
        $html   =   '<option value="0">--Chọn Xã/Phường--</option>';
        foreach ($data as $k => $v) {
            $html   .=  '<option value="' . $k . '">' . $v . '</option>';
        }
        break;
}

echo    $html;