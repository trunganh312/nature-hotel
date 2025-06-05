<?
include('../Core/Config/require_web.php');

/**
 * Generate file sitemap collection
 */

//Đường dẫn lưu các file sitemap con;
$path_sitemap   =   DOMAIN_WEB . '/media/sitemap/';

$content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

//Lấy các collection
$data   =   $DB->query("SELECT col_id, col_name, col_last_update
                        FROM collection
                        WHERE col_active = 1
                        ORDER BY col_last_update DESC")
                        ->toArray();
foreach ($data as $row) {
    $content    .=  '<url><loc>' . $Router->detailCollection($row) . '</loc><lastmod>' . date('Y-m-d', $row['col_last_update']) .'</lastmod><changefreq>daily</changefreq><priority>0.8</priority></url>' . PHP_EOL;
}

$content    .=  '</urlset>';
//Tạo file sitemap riêng cho mục bài viết, do có ít bài viết nên gộp chung vào 1 file
create_file('/media/sitemap/sitemap_collection.xml', $content);
?>