<?

use src\Services\CommonService;

include('config_module.php');
//Check quyền
$Auth->checkPermission('system_table_log_create');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Thêm mới bảng lưu log';
$table      =   'table_log';
$field_id   =   'talo_id';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('talo_table', DATA_STRING, '', 'Bạn chưa nhập tên bảng', 'Tên bảng đã tồn tại')
        ->add('talo_note', DATA_STRING, '', '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {

    //Validate form
    if ($Query->validate()) {
        
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
        }
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
}
/** --- End of Submit form --- **/

