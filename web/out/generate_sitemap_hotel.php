<?
include('../Core/Config/require_web.php');

/**
 * Generate sitemap hotel, chia theo SITEMAP_PAGE_SIZE
 */

$data   =   $DB->query("SELECT hot_id, hot_type, hot_active, hot_name, hot_last_update, hot_picture, hot_picture_group
                        FROM hotel
                        ORDER BY hot_id DESC")
                        ->toArray();

$content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

$i  =   0;
$page   =   1;
$year   =   date('Y');

foreach ($data as $row) {
    $i++;
    
    //Ko index các hotel deactive
    if ($row['hot_active'] == 1) {
    
        $hotel_name =   $cfg_hotel_type[$row['hot_type']] . ' ' . $row['hot_name'];
        $hotel_name =   str_replace('&', '', $hotel_name);
        $hotel_name =   str_replace('  ', ' ', $hotel_name);
        
        $content    .=  '<url>' . PHP_EOL;
        $content    .=  '<loc>' . $Router->detailHotel($row) . '</loc>' . PHP_EOL . '<lastmod>' . date('Y-m-d', $row['hot_last_update']) .'</lastmod>' . PHP_EOL . '<changefreq>daily</changefreq>' . PHP_EOL . '<priority>1.0</priority>' . PHP_EOL;
        
        //Lấy ảnh
        if ($row['hot_picture_group'] == 1) {
            $images =   $HotelModel->getHotelImageGroup($row['hot_id']);
        } else {
            $images =   $HotelModel->getHotelImage($row['hot_id']);
        }

        foreach ($images as $img) {
            $content    .=  '<image:image>' . PHP_EOL;
            $content    .=  '<image:loc>' . $img . '</image:loc>' . PHP_EOL;
            $content    .=  '<image:title>Ảnh ' . $hotel_name . '</image:title>' . PHP_EOL;
            $content    .=  '<image:caption>Thông tin hình ảnh ' . $hotel_name . ' mới nhất ' . $year . '</image:caption>' . PHP_EOL;
            $content    .=  '</image:image>' . PHP_EOL;
        }
        
        $content    .=  '<PageMap xmlns="http://www.google.com/schemas/sitemap-pagemap/1.0">
                            <DataObject type="thumbnail">
                            <Attribute name="name" value="' . $hotel_name . '"/>
                            <Attribute name="src" value="' . $Router->srcHotel($row['hot_id'], $row['hot_picture']) . '"/>
                            </DataObject>
                        </PageMap>';
        
        $content    .=  '</url>' . PHP_EOL;
    }
    /** Nếu hết 1 page (SITEMAP_PAGE_SIZE) thì tạo file XML và tách thành page mới **/
    if ($i == SITEMAP_PAGE_SIZE) {

        //Đóng thẻ và tạo file XML
        $content    .=  '</urlset>' . PHP_EOL;
        create_file('/media/sitemap/sitemap_hotel_' . $page . '.xml', $content);
        
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
    create_file('/media/sitemap/sitemap_hotel_' . $page . '.xml', $content);
}
?>