<?php

use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;

include('config_module.php');

// Check quyền
$Auth->checkPermission('general_city_list');
// Alias Menu
$Auth->setAliasMenu('general_city_list');

// Nhận dữ liệu từ body của POST
$input = json_decode(file_get_contents('php://input'), true);

// Xử lý yêu cầu lấy danh sách thành phố cho select
if (isset($input['action']) && $input['action'] === 'getCities') {
    $cities = DB::pass()->query("SELECT cit_id, cit_name FROM cities ORDER BY cit_name")->toArray();
    if (empty($cities)) {
        $cities = [];
    }
    // Chuyển đổi định dạng cho a-select
    $cityOptions = array_map(function ($city) {
        return [
            'value' => $city['cit_id'],
            'label' => $city['cit_name'],
        ];
    }, $cities);
    
    error_log("Cities data: " . json_encode($cityOptions));
    CommonService::resJson([
        'success' => 1,
        'data' => $cityOptions, // Trả về định dạng {value, label}
    ]);
    exit;
}

// Xử lý yêu cầu lấy dữ liệu bảng
if (isset($input['action']) && $input['action'] === 'getData') {
    $cit_id = isset($input['cit_id']) ? (int)$input['cit_id'] : null;
    $sql = "SELECT * FROM cities";
    if ($cit_id !== null) {
        $cit_id_safe = addslashes($cit_id);
        $sql .= " WHERE cit_id = '$cit_id_safe'";
    }
    $sql .= " ORDER BY cit_id";

    $rows = DB::pass()->query($sql)->toArray();
    if (empty($rows)) {
        $rows = [];
    }

    $rows = array_map(function ($item) {
        global $Router;
        $item['cit_image_url'] = $Router->srcCity($item['cit_image']);
        return $item;
    }, $rows);

    CommonService::resJson([
        'success' => 1,
        'data' => [
            'rows' => $rows,
        ],
    ]);
    exit;
}

// Dữ liệu mặc định cho giao diện (nếu không có POST)
$sql = "SELECT * FROM cities ORDER BY cit_id";
$rows = DB::pass()->query($sql)->toArray();
if (empty($rows)) {
    $rows = [];
}

$rows = array_map(function ($item) {
    global $Router;
    $item['cit_image_url'] = $Router->srcCity($item['cit_image']);
    return $item;
}, $rows);

// Lấy danh sách thành phố cho bộ lọc (tương tự district.php)
$cityOptions = DB::pass()->query("SELECT cit_id, cit_name FROM cities ORDER BY cit_name")->toArray();
$cityOptions = array_map(function ($city) {
    return [
        'value' => $city['cit_id'],
        'label' => $city['cit_name'],
    ];
}, $cityOptions);

$res = [
    'rows' => $rows,
    'cities' => $cityOptions, // Thêm danh sách thành phố vào dữ liệu mặc định
    'permissions' => [
        'hasEdit' => $Auth->hasPermission('general_city_edit'),
        'hasCreate' => $Auth->hasPermission('general_city_create'),
    ],
];

Vue::setData($res);
Vue::setTitle('Danh sách thành phố');
Vue::render('crm-general-city', 'admin');