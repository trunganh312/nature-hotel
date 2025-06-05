<?

use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

//Check quyền
$Auth->checkPermission('account_admin_group_list');
//Alias menu
$Auth->setAliasMenu('account_admin_group_list');

$page_title =   'Danh sách các nhóm quyền Admin';
$table      =   'admins_group';
$has_edit   =   $Auth->hasPermission('account_admin_group_edit');

/** --- Khai báo một số biến cơ bản --- **/
$field_id   =   'gro_id';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('gro_name', 'Tên nhóm quyền', TAB_TEXT, true)
        ->column('gro_note', 'Mô tả nhóm', TAB_TEXT);
if ($has_edit) {
    $Table->column('gro_no_level', 'Không giới hạn quyền', TAB_CHECKBOX)
            ->column('gro_active', 'Act', TAB_CHECKBOX)
            ->addED(true)
            ->setEditFileName('group_edit.php')
            ->setEditThickbox(['width' => 600, 'height' => 300, 'title' => 'Sửa thông tin nhóm quyền']);
}
$Table->addSQL();

$data   =   $DB->query($Table->sql_table)->toArray();

// Lặp qua trả về đúng kiểu dữ liệu
$data = array_map(function($item) use ($Router, $Auth) {
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
    "others" => [
    ],
    'permissions' => [
        'edit'          => $has_edit,
        'permission'    => $Auth->hasPermission('account_admin_set_permission'),
        'create'        => $Auth->hasPermission('account_admin_group_create')
    ],
];


if(getValue('json')) {
    CommonService::resJson($res);
}


Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-account-group-list', 'admin');
