<?php
use src\Services\CommonService;

include('config_module.php');

// Kiểm tra quyền truy cập
$Auth->checkPermission('application_create');

// Chỉ chấp nhận phương thức GET
if (!CommonService::isGet()) {
    CommonService::resJson(['error' => 'Phương thức không được hỗ trợ'], STATUS_INACTIVE, 'Error!');
}

$action = getValue('action', GET_STRING, GET_GET, '');

if ($action === 'generate_app_id') {
    // Tạo app_app_id: 19 chữ số, đảm bảo không trùng
    do {
        $app_app_id = '';
        for ($i = 0; $i < 19; $i++) {
            $app_app_id .= mt_rand(0, 9);
        }
        // Kiểm tra trùng lặp trong bảng application
        $check = $DB->query("SELECT app_id FROM application WHERE app_app_id = '$app_app_id'")->getOne();
    } while (!empty($check));

    CommonService::resJson(['app_app_id' => $app_app_id]);
} elseif ($action === 'generate_app_secret') {
    // Tạo app_app_secret: 21 ký tự chữ hoa, chữ thường, số
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $app_app_secret = '';
    for ($i = 0; $i < 21; $i++) {
        $app_app_secret .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    CommonService::resJson(['app_app_secret' => $app_app_secret]);
} else {
    CommonService::resJson(['error' => 'Hành động không hợp lệ'], STATUS_INACTIVE, 'Error!');
}
?>