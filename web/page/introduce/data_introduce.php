<?php
use src\Models\Hotel;
use src\Models\HotelPicture;

$hotels = Hotel::where('hot_active', 1)
    ->select('cit_name', 'cit_id', 'cit_image', 'hot_id', 'hot_name', 'hot_address_full', 'hot_picture', 'hot_phone', 'hot_intro')
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
        'phone' => $hotel['hot_phone'] ?? '', 
        'intro' => $hotel['hot_intro'] ?? 'Khách sạn cao cấp tại ' . $hotel['cit_name'], 
        'link' => '/hotel-' . $hotel['hot_id'] . '-' . $slug . '.html',
        'img'  => $img,
        'price' => format_number(123456),
        'services' => $services  
    ];
}
$data_hotels = array_values($data_hotels);
?>