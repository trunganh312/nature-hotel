<?php

use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;
use src\Models\City;

include('config_module.php');

// Kiểm tra quyền truy cập
$Auth->checkPermission('general_district_list');
// Thiết lập alias menu
$Auth->setAliasMenu('general_district_list');

/** --- Khai báo một số biến cơ bản --- **/
$table          =   "districts";
$field_id       =   "dis_id";
$page_title     =   'Danh sách quận huyện';
$has_edit       =   $Auth->hasPermission('general_district_edit');
$has_create     =   $Auth->hasPermission('general_district_create');
/** --- End of Khai báo một số biến cơ bản --- **/

// Nhận dữ liệu từ body của POST (cho AJAX request)
$input = json_decode(file_get_contents('php://input'), true);

// Xử lý yêu cầu lấy danh sách quận/huyện theo thành phố
if (isset($input['action']) && $input['action'] === 'getDistrictsByCity') {
    $cityId = isset($input['cityId']) ? (int)$input['cityId'] : null;
    if ($cityId === null) {
        CommonService::resJson([
            'success' => 0,
            'message' => 'Thiếu thông tin thành phố',
        ]);
        exit;
    }

    $sql = "SELECT dis_id, dis_name FROM districts WHERE dis_city = '$cityId' ORDER BY dis_name";
    $districts = DB::pass()->query($sql)->toArray();
    if (empty($districts)) {
        $districts = [];
    }

    CommonService::resJson([
        'success' => 1,
        'data' => [
            'districts' => $districts,
        ]
    ]);
    exit;
}

// Xử lý yêu cầu lấy dữ liệu bảng với bộ lọc
if (isset($input['action']) && $input['action'] === 'getData') {
    $dis_city = isset($input['dis_city']) ? (int)$input['dis_city'] : null;
    $dis_id = isset($input['dis_id']) ? (int)$input['dis_id'] : null;
    $dis_name = isset($input['dis_name']) ? trim($input['dis_name']) : null;

    $sql = "SELECT d.*, c.cit_name 
            FROM districts d 
            LEFT JOIN cities c ON d.dis_city = c.cit_id 
            WHERE 1=1";
    
    if ($dis_city !== null) {
        $dis_city_safe = addslashes($dis_city);
        $sql .= " AND d.dis_city = '$dis_city_safe'";
    }
    if ($dis_id !== null) {
        $dis_id_safe = addslashes($dis_id);
        $sql .= " AND d.dis_id = '$dis_id_safe'";
    }
    if ($dis_name !== null && $dis_name !== '') {
        $dis_name_safe = addslashes($dis_name);
        $sql .= " AND (d.dis_name LIKE '%$dis_name_safe%' OR d.dis_name_other LIKE '%$dis_name_safe%')";
    }
    $sql .= " ORDER BY d.dis_id";

    $rows = DB::pass()->query($sql)->toArray();
    if (empty($rows)) {
        $rows = [];
    }

    $rows = array_map(function ($item) {
        global $Router;
        $item['dis_image_url'] = $Router->srcDistrict($item['dis_image']);
        return $item;
    }, $rows);

    // Lấy danh sách thành phố
    $list_city = City::orderBy('cit_name', 'ASC')->pluck('cit_id', 'cit_name');
    $cities = [];
    foreach ($list_city as $k => $v) {
        $cities[] = [
            "value" => $k,
            "label" => $v
        ];
    }

    CommonService::resJson([
        'success' => 1,
        'data' => [
            'rows' => $rows,
            'cities' => $cities,
            'pagination' => [
                'total' => count($rows), // Có thể cải tiến bằng cách dùng COUNT trong SQL
                'current' => 1,
                'pageSize' => count($rows)
            ]
        ]
    ]);
    exit;
}

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('dis_name', 'Tên', TAB_TEXT, true)
      ->column('dis_name_other', 'Tên khác', TAB_TEXT, true)
      ->column('dis_image', 'Ảnh', TAB_TEXT, true)
      ->column('dis_active', 'Active', TAB_TEXT, true)
      ->column('dis_hot', 'Hot', TAB_TEXT, true);

if (getValue('pageSize')) {
    $Table->setPageSize(getValue('pageSize'));
}

$Table->addFieldMultiSearch([
    'dis_name' => ['dis_name', 'dis_name_other'],
]);

// Join bảng districts với cities để lấy cit_name
$Table->addSQL("SELECT d.*, c.cit_name 
                FROM districts d 
                LEFT JOIN cities c ON d.dis_city = c.cit_id 
                ORDER BY d.dis_id");

// Lấy dữ liệu từ bảng districts
$rows = DB::pass()->query($Table->sql_table)->toArray();

$rows = array_map(function ($item) {
    global $Router;
    $item['dis_image_url'] = $Router->srcDistrict($item['dis_image']);
    return $item;
}, $rows);

// Lấy danh sách thành phố
$list_city = City::orderBy('cit_name', 'ASC')->pluck('cit_id', 'cit_name');
$cities = [];
foreach ($list_city as $k => $v) {
    $cities[] = [
        "value" => $k,
        "label" => $v
    ];
}

$res = [
    'rows' => $rows,
    'permissions' => [
        'hasEdit' => $has_edit,
        'hasCreate' => $has_create,
    ],
    'cities' => $cities,
    "pagination" => [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
];

if (getValue('json')) {
    CommonService::resJson($res);
}

Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-general-district', 'admin');
