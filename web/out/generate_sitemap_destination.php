<?
include('../Core/Config/require_web.php');

/**
 * Generate sitemap destination, chia theo SITEMAP_PAGE_SIZE
 */

$data   =   $DB->query("SELECT des_id, des_name, des_image, des_active, des_last_update
                        FROM destination
                        ORDER BY des_id DESC")
                        ->toArray();

$content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

$i  =   0;
$page   =   1;
$year   =   date('Y');

//Tách thành 2 cụm content: Detail các Destination và Page Tour đi đến các Destination
$str_detail = $str_tour = '';

foreach ($data as $row) {
    $i++;
    
    //Ko index các destination deactive
    if ($row['des_active'] == 1) {
    
        $row['des_name']    =   str_replace('&', '', $row['des_name']);
        $row['des_name']    =   str_replace('  ', ' ', $row['des_name']);
        
        //Sitemap cho destination detail page
        $str_detail    .=  '<url>' . PHP_EOL;
        $str_detail    .=  '<loc>' . $Router->detailDestination($row) . '</loc>' . PHP_EOL . '<lastmod>' . date('Y-m-d', $row['des_last_update']) .'</lastmod>' . PHP_EOL . '<changefreq>daily</changefreq>' . PHP_EOL . '<priority>0.8</priority>' . PHP_EOL;
        
        //Lấy ảnh
        $images =   $PlaceModel->getDestinationImage($row['des_id'], 'dei_image');
        foreach ($images as $img) {
            $str_detail    .=  '<image:image>' . PHP_EOL;
            $str_detail    .=  '<image:loc>' . $Router->srcDestination($img['dei_image']) . '</image:loc>' . PHP_EOL;
            $str_detail    .=  '<image:title>Ảnh ' . $row['des_name'] . '</image:title>' . PHP_EOL;
            $str_detail    .=  '<image:caption>Thông tin hình ảnh ' . $row['des_name'] . ' mới nhất ' . $year . '</image:caption>' . PHP_EOL;
            $str_detail    .=  '</image:image>' . PHP_EOL;
        }
        
        $str_detail    .=  '<PageMap xmlns="http://www.google.com/schemas/sitemap-pagemap/1.0">
                            <DataObject type="thumbnail">
                            <Attribute name="name" value="' . $row['des_name'] . '"/>
                            <Attribute name="src" value="' . $Router->srcDestination($row['des_image']) . '"/>
                            </DataObject>
                        </PageMap>';
                        
        $str_detail    .=  '</url>' . PHP_EOL;
        
        //Sitemap cho page list tour destination
        $str_tour   .=  '<url><loc>' . $Router->listTourDestination($row) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>0.8</priority></url>' . PHP_EOL;
    }
    /** Nếu hết 1 page (SITEMAP_PAGE_SIZE) thì tạo file XML và tách thành page mới **/
    if ($i == SITEMAP_PAGE_SIZE) {

        //Đóng thẻ và tạo file XML cho page này
        
        //Ghép 2 đoạn sitemap của destination vào
        $content    .=  $str_detail . $str_tour;
        
        $content    .=  '</urlset>' . PHP_EOL;
        create_file('/media/sitemap/sitemap_destination_' . $page . '.xml', $content);
        
        //Reset content
        $str_detail =   '';
        $str_tour   =   '';
        $i  =   0;
        $page++;
        $content    =   
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

    }
}

/** Nếu hết vòng for mà chưa đạt đến SITEMAP_PAGE_SIZE (tức là page cuối cùng) thì tạo file cho page cuối **/
if ($i > 0 && $i < SITEMAP_PAGE_SIZE) {
        
    //Ghép 2 đoạn sitemap của destination vào
    $content    .=  $str_detail . $str_tour;
    
    $content    .=  '</urlset>' . PHP_EOL;
    create_file('/media/sitemap/sitemap_destination_' . $page . '.xml', $content);
}
?>