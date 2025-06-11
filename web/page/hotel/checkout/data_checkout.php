<?

use src\Models\Hotel;
use src\Models\Room;

include('../../../Core/Config/require_web.php');
require_once('../../../libraries/payos/vendor/autoload.php');
include('../../../libraries/payos/src/PayOSWrapper.php');
use Lib\PayOS\PayOSWrapper;
$payOS = new PayOSWrapper(
    PAYOS_CLIENT_ID,
    PAYOS_API_KEY, 
    PAYOS_CHECKSUM_KEY
);

// Lấy thông tin từ session
$booking_data = getValue('booking_data', GET_ARRAY, GET_SESSION, []);
$rooms = $booking_data['selectedRooms'];
$checkIn = $booking_data['checkIn'];
$checkOut = $booking_data['checkOut'];
$hotel_id = $booking_data['hotel_id'];

$nights = (str_totime($checkOut) - str_totime($checkIn)) / 86400;

// Lấy thông tin khách sạn
$hotel_info = Hotel::where(['hot_id' => $hotel_id, 'hot_active' => STATUS_ACTIVE])->getOne();

if(empty($hotel_info)) {
    dump('Không tìm thấy khách sạn');
    exit;
}
$image_hotel = isset($hotel_info['hot_picture']) ? $Router->srcHotel($hotel_id, $hotel_info['hot_picture']) : $cfg_default_image;

$roomTypeGuests = []; // Mảng lưu tổng số người từng hạng phòng

$total_adult = 0;
$total_child = 0;
$total_infant = 0;
$total_room = 0;
$total_price = 0;
$total_discount = 0;
foreach($rooms as $roomType) {
    $adult = 0;
    $child = 0;
    $infant = 0;
    $room_id = (int) $roomType['roomId'];
    foreach($roomType['rooms'] as $room) {
        $adult += $room['adults'];
        $child += $room['children'];
        $infant += $room['infants'];
    }
    $total_adult += $adult;
    $total_child += $child;
    $total_infant += $infant;
    $total_room += $roomType['roomCount'];
    $room_info = Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();
    // Lấy view
    $image = isset($room_info['roo_picture']) ? $Router->srcRoom($room_id, $room_info['roo_picture']) : $cfg_default_image;
    $room_info['image'] = $image;
    $attrs = $AttributeModel->getAttributeOfId($room_id, GROUP_ROOM);
    $services = [];
    foreach ($attrs as $attr) {
        if ($attr['info']['param'] == 'tien-nghi-phong') {
            $services = array_values($attr['data']);
            break;
        }
    }
    $total_price += $roomType['roomPrice'];
    $total_discount += ($roomType['roomPrice'] * 15 / 100);
    $room_info['view'] = $HotelModel->showRoomView($room_info, true);
    $room_info['bed'] = $HotelModel->showRoomBed($room_info, false);
    $roomTypeGuests[] = [
        'roomId' => $room_info['roo_id'],
        'roomName' => $room_info['roo_name'],
        'roomCount' => $roomType['roomCount'],
        'total_adult' => $room_info['roo_adult'],
        'image' => $image,
        'view' => $room_info['view'],
        'bed' => $room_info['bed'],
        'adult' => $adult,
        'child' => $child,
        'infant' => $infant,
        'tags' => $services,
        'price' => format_number($roomType['roomPrice'])
    ];
}
// Mặc định là 5p
$time_limit = CURRENT_TIME + 5 * 60;
$paymentLink = $payOS->createBookingPayment(
    intval(substr(strval(microtime(true) * 10000), -6)),
    (int)$total_price,
    'TT TIEN PHONG NATURE',
    DOMAIN_WEB."/checkout.html",
    DOMAIN_WEB."/checkout.html",
    [],
    $time_limit
);
$redirect_url = $paymentLink['checkoutUrl'];

$total_discount = $total_price + ($total_price * (15 / 100));
$total_price = format_number($total_price);
$total_discount = format_number($total_discount);