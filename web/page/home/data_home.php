<?php
use src\Models\Hotel;
use src\Models\HotelPicture;
use src\Services\HotelService;
use src\Facades\DB;

include('Core/Config/require_web.php');

$Layout->setTitle($cfg_website['con_meta_title'])
        ->setDescription($cfg_website['con_meta_description'])
        ->setKeywords($cfg_website['con_meta_keyword'])
        ->setImages(['src' => $cfg_default_image, 'alt' => $cfg_website['con_meta_title']])
        ->setCanonical(DOMAIN_WEB)
        ->setJS(['page.home']);

$hotels = Hotel::where('hot_active', 1)
    ->select('cit_name', 'cit_id', 'cit_image', 'hot_id', 'hot_name', 'hot_address_full', 'hot_picture')
    ->join('cities', 'hot_city', 'cit_id')
    ->toArray();

$data_hotels = [];
foreach ($hotels as $hotel) {
    $img = isset($hotel['hot_picture']) ? $Router->srcHotel($hotel['hot_id'], $hotel['hot_picture']) : $cfg_default_image;

    $slug = to_slug($hotel['hot_name']);
    
    // Lấy dịch vụ của khách sạn
    $attrs = $AttributeModel->getAttributeOfId($hotel['hot_id'], GROUP_HOTEL);
    $services = [];
    foreach ($attrs as $attr) {
        if ($attr['info']['param'] == 'tien-nghi') {
            $services = array_values($attr['data']);
            break;
        }
    }

    $data_hotels[] = [
        'name' => $hotel['hot_name'],
        'city' => $hotel['cit_name'],
        'address' => $hotel['hot_address_full'],
        'link' => '/hotel-' . $hotel['hot_id'] . '-' . $slug . '.html',
        'img'  => $img,
        'price' => format_number(HotelService::getRoomPriceByMonth($hotel['hot_id'])),
        'services' => $services  
    ];
}

// Lấy danh sách thành phố duy nhất từ $hotels
$cities = [];
$city_ids = [];
foreach ($hotels as $hotel) {
    if (!in_array($hotel['cit_id'], $city_ids)) {
        $city_ids[] = $hotel['cit_id'];
        // Tạo URL đầy đủ cho cit_image
        $city_image = isset($hotel['cit_image']) 
            ? '/theme/images/city/' . $hotel['cit_image'] 
            : $cfg_default_image;
        $cities[] = [
            'cit_id' => $hotel['cit_id'],
            'cit_name' => $hotel['cit_name'],
            'cit_image' => $city_image,
        ];
    }
}

// Lấy 3 bài viết mới nhất từ bảng document
$news = DB::pass()->query("
    SELECT doc_id, doc_name, doc_img, doc_slug, created_at 
    FROM document 
    WHERE doc_hot = 1 
    ORDER BY created_at DESC 
    LIMIT 3
")->toArray();

$news_list = [];
foreach ($news as $item) {
    $news_list[] = [
        'title' => $item['doc_name'],
        // Thêm DOMAIN_WEB vào trước đường dẫn ảnh
        'image' => $item['doc_img'] ? $Router->srcDocument($item['doc_img'] ) : $cfg_default_image,
        'link'  => '/document/' . $item['doc_slug'] . '-' . $item['doc_id'] . '.html',
        'created' => date('d/m/Y', strtotime($item['created_at']))
    ];
}


// Truyền ra view
$news_list = array_values($news_list);
$data_hotels = array_values($data_hotels);
?>