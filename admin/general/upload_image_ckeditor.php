<?php

include('config_module.php');
require_once(PATH_CORE . '/Classes/Upload.php');
require_once(PATH_CORE . '/Classes/Image.php');
require_once(PATH_CORE . '/Classes/Router.php');
$Router         =   new Router;

$path_picture_hotel = PATH_ROOT . '/static/image/document/';

// Nếu chưa có thư mục thì tạo mới
if (!is_dir($path_picture_hotel)) {
    mkdir($path_picture_hotel, 0777, true);
}

// Khởi tạo Upload class
$Upload = new Upload('upload', $path_picture_hotel, 1, 1);
$filename = $Upload->new_name;

// Kiểm tra lỗi upload
if (!empty($Upload->error)) {
    echo json_encode([
        'uploaded' => 0,
        'error' => [
            'message' => 'Lỗi khi tải ảnh lên: ' . $Upload->error
        ]
    ]);
    exit;
}

// Kiểm tra xem file đã được upload chưa
if ($filename && file_exists($path_picture_hotel . $filename)) {
    // Trả về đường dẫn ảnh cho CKEditor
    echo json_encode([
        'uploaded' => 1,
        'fileName' => $filename,
        'url' => $Router->srcDocument($filename)
    ]);
} else {
    echo json_encode([
        'uploaded' => 0,
        'error' => [
            'message' => 'Tải ảnh lên thất bại. Vui lòng thử lại!'
        ]
    ]);
}
exit;
