<?php
// File: C:\xampp\htdocs\nature-hotel\web\data_list.php

// Kiểm tra nếu được gọi trực tiếp qua fetch
if (php_sapi_name() === 'cli' || defined('STDIN')) {
    exit;
}

// Bao gồm file cấu hình
include('../../../Core/Config/require_web.php');
use src\Models\Hotel;
use src\Models\HotelPicture;

// Lấy tham số city từ request
$id = getValue('city');

// Lấy tất cả khách sạn
$all_hotels = Hotel::where('hot_active', 1)
    ->select('hot_id', 'hot_city', 'hot_name', 'hot_phone', 'hot_email', 'cit_name', 'cit_id', 'cit_image')
    ->join('cities', 'hot_city', 'cit_id')
    ->toArray();

// Tạo danh sách thành phố ($data_city)
$data_city = [];
foreach ($all_hotels as $hotel) {
    $name = $hotel['cit_name'];
    if (!isset($data_city[$name])) {
        $slug = to_slug($name);
        $data_city[$name] = [
            'name' => $name,
            'value' => 1,
            'link' => '/city-' . $hotel['cit_id'] . '-' . $slug . '.html',
            'img' => $hotel['cit_image']
        ];
    } else {
        $data_city[$name]['value']++;
    }
}
$data_city = array_values($data_city);

// Tạo danh sách thành phố ($cities) cho bộ lọc
$cities = [];
foreach ($all_hotels as $hotel) {
    $cid = $hotel['hot_city'];
    $cname = array_get($cfg_city, $cid);
    $slug = to_slug($cname);
    if ($cid && $cname) {
        $cities[$cid] = ['city_id' => $cid, 'city_name' => $cname, 'city_link' => '/city-' . $cid . '-' . $slug . '.html'];
    }
}
$cities = array_values($cities);

// Lấy filter tags từ URL
$tags = getValue('tags', 'str', 'GET', '');
$selected_tags = array_filter(explode(',', $tags));

// Lọc danh sách khách sạn theo tỉnh nếu có
if ($id) {
    $hotels = Hotel::where('hot_active', 1)
        ->where('hot_city', $id)
        ->select('hot_id', 'hot_city', 'hot_name', 'hot_phone', 'hot_email')
        ->toArray();
} else {
    $hotels = $all_hotels;
}

// Lặp qua từng khách sạn để lấy tiện nghi
foreach ($hotels as &$hotel) {
    $attrs = $AttributeModel->getAttributeOfId($hotel['hot_id'], GROUP_HOTEL);
    $hotel['utilities'] = [];
    $images = HotelPicture::where('hopi_hotel_id', $hotel['hot_id'])->toArray();
    $hotel['images'] = [];
    $hotel['link'] = '/hotel-' . $hotel['hot_id'] . '-' . to_slug($hotel['hot_name']) . '.html';
    foreach ($images as $image) {
        $hotel['images'][] = $Router->srcHotel($hotel['hot_id'], $image['hopi_picture']);
    }
    foreach ($attrs as $attr) {
        if ($attr['info']['param'] == 'tien-nghi') {
            $hotel['utilities'] = array_values($attr['data']);
            break;
        }
    }
}
unset($hotel);

// Lọc theo tags nếu có
if (!empty($selected_tags)) {
    $hotels = array_filter($hotels, function($hotel) use ($selected_tags) {
        if (empty($hotel['utilities'])) return false;
        $hotel_tag_ids = array_column($hotel['utilities'], 'value');
        return count(array_intersect($selected_tags, $hotel_tag_ids)) > 0;
    });
    $hotels = array_values($hotels);
}

// Lấy danh sách tiện nghi duy nhất từ tất cả khách sạn đang hoạt động để filter
$amenity_map = [];
foreach ($all_hotels as $hotel) {
    $attrs = $AttributeModel->getAttributeOfId($hotel['hot_id'], GROUP_HOTEL);
    foreach ($attrs as $attr) {
        if ($attr['info']['param'] == 'tien-nghi') {
            foreach ($attr['data'] as $item) {
                $value = $item['value'];
                $name = $item['name'];
                if ($value && $name) {
                    $amenity_map[$value] = $name;
                }
            }
        }
    }
}
$amenities = [];
foreach ($amenity_map as $value => $name) {
    $amenities[] = ['tag_id' => $value, 'tag_name' => $name];
}


?>