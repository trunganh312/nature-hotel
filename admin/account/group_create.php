<?

use src\Services\CommonService;

include('config_module.php');
//Check quyền sử dụng tính năng
$Auth->checkPermission('account_admin_group_create');

$page_title =   'Thêm mới nhóm quyền';
$table      =   'admins_group';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('gro_name', DATA_STRING, '', 'Bạn chưa nhập tên nhóm quyền', 'Tên nhóm quyền này đã tồn tại')
        ->add('gro_note', DATA_STRING, '')
        ->add('gro_no_level', DATA_INTEGER, 0)
        ->add('gro_active', DATA_INTEGER, 1);
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