<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('general_attribute_value_create');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Thêm mới giá trị của thuộc tính';

//Lấy thông tin của attribute_name
$atn_id =   getValue('id');
$attribute_info =   $DB->query("SELECT * FROM attribute_name WHERE atn_id = " . $atn_id)->getOne();
if (empty($attribute_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

//URL để redirect sau khi thêm mới xong
$url_return =   base64_decode(getValue('url', GET_STRING, GET_GET, base64_encode('list_attribute_value.php?id=' . $atn_id)));
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('attribute_value');
$Query->add('atv_attribute_id', DATA_INTEGER, $atn_id)
        ->add('atv_name', DATA_STRING, '', 'Bạn chưa nhập giá trị')
        ->add('atv_order', DATA_INTEGER, 0)
        ->add('atv_icon', DATA_STRING, '')
        ->add('atv_active', DATA_INTEGER, 1);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    //Remove thẻ i ở icon đi
    $icon   =   getValue('atv_icon', GET_STRING, GET_POST, '');
    $icon   =   str_replace('<i class="', '', $icon);
    $icon   =   str_replace('"></i>', '', $icon);
    $_POST['atv_icon']  =   $icon;
    
    //Lấy giá trị hexa max của attribute name để tự nhân thêm 2. Hiện tại chưa dùng đến giá trị này nhưng lưu trước có thể sau này cần dùng
    $row    =   $DB->query("SELECT MAX(atv_value_hexa) AS max_value
                            FROM attribute_value
                            WHERE atv_attribute_id = " . $atn_id)
                            ->getOne();
    $atv_value_hexa =   1;
    if (isset($row['max_value'])) {
        $atv_value_hexa =   (int)$row['max_value'] * 2;
    }
    $Query->add('atv_value_hexa', DATA_INTEGER, $atv_value_hexa);
    
     //Validate form
     if ($Query->validate()) {
        
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
            CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
        }
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
}
/** --- End of Submit form --- **/
