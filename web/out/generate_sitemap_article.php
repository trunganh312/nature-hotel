<?
include('../Core/Config/require_web.php');

/**
 * Generate file sitemap bài viết
 */

//Đường dẫn lưu các file sitemap con;
$path_sitemap   =   DOMAIN_WEB . '/media/sitemap/';

$content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

//Lấy hết các category của bài viết
$data   =   $DB->query("SELECT cat_id, cat_name
                        FROM category
                        WHERE cat_group = " . GROUP_ARTICLE . " AND cat_active = 1
                        ORDER BY cat_id")
                        ->toArray();

foreach ($data as $row) {
    $content    .=  '<url><loc>' . $Router->listArticleCate($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>0.6</priority></url>' . PHP_EOL;
}

//Lấy các bài viết
$data   =   $DB->query("SELECT art_id, art_title, art_last_update
                        FROM article
                        WHERE art_active = 1
                        ORDER BY art_last_update DESC")
                        ->toArray();
foreach ($data as $row) {
    $content    .=  '<url><loc>' . $Router->detailArticle($row) . '</loc><lastmod>' . date('Y-m-d', $row['art_last_update']) .'</lastmod><changefreq>daily</changefreq><priority>0.5</priority></url>' . PHP_EOL;
}

$content    .=  '</urlset>';
//Tạo file sitemap riêng cho mục bài viết, do có ít bài viết nên gộp chung vào 1 file
create_file('/media/sitemap/sitemap_article.xml', $content);
?>