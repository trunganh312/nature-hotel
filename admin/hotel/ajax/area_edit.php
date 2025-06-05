<?php

use src\Facades\DB;
use src\Services\CommonService;

include(__DIR__ . '/../config_module.php');

// Check quyền
$Auth->checkPermission('area_edit');

/** --- Khai báo biến --- **/
$table = 'area';
$are_id = getValue('id', GET_INT, GET_GET, 0);
$are_name = getValue('are_name', GET_STRING, GET_POST, '');
$errors = [];

/** --- Xử lý dữ liệu đầu vào --- **/
if (empty($are_name)) {
    $errors[] = 'Tên khu vực không được để trống';
}
if ($are_id <= 0) {
    $errors[] = 'ID khu vực không hợp lệ';
}

// Kiểm tra lỗi
if (!empty($errors)) {
    CommonService::resJson(['success' => 0, 'data' => $errors]);
}

// Chuẩn bị dữ liệu cập nhật
$data = [
    'are_name' => $are_name
];



try {
    // Cập nhật bản ghi
    $affected_rows = DB::pass()->update($table, $data, ['are_id' => $are_id]);
    if ($affected_rows > 0) {

        CommonService::resJson(['success' => 1, 'data' => 'Cập nhật khu vực thành công']);
    } else {
        $errors[] = 'Không có thay đổi nào được thực hiện';

        CommonService::resJson(['success' => 0, 'data' => $errors]);
    }
} catch (Exception $e) {
    $errors[] = 'Lỗi khi cập nhật khu vực: ' . $e->getMessage();

    CommonService::resJson(['success' => 0, 'data' => $errors]);
}