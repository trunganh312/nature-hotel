<?

use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');
//Check quyền
$Auth->checkPermission('general_crm_config');
//Alias Menu
$Auth->setAliasMenu('general_crm_config');


/** --- Khai báo một số biến cơ bản --- **/
$table      =   'config_crm';
$field_id   =   'cocr_id';
$page_title =   'Sửa thông tin cấu hình';
$record_id  =   getValue('id') || 1;
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();

/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('cocr_ip', DATA_STRING, '', 'Bạn chưa nhập ID')
        ->add('cocr_password_default', DATA_STRING, PASSWORD_DEFAULT);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    //Validate form
    if ($Query->validate()) {

        if(empty($record_info)) {
            if ($DB->execute($Query->generateQueryInsert()) > 0) {
                CommonService::resJson();
            }
        }else {
            if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
                CommonService::resJson();
            } else {
                $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
                CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
            }
        }
        
        
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
    
}
/** --- End of Submit form --- **/

Vue::setData(compact('record_info'));
Vue::setTitle($page_title);
Vue::render('crm-general-config', 'admin');
