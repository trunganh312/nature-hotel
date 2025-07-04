<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('general_bank_edit');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'banks';
$field_id   =   'bak_id';
$page_title =   'Sửa thông tin ngân hàng';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('bak_name', DATA_STRING, '', 'Bạn chưa nhập tên ngân hàng', 'Tên ngân hàng này đã tồn tại')
    ->add('bak_name_en', DATA_STRING, '', 'Bạn chưa nhập tên ngân hàng (EN)', 'Tên ngân hàng (EN) này đã tồn tại')
    ->add('bak_abbreviation', DATA_STRING, '', 'Bạn chưa nhập tên viết tắt ngân hàng', 'Tên viết tắt ngân hàng này đã tồn tại')
    ->add('bak_bin', DATA_STRING, '', 'Bạn chưa nhập mã BIN ngân hàng', 'Mã BIN ngân hàng đã tồn tại');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
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
