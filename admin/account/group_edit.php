<?

use src\Services\CommonService;

include('config_module.php');
//Check quyền sử dụng tính năng
$Auth->checkPermission('account_admin_group_edit');

$page_title =   'Thêm mới nhóm quyền';
$table      =   'admins_group';
$field_id   =   'gro_id';
$page_title =   'Sửa thông tin nhóm quyền';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('gro_name', DATA_STRING, '', 'Bạn chưa nhập tên nhóm quyền', 'Tên nhóm quyền này đã tồn tại')
        ->add('gro_note', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            
            CommonService::resJson();

        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
        }
        
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
    
}