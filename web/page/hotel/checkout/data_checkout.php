<?

use src\Models\Hotel;
use src\Models\Room;

include('../../../Core/Config/require_web.php');

// Lấy thông tin từ session
$booking_data = getValue('booking_data', GET_ARRAY, GET_SESSION, []);
$rooms = $booking_data['selectedRooms'];
$checkIn = $booking_data['checkIn'];
$checkOut = $booking_data['checkOut'];
$hotel_id = $booking_data['hotel_id'];
dump($rooms);

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
foreach($rooms as $roomType) {
    $room_id = $roomType['roomId'];
    $room_name = $roomType['roomName'];
    $room_count = $roomType['roomCount'];
    $room_price = $roomType['roomPrice'];
    $room_guests = $roomType['rooms'];

    $room_info = Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();
    // Lấy view
    $image = isset($room_info['roo_picture']) ? $Router->srcRoom($room_id, $room_info['roo_picture']) : $cfg_default_image;
    $room_info['image'] = $image;
    $room_info['view'] = $HotelModel->showRoomView($room_info, true);
    $room_info['bed'] = $HotelModel->showRoomBed($room_info, false);

    // Duyệt từng phòng đã đặt
    foreach($room_guests as $guest) {
        $adults = $guest['adults'];
        $children = $guest['children'];
        $infants = $guest['infants'];
    }

    $room_book[] = $room_info;
}






