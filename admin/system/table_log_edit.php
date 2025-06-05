<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('system_module_update');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Sửa thông tin bảng lưu log';
$table      =   'table_log';
$field_id   =   'talo_id';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('talo_table', DATA_STRING, '', 'Bạn chưa nhập tên bảng', 'Tên bảng đã tồn tại')
        ->add('talo_note', DATA_STRING, '', '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        
        $DB->execute($Query->generateQueryUpdate($field_id, $record_id));

        CommonService::resJson();
        
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
}
/** --- End of Submit form --- **/
