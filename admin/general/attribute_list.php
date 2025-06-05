<?

use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

//Check quyền
$Auth->checkPermission('general_attribute_list');
//Alias Menu
$Auth->setAliasMenu('general_attribute_list');

/** --- Khai báo một số biến cơ bản --- **/
$table          =   "attribute_name";
$field_id       =   "atn_id";
$page_title     =   'Danh sách thuộc tính';
$atn_group      =   $cfg_group;
$atn_type       =   $cfg_type_attribute;
$has_edit       =   $Auth->hasPermission('general_attribute_edit');
$has_value      =   $Auth->hasPermission('general_attribute_value_list');
$has_create     =   $Auth->hasPermission('general_attribute_create');
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('atn_name', 'Tên thuộc tính', TAB_TEXT, true, true)
        ->column('atn_group', 'Nhóm', TAB_SELECT, true)
        ->column('atn_type', 'Kiểu', TAB_SELECT, true)
        ->column('atn_alias_search', 'Alias search', TAB_TEXT, true)
        ->column('atn_list_value', 'Các giá trị')
        ->column('atn_column', 'Column', TAB_NUMBER, false, true)
        ->column('atn_order', 'Thứ tự', TAB_NUMBER, false, true)
        ->column('atn_note', 'Mô tả');
if ($has_edit) {
    $Table->column('atn_show_filter', 'Lọc', TAB_CHECKBOX)
            ->column('atn_hot', 'Hot', TAB_CHECKBOX)
            ->column('atn_active', 'Act', TAB_CHECKBOX)
            ->addED(true)
            ->setEditFileName('attribute_edit.php')
            ->setEditThickbox(['width' => 700, 'height' => 500, 'title' => 'Sửa thông tin thuộc tính']);
}
$Table->addSQL("SELECT * FROM attribute_name ORDER BY atn_order, atn_name");

$rows = DB::pass()->query($Table->sql_table)->toArray();

// Chỉnh sửa dữ liệu trước khi trả về
$rows = array_map(function ($row) use ($atn_group, $atn_type) {
    $row['atn_type_text']           = array_get($atn_type, $row['atn_type']);
    $row['atn_group_text']          = array_get($atn_group, $row['atn_group']);
    return $row;
}, $rows);

$res = [
    'rows' => $rows,
    'permissions' => [
        'hasEdit' => $has_edit,
        'hasCreate' => $has_create,
        'hasValue' => $has_value,
    ],
    "pagination" => [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
];

$res['others']['atn_group'] = [];
foreach ($atn_group as $k => $v) {
    $res['others']['atn_group'][] = [
        "value" => $k,
        "label" => $v
    ];
}

$res['others']['atn_type'] = [];
foreach ($atn_type as $k => $v) {
    $res['others']['atn_type'][] = [
        "value" => $k,
        "label" => $v
    ];
}

if(getValue('json')) {
    CommonService::resJson($res);
}

Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-general-attribute-list', 'admin');
