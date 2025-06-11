<?

use src\Models\Hotel;
use src\Models\Room;

include('../../../Core/Config/require_web.php');


$hotel_id   = getValue('hotel_id', GET_INT, GET_SESSION, 5189);
$rooms      = getValue('rooms', GET_ARRAY, GET_SESSION, [2, 3]);

// Lấy thông tin khách sạn
$hotel_info = Hotel::where(['hot_id' => $hotel_id, 'hot_active' => STATUS_ACTIVE])->getOne();

if(empty($hotel_info)) {
    dump('Không tìm thấy khách sạn');
    exit;
}
$image_hotel = isset($hotel_info['hot_picture']) ? $Router->srcHotel($hotel_id, $hotel_info['hot_picture']) : $cfg_default_image;
$booking_info = [
    'checkin_date' => '2025-06-10',
    'checkout_date' => '2025-06-11',
    'adult_qty' => 2,
    'child_qty' => 1,
    'baby_qty' => 0,
];

// Lấy thông tin hạng phòng đặt
$room_book = [];
foreach($rooms as $room_id) {
    $room_info = Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();
    // Lấy view
    $image = isset($room_info['roo_picture']) ? $Router->srcRoom($room_id, $room_info['roo_picture']) : $cfg_default_image;
    $room_info['image'] = $image;
    $room_info['view'] = $HotelModel->showRoomView($room_info, true);
    $room_info['bed'] = $HotelModel->showRoomBed($room_info, false);
    $room_book[] = $room_info;
}






