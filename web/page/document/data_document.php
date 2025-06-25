<?php

use src\Models\Document; 
use src\Facades\DB;

if (!isset($path_root)) {
    $path_root = $_SERVER['DOCUMENT_ROOT'] . '/';
}

include_once($path_root . 'Core/Config/require_web.php');


$doc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Nếu không có thì lấy từ URL bằng regex
if ($doc_id == 0) {
    $slug_id = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '.html');
    if (preg_match('/-(\d+)$/', $slug_id, $matches)) {
        $doc_id = intval($matches[1]);
    }
}

// Kiểm tra doc_id hợp lệ
if ($doc_id <= 0) {
    header("Location: /404.php");
    exit;
}

// Lấy dữ liệu chi tiết document
$document = DB::query("SELECT * FROM document WHERE doc_id = $doc_id LIMIT 1")->getOne();
if (!$document) {
    header("Location: /404.php");
    exit;
}

// Lấy 10 bài viết khác (trừ bài hiện tại)
$other_documents = Document::where('doc_id', '!=', $doc_id)
    ->where('doc_active', 1)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->select('doc_id', 'doc_name', 'doc_slug', 'created_at')
    ->toArray();

