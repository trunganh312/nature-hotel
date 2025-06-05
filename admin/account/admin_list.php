<?

use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');
if (!$Auth->envAdmin()) {
    exit('Bạn không có quyền sử dụng tính năng này!');
}

//Check quyền
$Auth->checkPermission('account_admin_admin_list');
//Alias menu
$Auth->setAliasMenu('account_admin_admin_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Danh sách tài khoản quản trị CRM';
$table      =   'admins';
$field_id   =   'adm_id';
$has_edit   =   $Auth->hasPermission('account_admin_admin_edit');
$has_view_log   =   $Auth->hasPermission('account_admin_view_log');

/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
if ($Auth->isSuperAccount()) $Table->column('adm_id', 'ID');
$Table->column('adm_avatar', 'Avatar', TAB_IMAGE)
        ->column('adm_email', 'Email', TAB_TEXT, true)
        ->column('adm_name', 'Họ tên', TAB_TEXT, true)
        ->column('adm_phone', 'Điện thoại', TAB_TEXT, true)
        ->column('adm_time_create', 'Ngày tạo', TAB_DATE, false, true)
        ->column('adm_last_online', 'Last Online', TAB_DATE, false, true)
        ->column('adm_group', 'Nhóm quyền', TAB_SELECT)
        ->column('adm_department', 'Phòng/Ban', TAB_SELECT)
        ->setPathImage(DOMAIN_STATIC . '/image/admin/small/');
        
if ($has_edit) {
    $Table->column('adm_active', 'Act', TAB_CHECKBOX, false, true)
            ->setEditThickbox(['width' => 800, 'height' => 600, 'title' => 'Sửa thông tin tài khoản'])
            ->setEditFileName('admin_edit.php')
            ->addED(true);
}
if(getValue('pageSize')) {
    $Table->setPageSize(getValue('pageSize'));
}    

//Khai báo 2 biến array để fill vào form tìm kiếm
$LevelGroup         =   new DataLevelModel('admins_group');
$LevelDepartment    =   new DataLevelModel('admins_department');
$adm_group      =   $LevelGroup->getAllLevelStep();
$adm_department =   $LevelDepartment->getAllLevelStep();

$Table->addSearchData([
    'group'         =>  ['label' => 'Nhóm quyền', 'type' => TAB_SELECT, 'query' => false],
    'department'    =>  ['label' => 'Phòng/Ban', 'type' => TAB_SELECT, 'query' => false]
]);

$sql_filter = $sql_join = "";

//Tìm kiếm theo Nhóm quyền
$group_id   =   getValue('group');
if ($group_id > 0) {
    $sql_join   .=  "INNER JOIN admins_group_admins ON adm_id = grac_account_id";
    $sql_filter .=  " AND grac_group_id IN(" . implode(',' . $LevelGroup->getAllChild($group_id, true, 'id'));
}

//Tìm kiếm theo Phòng ban
$department_id  =   getValue('department');
if ($department_id > 0) {
    $sql_join   .=  "INNER JOIN departments_admins ON adm_id = deac_account_id";
    $sql_filter .=  " AND deac_department_id IN(" . implode(',' . $LevelDepartment->getAllChild($department_id, true, 'id'));
}

$Table->addSQL("SELECT *
                FROM admins
                $sql_join
                WHERE adm_cto <> 1 $sql_filter");

/** Tách riêng 1 câu query lấy ra group/department của các account để ko bị query trong vòng lặp **/
$arr_admin_group    =   [];
$data   =   $DB->query("SELECT grac_account_id, gro_name
                        FROM admins_group_admins
                        INNER JOIN admins_group ON gro_id = grac_group_id
                        ORDER BY grac_account_id")
                        ->toArray();
foreach ($data as $row) {
    //Nếu chưa có trong mảng thì gán group đầu tiên
    if (!isset($arr_admin_group[$row['grac_account_id']])) {
        $arr_admin_group[$row['grac_account_id']]  =   $row['gro_name'];
    } else {
        //Nếu có trong mảng rồi thì nối thêm group tiếp theo
        $arr_admin_group[$row['grac_account_id']]  .=  ', ' . $row['gro_name'];
    }
}

$arr_admin_department   =   [];
$data   =   $DB->query("SELECT deac_account_id, dep_name
                        FROM admins_department
                        INNER JOIN admins_department_admins ON dep_id = deac_department_id
                        ORDER BY deac_account_id")
                        ->toArray();
foreach ($data as $row) {
    //Nếu chưa có trong mảng thì gán group đầu tiên
    if (!isset($arr_admin_department[$row['deac_account_id']])) {
        $arr_admin_department[$row['deac_account_id']]    =   $row['dep_name'];
    } else {
        //Nếu có trong mảng rồi thì nối thêm group tiếp theo
        $arr_admin_department[$row['deac_account_id']]    .=  ', ' . $row['dep_name'];
    }
}

$table_log   =   DB::pass()->query("SELECT talo_id FROM table_log WHERE talo_table = '" . $table . "'")->getOne();

$data   =   $DB->query($Table->sql_table)->toArray();

// Lặp qua trả về đúng kiểu dữ liệu
$data = array_map(function($item) use ($Auth, $arr_admin_group, $arr_admin_department) {
    if (isset($arr_admin_group[$item['adm_id']])) {
        $item['adm_group']     = $arr_admin_group[$item['adm_id']];
    };
    if (isset($arr_admin_department[$item['adm_id']])) {
        $item['adm_department']     = $arr_admin_department[$item['adm_id']];
    };
    $list_current_group     =   $Auth->getListIDGroupOfAccount($item['adm_id'], true);
    $item['group']          =   $list_current_group;
    $item['adm_time_create']    = $item['adm_time_create'] > 0 ? date("d/m/Y", $item['adm_time_create']) : null;
    $item['adm_last_online']    = $item['adm_last_login'] > 0 ? date("d/m/Y H:i:s", $item['adm_last_online']) : null;
    $item['link_fake_login']    = 'login_by_cto.php?id=' . $item['adm_id'] . '&token=' . $Auth->genToken($item['adm_random']) . '" target="_blank">' . $item['adm_email']; 
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
        'talo_id' => $table_log['talo_id'] ?? null,
    ],
    'permissions' => [
        'hasEdit' => $has_edit,
        'super'   => $Auth->cto
    ],
];

$list_group_admin   =   $Model->getListData('admins_group', 'gro_id, gro_name', '', 'gro_name');
$res['others']['list_group'] = [];
foreach ($list_group_admin as $k => $v) {
    $res['others']['list_group'][] = [
        "value" => $k,
        "label" => $v
    ];
}


if(getValue('json')) {
    CommonService::resJson($res);
}


Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-account-admin-list', 'admin');
