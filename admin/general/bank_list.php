<?

use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

//Check quyền
$Auth->checkPermission('general_bank_list');
//Alias Menu
$Auth->setAliasMenu('general_bank_list');

/** --- Khai báo một số biến cơ bản --- **/
$table          =   "banks";
$field_id       =   "bak_id";
$page_title     =   'Danh sách ngân hàng';
$has_edit       =   $Auth->hasPermission('general_attribute_edit');
$has_create     =   $Auth->hasPermission('general_attribute_create');
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('bak_name', 'Tên ngân hàng', TAB_TEXT, true)
        ->column('bak_name_en', 'Tên ngân hàng (EN)', TAB_TEXT, true)
        ->column('bak_abbreviation', 'Tên viết tắt', TAB_TEXT, true)
        ->column('bak_bin', 'BIN', TAB_TEXT, true);

if(getValue('pageSize')) {
    $Table->setPageSize(getValue('pageSize'));
}

$Table->addFieldMultiSearch([
        'bak_name' => ['bak_name', 'bak_name_en', 'bak_abbreviation', 'bak_bin'],
]);
        
$Table->addSQL("SELECT * FROM banks ORDER BY bak_id");

$rows = DB::pass()->query($Table->sql_table)->toArray();


$res = [
    'rows' => $rows,
    'permissions' => [
        'hasEdit' => $has_edit,
        'hasCreate' => $has_create,
    ],
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
Vue::render('crm-general-bank-list', 'admin');
