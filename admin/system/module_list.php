<?

use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

//Check quyền
$Auth->checkPermission('system_module_list');
//Alias menu
$Auth->setAliasMenu('system_module_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Danh sách Module';
$table      =   'modules';
$field_id   =   'mod_id';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('mod_name', 'Tên module', TAB_TEXT, true)
        ->column('mod_folder', 'Folder', TAB_TEXT, true)
        ->column('file', 'Tính năng của module', TAB_TEXT)
        ->column('mod_icon', 'Icon', TAB_TEXT)
        ->column('mod_order', 'Thứ tự', TAB_NUMBER, false, true)
        ->column('mod_active', 'Active', TAB_CHECKBOX, false, true)
        ->addED(true)
        ->setEditFileName('module_edit.php')
        ->setEditThickbox(['width' => 800, 'height' => 400, 'title' => 'Sửa thông tin module']);

$Table->addSQL("SELECT * FROM " . $table . " ORDER BY mod_env, mod_group, mod_order");

$data   =   $DB->query($Table->sql_table)->toArray();

// Lặp qua trả về đúng kiểu dữ liệu
$data = array_map(function($item) use($field_id) {

    $menu   =   DB::pass()->query("SELECT *
                                        FROM modules_file
                                        WHERE modf_module_id = " . $item[$field_id] . "
                                        ORDER BY modf_order")
                                        ->toArray();
    $total_menu         =   count($menu);
    $item['total_menu'] = $total_menu;
    $item['menu']       = $menu;

    return $item;
}, $data);

$res = [
    "rows" => $data,
    "pagination" => [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
    "params" => [],
    "others" => [],
    'permissions' => [
    ],
];


$res['others']['mod_group'] = [];
foreach ($cfg_module_group_name as $k => $v) {
    $res['others']['mod_group'][] = [
        "value" => $k,
        "label" => $v
    ];
}

if(getValue('json')) {
    CommonService::resJson($res);
}

Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-system-module-list', 'admin');
