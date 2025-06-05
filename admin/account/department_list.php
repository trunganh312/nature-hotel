<?

use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

//Check quyền
$Auth->checkPermission('account_admin_department_list');
//Alias menu
$Auth->setAliasMenu('account_admin_department_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Quản lý phòng ban';
$field_id   =   'dep_id';
$table      =   'admins_department';
$has_edit   =   $Auth->hasPermission('account_admin_department_edit');
$has_manage_member  =   $Auth->hasPermission('account_admin_department_member_list');
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('dep_avatar', 'Avatar', TAB_IMAGE)
        ->column('dep_name', 'Tên Phòng/Ban', TAB_TEXT, true)
        ->column('dep_parent_id', 'Cấp trên', TAB_SELECT, true)
        ->column('manager', 'Người quản lý', TAB_TEXT)
        ->column('dep_description', 'Giới thiệu', TAB_TEXT)
        ->column('member', 'Thành viên')
        ->setPathImage(DOMAIN_STATIC . '/image/department/small/');
if ($has_edit) {
    $Table->column('dep_active', 'Act', TAB_CHECKBOX, false, true)
            ->addED(true)
            ->setEditFileName('department_edit.php')
            ->setEditThickbox(['width' => 800, 'height' => 600, 'title' => 'Sửa thông tin phòng ban']);
}
$Table->addSQL();

$data   =   $DB->query($Table->sql_table)->toArray();

foreach ($data as $row) {
    $data[$row['dep_id']] =   $row;
}

//Lấy Data theo level để show theo cấp bậc nhìn cho trực quan
$DataLevel  =   new DataLevelModel($table);
$data_level =   $DataLevel->getAllLevelStep();


$stt    =   0;
$result = [];
foreach ($data_level as $id => $name) {
    $row = $data[$id];
    $stt++;
    // Xử lý thông tin quản lý
    if ($row['dep_manager_id'] > 0) {
        $account = $DB->query("SELECT adm_name, adm_email FROM admins WHERE adm_id = " . $row['dep_manager_id'])->getOne();
        $row['manager_info'] = $account['adm_name'] . ' - ' . $account['adm_email'];
    } else {
        $row['manager_info'] = null;
    }

    // Tính tổng thành viên
    $total_member   =   $DB->count("SELECT COUNT(deac_account_id) AS total FROM admins_department_admins WHERE deac_department_id = $id");

    // Thêm vào kết quả
    $result[] = [
        'stt'=> $stt,
        'id' => $id,
        'dep_name' => $name,
        'parent_id' => $row['dep_parent_id'],
        'dep_parent_name' => $row['dep_parent_id'] > 0 ? $data_level[$row['dep_parent_id']] : null,
        'manager' => $row['manager_info'],
        'dep_description' => $row['dep_description'],
        'total_member' => $total_member,
        'dep_active' => $row['dep_active'] == 1 ? true : false,
        'dep_avatar' => isset($row['dep_avatar']) ? DOMAIN_STATIC . "/image/department/small/" . $row['dep_avatar'] : '',
        'href' =>  'member.php?department=' . urlencode($id) . '&' . param_thickbox()
    ];
}

$res = [
    "rows"=>$result,
    "pagination"=> [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
    "params"=> [],
    "others"=> [
        'dep_account_create' =>  $Auth->id,
    ],
    'DOMAIN_STATIC'     => DOMAIN_STATIC,
    'hasEdit'           => $has_edit,
    'hasManageMember'   => $has_manage_member,
    'hasCreate'         => $Auth->hasPermission('account_admin_department_create')
];


$res['others']['departments_type'] = [];
foreach ($cfg_department_type as $k => $v) {
    $res['others']['departments_type'][] = [
        "value"=> $k,
        "label"=> $v
    ];
}

if (getValue('json')) {
    CommonService::resJson($res);
}

$res['others']['list_department'] = [];
foreach ($data_level as $k => $v) {
     $res['others']['list_department'][] = [
          "value"=> $k,
          "label"=> $v
     ];
}

Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-account-department-list', 'admin');
