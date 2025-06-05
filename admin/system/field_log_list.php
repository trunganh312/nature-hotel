<?

use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

//Check quyền
$Auth->checkPermission('system_list_field_log');
//Alias menu
$Auth->setAliasMenu('system_list_field_log');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Các trường dữ liệu cần lưu log';
$table      =   'field_log';
$field_id   =   'fie_id';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('talo_table', 'Bảng', TAB_TEXT, true, true)
        ->column('fie_field', 'Tên trường', TAB_TEXT, true, true)
        ->column('fie_name', 'Tên lưu log', TAB_TEXT, true, true)
        ->column('fie_type', 'Kiểu dữ liệu', TAB_SELECT, true)
        ->column('fie_table_target', 'Bảng lấy dữ liệu', TAB_TEXT, true)
        ->column('fie_field_id', 'Trường ID', TAB_TEXT)
        ->column('fie_field_value', 'Trường giá trị', TAB_TEXT)
        ->column('fie_variable', 'Biến giá trị', TAB_TEXT, true, true)
        ->column('fie_description', 'Mô tả', TAB_TEXT)
        ->addED($Auth->hasPermission('system_edit_field_log') ? true : false)
        ->setEditFileName('field_log_edit.php')
        ->setEditThickbox(['width' => 800, 'height' => 600, 'title' => 'Sửa thông tin trường lưu log']);
$fie_type   =   $cfg_field_type;

if(getValue('pageSize')) {
    $Table->setPageSize(getValue('pageSize'));
}

$Table->addSQL("SELECT $table.*, talo_table
                FROM $table
                INNER JOIN table_log ON fie_table_id = talo_id
                ORDER BY talo_table ASC, fie_field");

$data = DB::pass()->query($Table->sql_table)->toArray();

$data = array_map(function($item) use ($fie_type) {
    $item['fie_type_text']      = array_get($fie_type, $item['fie_type']);
    return $item;
}, $data);

// DS bảng
$list_table =   $Model->getListData('table_log', 'talo_id, talo_table', '', 'talo_table');

$res = [
    'rows' => $data,
    "pagination" => [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
    "others" => [],
    'constants' => [
            'text'     =>  FIELD_TEXT,
            'database' => FIELD_DATABASE,
            'constant' => FIELD_CONSTANT,
            'time' => FIELD_TIME
    ],
];

$res['others']['fie_type'] = [];
foreach ($fie_type as $k => $v) {
    $res['others']['fie_type'][] = [
        "value" => $k,
        "label" => $v
    ];
}

$res['others']['list_table'] = [];
foreach ($list_table as $k => $v) {
    $res['others']['list_table'][] = [
        "value" => $k,
        "label" => $v
    ];
}


if(getValue('json')) {
    CommonService::resJson($res);
}


Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-system-field-list', 'admin');
