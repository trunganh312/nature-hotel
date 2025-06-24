<?

use src\Facades\DB;
use src\Libs\Utils;
use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

//Check quyền
$Auth->checkPermission('general_document_list');
//Alias Menu
$Auth->setAliasMenu('general_document_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Danh sách bài viết';
/** --- DataTable --- **/
// Lấy ra danh sách bài viết 
$Table  =   new DataTable('document', 'doc_id');

$Table->column('doc_name', 'Tên danh mục', TAB_TEXT);
$Table->column('doc_parent_id', 'Tên danh mục cha', TAB_SELECT);
$Table->column('doc_img', 'Ảnh', TAB_TEXT);

$Table->column('doc_active', 'Act', TAB_CHECKBOX, false, true);
$parent_id = getValue('doc_parent_id');
$doc_name  = getValue('doc_name', GET_STRING, GET_GET, '');
$params = [
    'doc_parent_id' => $parent_id,
    'doc_name' => $doc_name
];
$sql_where = '';
if ($parent_id > 0) {
    $sql_where = " AND doc_parent_id = $parent_id";
}
if ($doc_name!= '') {
    $sql_where.= " AND doc_name LIKE '%".$doc_name."%'";
}
$Table->addSQL("SELECT * FROM document WHERE 1 $sql_where ORDER BY doc_parent_id DESC, doc_order ASC");
$data   =   DB::pass()->query($Table->sql_table)->toArray();
$data = array_map(function($item) {
    $item['doc_parent_name'] = DB::pass()->query('SELECT * FROM document WHERE doc_id = ' . $item['doc_parent_id'])->getOne()['doc_name'] ?? '';
    global $Router;
    $item['doc_img_url'] = $item['doc_img'] ? $Router->srcDocument($item['doc_img']) : '';
    return $item;
}, $data);

$res = [
    "rows" => $data,
    "pagination" => [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
    "others" => [
    ],
    'permissions' => [
        'hasEdit' => Utils::auth()->hasPermission('document_edit'),
        'hasCreate' => Utils::auth()->hasPermission('document_create'),
    ],
    'params' => $params
];

// Lấy ra những cái mà doc_parent_id =0
$doc_parents = DB::pass()->query("SELECT * FROM document WHERE doc_parent_id = 0")->pluck('doc_id', 'doc_name');
$res['others']['doc_parents'] = [];

foreach ($doc_parents as $k => $v) {
    $res['others']['doc_parents'][] = [
        "value" => $k,
        "label" => $v
    ];
}

if(getValue('json')) {
    CommonService::resJson($res);
}

Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-general-document-list', 'admin');
