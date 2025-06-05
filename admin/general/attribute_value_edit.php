<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('general_attribute_value_edit');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Sửa giá trị của thuộc tính';
$table      =   'attribute_value';
$field_id   =   'atv_id';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT attribute_value.*, atn_id, atn_name
                                FROM " . $table . "
                                INNER JOIN attribute_name ON atv_attribute_id = atn_id
                                WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

//URL để redirect sau khi thêm mới xong
$url_return =   base64_decode(getValue('url', GET_STRING, GET_GET, base64_encode('attribute_value_list.php?id=' . $record_info['atn_id'])));
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('atv_name', DATA_STRING, '', 'Bạn chưa nhập giá trị')
        ->add('atv_order', DATA_INTEGER, 0)
        ->add('atv_icon', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    //Remove thẻ i ở icon đi
    $icon   =   getValue('atv_icon', GET_STRING, GET_POST, '');
    $icon   =   str_replace('<i class="', '', $icon);
    $icon   =   str_replace('"></i>', '', $icon);
    $_POST['atv_icon']  =   $icon;
    
    if ($Query->validate($field_id, $record_id)) {
        
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
            CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
        }
        
    } else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
}
/** --- End of Submit form --- **/
