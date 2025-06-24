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
// xử lý ảnh
$doc_img = isset($_POST['doc_img']) ? addslashes($_POST['doc_img']) : '';
if (!file_exists($path_image_document)) {
    mkdir($path_image_document, 0777, true);
}
$old_record = $DB->query("SELECT * FROM document WHERE doc_id = $record_id")->getOne();
$imagePath = $old_record['doc_img'];
if (isset($_FILES['doc_image']) && $_FILES['doc_image']['error'] === UPLOAD_ERR_OK) {
    $Upload = new Upload('doc_image', $path_image_document, 450, 450);
    if ($Upload->error) {
        $Query->addError($Upload->error);

    } else {
        $imagePath = addslashes($Upload->new_name);
    }
} elseif (!empty($doc_img)) {
    $imagePath = $doc_img;

} else {
    $imagePath = '';
}
$_POST['doc_img'] = $imagePath; 
$Query->add('doc_img', DATA_STRING, $imagePath);
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
$res['others']['doc_img_url'] = $record_info['doc_img'] ? '/uploads/document/' . $record_info['doc_img'] : '';

Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-general-document-edit', 'admin');

?>
