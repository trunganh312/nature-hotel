<?

use src\Models\Hotel;
use src\Models\HotelPicture;
use src\Models\Room;
use src\Models\RoomPicture;
use src\Services\HotelService;

include('../../../Core/Config/require_web.php');

$id = getValue('id');

// Hàm chuyển đổi từ dd/mm/yyyy sang DateTime object
function parseDate($dateStr) {
    return DateTime::createFromFormat('d/m/Y', $dateStr);
}

// Lấy dữ liệu từ cookies nếu có
$cookie_checkin = $_COOKIE['search_checkin'] ?? null;
$cookie_checkout = $_COOKIE['search_checkout'] ?? null;

if ($cookie_checkin && $cookie_checkout) {
    $checkin_date = parseDate($cookie_checkin);
    $checkout_date = parseDate($cookie_checkout);
    
    if ($checkin_date && $checkout_date) {
        $check_in = $checkin_date->format('d/m/Y');
        $check_out = $checkout_date->format('d/m/Y');
        $nights = $checkout_date->diff($checkin_date)->days;
    }
} else {
    $check_in = isset($check_in) ? htmlspecialchars($check_in) : date('d/m/Y');
    $check_out = isset($check_out) ? htmlspecialchars($check_out) : date('d/m/Y', strtotime('+1 day'));
    $nights = isset($nights) ? $nights : 1;
}
$daterange = $check_in . ' - ' . $check_out;

// lấy thông tin khách sạn
$hotel_detail = Hotel::where('hot_id', $id)->getOne();

// Lấy ảnh KS
$hotel_picture = HotelPicture::where('hopi_hotel_id', $id)->toArray();
$images = [];

foreach ($hotel_picture as $picture) {
    // Lấy 5 ảnh đầu k theo id và các ảnh sau
    $images[] = $Router->srcHotel($id, $picture['hopi_picture']);
}
$image_1 = $images[0] ?? $cfg_default_image;
$image_2 = $images[1] ?? $cfg_default_image;
$image_3 = $images[2] ?? $cfg_default_image;
$image_4 = $images[3] ?? $cfg_default_image;
$image_5 = $images[4] ?? $cfg_default_image;

// Lấy danh sách hạng phòng
$rooms = Room::where('roo_hotel_id', $id)->toArray();
$rooms = array_map(function($room) use($Router, $cfg_default_image, $AttributeModel, $HotelModel, $daterange) {
    $room_picture = RoomPicture::where('rop_room_id', $room['roo_id'])->toArray();
    $room_images = [];
    foreach ($room_picture as $picture) {
        $room_images[] = isset($picture['rop_picture']) ? $Router->srcRoom($room['roo_id'], $picture['rop_picture']) : $cfg_default_image;
    }
    $room['images'] = $room_images;
    $room['attrs'] = $AttributeModel->getAttributeOfId($room['roo_id'], GROUP_ROOM);
    $room['utilities'] = [];
    foreach ($room['attrs'] as $attr) {
        $room['utilities'] = array_merge($room['utilities'], array_values($attr['data']));
    }
    $room['attr_view']        = $HotelModel->showRoomView($room, true);
    $room['attr_bed']         = $HotelModel->showRoomBed($room, false);
    $info = HotelService::getRoomPriceAndAvailability($room['roo_hotel_id'], $daterange, $room['roo_id']);
    $room['price']            = format_number($info['total_price']);
    $room['min_qty']          = $info['min_qty'];
    return $room;
}, $rooms);

$hotel_attribute    =   $AttributeModel->getAttributeOfId($hotel_detail['hot_id'], GROUP_HOTEL);

?>