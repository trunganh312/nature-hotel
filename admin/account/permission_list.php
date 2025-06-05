<?

use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

//Admin hay User
$env    =   getValue('type', GET_STRING, GET_GET, '');
switch ($env) {
    case 'admin':
        //Check quyền
        $Auth->checkPermission('account_permission_list_admin');
        //Alias menu
        $Auth->setAliasMenu('account_permission_list_admin');

        $page_title =   'Danh sách các quyền quản trị CRM';
        $table      =   'admins_permission';
        break;

    case 'user':
        //Check quyền
        $Auth->checkPermission('account_permission_list_user');
        //Alias menu
        $Auth->setAliasMenu('account_permission_list_user');

        $page_title =   'Danh sách các quyền người dùng';
        $table      =   'users_permission';
        break;
    
    default:
        exitError('Bạn không có quyền sử dụng tính năng này');
        break;
}

/** --- Khai báo một số biến cơ bản --- **/
$field_id   =   'per_id';
$group_id   =   getValue('mod_group');
$per_module_id  =   $Model->getListData('modules', 'mod_id, mod_name', $group_id > 0 ? 'mod_group = ' . $group_id : '', 'mod_name');
$mod_group  =   $cfg_module_group_name;
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('mod_group', 'Nhóm', TAB_SELECT, true)
        ->column('per_module_id', 'Module', TAB_SELECT, true)
        ->column('per_name', 'Tên quyền', TAB_TEXT, true, true)
        ->column('per_description', 'Mô tả', TAB_TEXT);
if(getValue('pageSize')) {
    $Table->setPageSize(getValue('pageSize'));
}
//Mảng chứa các param gửi kèm thêm của cột checkbox
$arr_checkbox_param =   [];

if ($Auth->cto) {
    $Table->column('per_alias', 'Alias', TAB_TEXT, true, true)
            ->column('per_feature_id', 'Menu tính năng', TAB_SELECT);
    
    //Nếu là Admin thì ko có cơ cấu tổ chức theo nhiều cty nên các quyền sẽ có luôn trường check sở hữu dữ liệu và có cho phép leader xử lý dữ liệu của nhân viên hay ko
    if ($env == 'admin') {
        $Table->column('per_check_owner', 'Owner', TAB_CHECKBOX, false, true)
                ->column('per_allow_leader', 'Leader', TAB_CHECKBOX, false, true);
        $arr_checkbox_param =   [
            'per_check_owner'   =>  ['type' => $env],
            'per_allow_leader'  =>  ['type' => $env]
        ];
    } else {
        //Còn của User thì những quyền cần check sở hữu sẽ theo cấu hình của mỗi cty
        $Table->column('per_company_config', 'Config', TAB_CHECKBOX, false, true);
        $arr_checkbox_param =   [
            'per_company_config'    =>  ['type' => $env]
        ];
    }
            
    $Table->column('per_active', 'Act', TAB_CHECKBOX, false, true)
            ->addED(true)
            ->setEditFileName('permission_edit.php?type=' . $env)
            ->setEditThickbox(['width' => 800, 'height' => 500, 'title' => 'Sửa thông tin quyền']);
}
$arr_checkbox_param['per_active']   =   ['type' => $env];

$Table->setFieldHidden(['type'])
    ->setCheckboxParamMore($arr_checkbox_param);

//Lấy riêng 1 mảng chứa các Quyền mà có gán với tính năng để show trong bảng
$Table->addSQL("SELECT $table.*, mod_group
                FROM $table
                INNER JOIN modules ON per_module_id = mod_id
                ORDER BY mod_env, mod_group, per_alias");
$data   =   $DB->query($Table->sql_table)->toArray();
$per_feature_id =   $Model->getListData('modules_file', 'modf_id, modf_name', "modf_file <> ''");
// Lặp qua trả về đúng kiểu dữ liệu
$data = array_map(function($item) use ($mod_group, $per_module_id, $per_feature_id) {
    $item['mod_group_text']     = array_get($mod_group, $item['mod_group']);
    $item['per_module_text']    = array_get($per_module_id, $item['per_module_id']);
    $item['per_feature_text']   = array_get($per_feature_id, $item['per_feature_id']);
    $item['per_feature_id']     = $item['per_feature_id'] > 1 ? $item['per_feature_id'] : null;
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
        'super' => $Auth->cto,
        'env'   => $env == 'user'
    ],
];

$res['others']['mod_group'] = [];
foreach ($mod_group as $k => $v) {
    $res['others']['mod_group'][] = [
        "value" => $k,
        "label" => $v
    ];
}

$array_module   =   $Model->getListData('modules', 'mod_id, mod_name', ' mod_env = ' . ($env == 'admin' ? ADMIN_CRM : ADMIN_USER), 'mod_name');

$res['others']['modules'] = [];
foreach ($array_module as $k => $v) {
    $res['others']['modules'][] = [
        "value" => $k,
        "label" => $v
    ];
}


$res['others']['features'] = [];
foreach ($per_feature_id as $k => $v) {
    // Bỏ đi value = 1
    if ($k == 1) continue;
    $res['others']['features'][] = [
        "value" => $k,
        "label" => $v
    ];
}


if(getValue('json')) {
    CommonService::resJson($res);
}

Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-account-permission-list', 'admin');    