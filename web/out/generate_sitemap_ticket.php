<?
include('../Core/Config/require_web.php');

/**
 * Generate file sitemap ticket
 */

//Đường dẫn lưu các file sitemap con;
$path_sitemap   =   DOMAIN_WEB . '/media/sitemap/';

$content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

//Lấy các ticket
$data   =   $DB->query("SELECT tic_id, tic_name, tic_last_update
                        FROM ticket
                        WHERE tic_active = 1
                        ORDER BY tic_last_update DESC")
                        ->toArray();
foreach ($data as $row) {
    $content    .=  '<url><loc>' . $Router->detailTicket($row) . '</loc><lastmod>' . date('Y-m-d', $row['tic_last_update']) .'</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
}

$content    .=  '</urlset>';
//Tạo file sitemap
create_file('/media/sitemap/sitemap_ticket.xml', $content);
?>