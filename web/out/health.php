<?php
// Đo thời gian bắt đầu
$start_time = microtime(true);

ob_start();
mb_internal_encoding('UTF-8');
error_reporting(E_ALL);

$path_root      =   $_SERVER['DOCUMENT_ROOT'] . '/';
$path_core      =   $path_root . '../Vietgoing_Core/';
$path_config    =   $path_root . 'Core/';

require_once($path_core . '/Env/ConfigEnv.php');
include_once($path_core . 'Config/Constant.php');
include_once($path_config . 'Config/constant_web.php');
require_once($path_core . 'Function/Function.php');
require_once($path_core . 'Classes/Database.php');

// Kiểm tra kết nối db
$DB = new Database;

$db_online = $DB->connectDB();

// Xóa đi các data in trước đó để tránh lỗi json
ob_clean();

// Thiết lập header để phản hồi dạng JSON
header('Content-Type: application/json');

echo json_encode([
    "db_online"=> $db_online ? 1 : 0,
    "execution_time_ms" => round((microtime(true) - $start_time) * 1000, 2) 
]);