<?php

use src\Facades\DB;
use src\Services\CommonService;

include(__DIR__ . '/../config_module.php');

// Check quyền
$Auth->checkPermission('area_create');

/** --- Khai báo biến --- **/
$table = 'area';
$errors = [];

/** --- Xử lý dữ liệu đầu vào --- **/
$are_name = getValue('are_name', GET_STRING, GET_POST, '');
if (empty($are_name)) {
    $errors[] = 'Tên khu vực không được để trống';
}

// Kiểm tra lỗi
if (!empty($errors)) {
    CommonService::resJson(['success' => 0, 'data' => $errors]);
}

// Chuẩn bị dữ liệu chèn
$data = [
    'are_name' => $are_name,
    'are_active' => 0,
    'are_country' => null, // Cho phép NULL nếu không có quốc gia
    'are_hot' => 0,
    'are_order' => 0
];


try {
    // Thực hiện chèn
    $inserted_id = DB::pass()->insert($table, $data);
    // Ghi log kết quả

    CommonService::resJson(['success' => 1, 'data' => 'Thêm khu vực thành công']);
} catch (Exception $e) {
    $errors[] = 'Lỗi khi thêm khu vực: ' . $e->getMessage();

    CommonService::resJson(['success' => 0, 'data' => $errors]);
}