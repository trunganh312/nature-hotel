<?

use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

//Check quyền
$Auth->checkPermission('system_table_log_list');
//Alias menu
$Auth->setAliasMenu('system_table_log_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Danh sách các bảng cần lưu log';
$table      =   'table_log';
$field_id   =   'talo_id';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('talo_table', 'Tên bảng', TAB_TEXT, true)
        ->column('talo_note', 'Ghi chú', TAB_TEXT)
        ->addED(true)
        ->setEditFileName('table_log_edit.php')
        ->setEditThickbox(['width' => 600, 'height' => 300, 'title' => 'Sửa thông tin bảng lưu log']);

$Table->addSQL("SELECT * FROM table_log ORDER BY talo_table");


$data = DB::pass()->query($Table->sql_table)->toArray();

$res = [
    'rows' => $data,
    "pagination" => [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
];


if(getValue('json')) {
    CommonService::resJson($res);
}


Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-system-table-list', 'admin');
