<?php

$requestUri = $_SERVER['REQUEST_URI'];

$_SERVER['DOCUMENT_ROOT'] = rtrim(__DIR__, '/server_dev');
if (empty($requestUri) || $requestUri == '/' || preg_match('/^\/\?/', $requestUri, $matches)) {
    // Thay đổi thư mục làm base path
    chdir($_SERVER['DOCUMENT_ROOT']);
    include 'index.php';
    exit;
}

$file_static = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'];
if (file_exists($file_static)) {

    // Quy tắc viết lại URL cho /page/{group}/detail/detail.php?id={id}
    if (preg_match('/.+\.(php)/', $file_static, $matches)) {
        // Thay đổi thư mục làm base path
        chdir(preg_replace("/\/[\w\d]*\.php/", '', $file_static));
        include $file_static;
        exit;
    }
    
    $fp = fopen($file_static, 'rb');
    
    // send the right headers
    $mime_type = mime_content_type($file_static);
    if (preg_match('/.+\.(css)/', $file_static, $matches)) {
        $mime_type = 'text/css';
    } else if (preg_match('/.+\.(js)/', $file_static, $matches)) {
        $mime_type = 'text/js';
    } else if (preg_match('/.+\.(woff2)/', $file_static, $matches)) {
        $mime_type = 'font/woff2';
    } else if (preg_match('/.+\.(js)/', $file_static, $matches)) {
        $mime_type = 'font/woff2';
    }
    header("Content-Type: ". $mime_type);
    header("Content-Length: " . filesize($file_static));

    // dump the picture and stop the script
    fpassthru($fp);
    fclose($fp);
    exit;
}

// Quy tắc viết lại URL cho /page/{group}/list/list.php?group={group}&id={id}
if (preg_match('/^\/([a-z]*)\/([a-z]*)\-([0-9]*)\-([^\/]*)\.html/', $requestUri, $matches)) {
    $_GET['group'] = $matches[2];
    $_GET['id'] = $matches[3];
    // Thay đổi thư mục làm base path
    chdir($_SERVER['DOCUMENT_ROOT'] .'/page/'. $matches[1] .'/list');
    include 'list.php';
    exit;
}

// Quy tắc viết lại URL cho /page/{group}/detail/detail.php?id={id}
if (preg_match('/^\/([a-z]*)\/([0-9]*)\-([^\/]*)\.html/', $requestUri, $matches)) {
    $_GET['id'] = $matches[2];
    // Thay đổi thư mục làm base path
    chdir($_SERVER['DOCUMENT_ROOT'] .'/page/'. $matches[1] .'/detail');
    include 'detail.php';
    exit;
}

// Quy tắc viết lại URL cho /page/{group}/checkout/checkout.php
if (preg_match('/^\/checkout\/([a-z]*)\//', $requestUri, $matches)) {
    // Thay đổi thư mục làm base path
    chdir($_SERVER['DOCUMENT_ROOT'] .'/page/'. $matches[1] .'/checkout');
    include 'checkout.php';
    exit;
}

// Quy tắc viết lại URL cho /page/{group}/thanks/thanks.php
if (preg_match('/^\/thanks\/([a-z]*)\//', $requestUri, $matches)) {
    // Thay đổi thư mục làm base path
    chdir($_SERVER['DOCUMENT_ROOT'] .'/page/'. $matches[1] .'/thanks');
    include 'thanks.php';
    exit;
}

// Quy tắc viết lại URL cho /page/{group}/list/list.php
if (preg_match('/^\/([a-z]*)\//', $requestUri, $matches)) {
    // Thay đổi thư mục làm base path
    chdir($_SERVER['DOCUMENT_ROOT'] .'/page/'. $matches[1] .'/list');
    include 'list.php';
    exit;
}

// Xử lý các trường hợp còn lại ở đây
echo '404 Not Found';