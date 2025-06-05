<?

use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');
//Check quyền
$Auth->checkPermission('general_document_edit');
//Alias Menu
$Auth->setAliasMenu('general_document_edit');


/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Sửa bài viết';
$table = 'document';
$field_id       =   'doc_id';
$record_id      =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exit('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('document');
$Query->add('doc_name', DATA_STRING, '', 'Bạn chưa nhập tên danh mục')
    ->add('doc_icon', DATA_STRING, '')
    ->add('doc_slug', DATA_STRING, '')
    ->add('doc_order', DATA_INTEGER, 0)
    ->add('doc_active', DATA_INTEGER, 1)
    ->add('doc_content', DATA_STRING, '')
    ->add('doc_parent_id', DATA_INTEGER, 0)
    ->setRemoveHTML(false);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    // Validate form
    if ($Query->validate($field_id, $record_id)) {
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Có lỗi xảy ra khi cập nhật bản ghi');
        }
    }
}

// Danh sách danh mục
$categories = DB::pass()->query('SELECT * FROM document WHERE doc_active = 1')->pluck('doc_id', 'doc_name');
$res = [
    'row' => $record_info
];
$res['others']['categories'] = [];
foreach ($categories as $k => $v) {
    $res['others']['categories'][] = [
        "value" => $k,
        "label" => $v
    ];
}

Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-general-document-edit', 'admin');

?>
