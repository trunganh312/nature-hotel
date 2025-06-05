<?
include('../Core/Config/require_web.php');

/**
 * Generate sitemap tour, chia theo SITEMAP_PAGE_SIZE
 */

$data   =   $DB->query("SELECT tou_id, tou_name, tou_active, tou_last_update, tou_image, tou_group
                        FROM tour
                        ORDER BY tou_id DESC")
                        ->toArray();

$content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

$i  =   0;
$page   =   1;
$year   =   date('Y');

foreach ($data as $row) {
    $i++;
    
    //Ko index các tour deactive
    if ($row['tou_active'] == 1) {
    
        $row['tou_name']    =   str_replace('&', '', $row['tou_name']);
        $row['tou_name']    =   str_replace('  ', ' ', $row['tou_name']);
        
        $content    .=  '<url>' . PHP_EOL;
        $content    .=  '<loc>' . $Router->detailTour($row) . '</loc>' . PHP_EOL . '<lastmod>' . date('Y-m-d', $row['tou_last_update']) .'</lastmod>' . PHP_EOL . '<changefreq>daily</changefreq>' . PHP_EOL . '<priority>1.0</priority>' . PHP_EOL;
        
        //Lấy ảnh
        $images =   $TourModel->getTourImage($row['tou_id'], 'toui_image');
        foreach ($images as $img) {
            $content    .=  '<image:image>' . PHP_EOL;
            $content    .=  '<image:loc>' . $Router->srcTour($row['tou_id'], $img['toui_image']) . '</image:loc>' . PHP_EOL;
            $content    .=  '<image:title>Ảnh ' . $row['tou_name'] . '</image:title>' . PHP_EOL;
            $content    .=  '<image:caption>Thông tin hình ảnh ' . $row['tou_name'] . ' mới nhất ' . $year . '</image:caption>' . PHP_EOL;
            $content    .=  '</image:image>' . PHP_EOL;
        }
        
        $content    .=  '<PageMap xmlns="http://www.google.com/schemas/sitemap-pagemap/1.0">
                            <DataObject type="thumbnail">
                            <Attribute name="name" value="' . $row['tou_name'] . '"/>
                            <Attribute name="src" value="' . $Router->srcTour($row['tou_id'], $row['tou_image']) . '"/>
                            </DataObject>
                        </PageMap>';
                        
        $content    .=  '</url>' . PHP_EOL;
    }
    /** Nếu hết 1 page (SITEMAP_PAGE_SIZE) thì tạo file XML và tách thành page mới **/
    if ($i == SITEMAP_PAGE_SIZE) {
        
        //Đóng thẻ và tạo file XML
        $content    .=  '</urlset>' . PHP_EOL;
        create_file('/media/sitemap/sitemap_tour_' . $page . '.xml', $content);
        
        //Reset content
        $i  =   0;
        $page++;
        $content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

    }
}

/** Nếu hết vòng for mà chưa đạt đến SITEMAP_PAGE_SIZE (tức là page cuối cùng) thì tạo file cho page cuối **/
if ($i > 0 && $i < SITEMAP_PAGE_SIZE) {
    $content    .=  '</urlset>' . PHP_EOL;
    create_file('/media/sitemap/sitemap_tour_' . $page . '.xml', $content);
}
?>