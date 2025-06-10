<?
include('../../../Core/Config/require_web.php');
use src\Models\Hotel;
use src\Models\HotelPicture;
use src\Services\HotelService;

$id = getValue('city');

$all_hotels = Hotel::where('hot_active', 1)->toArray();
$cities = [];
foreach ($all_hotels as $hotel) {
    $cid = $hotel['hot_city'];
    $cname = array_get($cfg_city, $cid);
    $slug = to_slug($cname);
    if ($cid && $cname) {
        $cities[$cid] = ['city_id' => $cid, 'city_name' => $cname, 'city_link' => '/city-' . $cid . '-' . $slug . '.html'];
    }
}
$cities = array_values($cities); // Đảm bảo là mảng tuần tự cho JS

// Lấy filter tags từ URL
$tags = getValue('tags', 'str', 'GET', ''); // VD: "1,2,3"
$selected_tags = array_filter(explode(',', $tags));

// Sau đó mới lọc danh sách khách sạn theo tỉnh nếu có
if ($id) {
    $hotels = Hotel::where('hot_active', 1)
        ->where('hot_city', $id)
        ->select('hot_id', 'hot_city', 'hot_name', 'hot_phone', 'hot_email', 'hot_address_full')
        ->toArray();
} else {
    $hotels = $all_hotels;
}

// Lặp qua từng khách sạn để lấy tiện nghi và dịch vụ
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
    $hotel['price'] = format_number(HotelService::getRoomPriceByMonth($hotel['hot_id']));
}
unset($hotel);

// Lọc tiếp theo tags nếu có
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
                $icon = $item['icon'];
                if ($value && $name) {
                    $amenity_map[$value] = [
                        'name' => $name,
                        'icon' => $icon
                    ];
                }
            }
        }
    }
}
$amenities = [];
foreach ($amenity_map as $value => $name) {
    $amenities[] = ['tag_id' => $value, 'tag_name' => $name['name'], 'tag_icon' => $name['icon']];
}


?>