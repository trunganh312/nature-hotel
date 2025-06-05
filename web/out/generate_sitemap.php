<?
include('../Core/Config/require_web.php');

/**
 * Generate file sitemap tổng chứa các link đến các file sitemap của từng module
 */

//Đường dẫn lưu các file sitemap con;
$path_sitemap   =   DOMAIN_WEB . '/media/sitemap/';

$content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

/** Sitemap hotel **/
//Đầu tiên là lưu sitemap của các KS/Tour/Destination theo Tỉnh/TP, Quận/Huyện
$content    .=  '<sitemap><loc>' . $path_sitemap . 'sitemap_city.xml</loc><lastmod>' . date('Y-m-d') .'</lastmod></sitemap>' . PHP_EOL;

//Tiếp theo là lấy ra tất cả các KS, phân trang theo SITEMAP_PAGE_SIZE
//Count tất cả ko loại ra deactive đi vì muốn giữ Item nào đã ở file sitemap nào thì sẽ cố định ở file đó,
//nếu chỉ lấy active thì nếu một số tour bị deactive thì các tour khác sẽ nhảy sang file khác
$total_record   =   $DB->count("SELECT COUNT(hot_id) AS total FROM hotel");
$total_page     =   ceil($total_record / SITEMAP_PAGE_SIZE);

for ($page = 1; $page <= $total_page; $page++) {
    $content    .=  '<sitemap><loc>' . $path_sitemap . 'sitemap_hotel_' . $page . '.xml</loc><lastmod>' . date('Y-m-d') .'</lastmod></sitemap>' . PHP_EOL;
}

/** Sitemap tour **/

//Tiếp theo là lấy ra tất cả các tour, phân trang theo SITEMAP_PAGE_SIZE
//Count tất cả ko loại ra deactive đi vì muốn giữ Item nào đã ở file sitemap nào thì sẽ cố định ở file đó,
//nếu chỉ lấy active thì nếu một số tour bị deactive thì các tour khác sẽ nhảy sang file khác
$total_record   =   $DB->count("SELECT COUNT(tou_id) AS total FROM tour");
$total_page     =   ceil($total_record / SITEMAP_PAGE_SIZE);

for ($page = 1; $page <= $total_page; $page++) {
    $content    .=  '<sitemap><loc>' . $path_sitemap . 'sitemap_tour_' . $page . '.xml</loc><lastmod>' . date('Y-m-d') .'</lastmod></sitemap>' . PHP_EOL;
}

/** Sitemap Destination **/ 
//Tiếp theo là file sitemap của các destination chia theo SITEMAP_PAGE_SIZE
$total_record   =   $DB->count("SELECT COUNT(des_id) AS total FROM destination");
$total_page     =   ceil($total_record / SITEMAP_PAGE_SIZE);

for ($page = 1; $page <= $total_page; $page++) {
    $content    .=  '<sitemap><loc>' . $path_sitemap . 'sitemap_destination_' . $page . '.xml</loc><lastmod>' . date('Y-m-d') .'</lastmod></sitemap>' . PHP_EOL;
}

/** Sitemap Collection **/ 
$content    .=  '<sitemap><loc>' . $path_sitemap . 'sitemap_collection.xml</loc><lastmod>' . date('Y-m-d') .'</lastmod></sitemap>' . PHP_EOL;

/** Sitemap Article **/ 
$content    .=  '<sitemap><loc>' . $path_sitemap . 'sitemap_article.xml</loc><lastmod>' . date('Y-m-d') .'</lastmod></sitemap>' . PHP_EOL;

/** Sitemap Ticket **/ 
$content    .=  '<sitemap><loc>' . $path_sitemap . 'sitemap_ticket.xml</loc><lastmod>' . date('Y-m-d') .'</lastmod></sitemap>' . PHP_EOL;

//Đóng thẻ
$content    .=  '</sitemapindex>';

/** Tạo file sitemap tổng **/
create_file('/sitemap.xml', $content);


/**
 * List các trang Hotel/Tour/Destination theo Tỉnh/TP có ít nên cho chạy luôn ở đây
 * Cho tất cả các URL liên quan đến Tỉnh/TP, Quận/Huyện của các module vào chung 1 file 
 */

//Content
$content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

//Trang chủ
$content    .=  '<url><loc>' . DOMAIN_WEB . '</loc><lastmod>' . date('Y-m-d') .'</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;

//Tỉnh/TP
//Cho các list hotel/tour/destination liền nhau
$str_hotel = $str_tour = $str_dest = $str_collection = '';
$cities =   $DB->query("SELECT cit_id, cit_name FROM city WHERE cit_active = 1")->toArray();
foreach ($cities as $row) {
    $str_hotel      .=  '<url><loc>' . $Router->listHotelCity($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
    $str_tour       .=  '<url><loc>' . $Router->listTourCity($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
    $str_dest       .=  '<url><loc>' . $Router->listDestinationCity($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
    $str_collection .=  '<url><loc>' . $Router->listCollectionCity($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
}
$content    .=  $str_hotel . $str_tour . $str_dest . $str_collection;

//Quận/Huyện
//Cho các list hotel/tour/destination liền nhau
$str_hotel = $str_tour = $str_dest = $str_collection = '';
$data   =   $DB->query("SELECT dis_id, dis_name FROM district WHERE dis_active = 1 ORDER BY dis_city, dis_name")->toArray();
foreach ($data as $row) {
    $str_hotel      .=  '<url><loc>' . $Router->listHotelDistrict($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
    $str_tour       .=  '<url><loc>' . $Router->listTourDistrict($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
    $str_dest       .=  '<url><loc>' . $Router->listDestinationDistrict($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
    $str_collection .=  '<url><loc>' . $Router->listCollectionDistrict($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
}
$content    .=  $str_hotel . $str_tour . $str_dest . $str_collection;

/** URL của phần vé/combo **/
$content    .=  '<url><loc>' . DOMAIN_WEB . '/ticket/</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;
$content    .=  '<url><loc>' . DOMAIN_WEB . '/combo/</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>' . PHP_EOL;

$content    .=  '</urlset>';
//Tạo file
create_file('/media/sitemap/sitemap_city.xml', $content);

?>