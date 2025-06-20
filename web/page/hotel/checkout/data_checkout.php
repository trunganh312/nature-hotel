<?

use src\Facades\DB;
use src\Models\Hotel;
use src\Models\Room;
use src\Services\BookingModel;
include('../../../Core/Config/require_web.php');
require_once('../../../libraries/payos/vendor/autoload.php');
include('../../../libraries/payos/src/PayOSWrapper.php');
use Lib\PayOS\PayOSWrapper;
$payOS = new PayOSWrapper(
    PAYOS_CLIENT_ID,
    PAYOS_API_KEY, 
    PAYOS_CHECKSUM_KEY
);
$BookingModel = new BookingModel();

// Lấy thông tin từ session
$booking_data = getValue('booking_data', GET_ARRAY, GET_SESSION, []);
$rooms = $booking_data['selectedRooms'];
$checkIn = $booking_data['checkIn'];
$checkOut = $booking_data['checkOut'];
$hotel_id = $booking_data['hotel_id'];

$nights = (str_totime($checkOut) - str_totime($checkIn)) / 86400;

// Lấy thông tin khách sạn
$hotel_info = Hotel::where(['hot_id' => $hotel_id, 'hot_active' => STATUS_ACTIVE])->getOne();

if(empty($hotel_info) && empty($booking_data)) {
    // Chuyển hướng về trang chủ
    header('Location: ' . "/");
    
}
$hotel_info['link'] = '/hotel-' . $hotel_info['hot_id'] . '-' . to_slug($hotel_info['hot_name']) . '.html';

$image_hotel = isset($hotel_info['hot_picture']) ? $Router->srcHotel($hotel_id, $hotel_info['hot_picture']) : $cfg_default_image;

$roomTypeGuests = []; // Mảng lưu tổng số người từng hạng phòng

$total_adult = 0;
$total_child = 0;
$total_infant = 0;
$total_room = 0;
$total_price = 0;
$total_discount = 0;

foreach($rooms as $roomType) {
    // lặp qua rooms
    foreach($roomType['rooms'] as $room) {
        $adult = $room['adults'];
        $child = $room['children'];
        $infant = $room['infants'];
        $total_adult += $adult;
        $total_child += $child;
        $total_infant += $infant;
    }
    
    $total_room += $roomType['roomCount'];
    $total_price += $roomType['roomPrice'] * $roomType['roomCount'];
    
    $room_id = (int) $roomType['roomId'];
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
    $total_discount += ($total_price * 15 / 100);
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
        'price' => format_number($roomType['roomPrice'] * $roomType['roomCount'])
    ];
}
$cancel = getValue('cancel', GET_STRING, GET_GET, '');
$status = getValue('status', GET_STRING, GET_GET, '');
$booking_id = getValue('booking_completed', GET_INT, GET_GET, 0);
$booking_id_session = getValue('booking_completed', GET_INT, GET_SESSION, 0);
$status_cancel = STT_CANCEL;

// Xử lý hủy booking (từ session hoặc query cancel)
$booking_to_cancel = 0;
if ($booking_id_session > 0) {
    $booking_to_cancel = $booking_id_session;
} elseif ($cancel == 'true' && $status == 'CANCELLED' && $booking_id > 0) {
    $booking_to_cancel = $booking_id;
}

if ($booking_to_cancel > 0) {
    $booking_info = DB::query("SELECT booking_hotel.*, hot_id, hot_name, hot_id_mapping 
                               FROM booking_hotel 
                               INNER JOIN hotel ON bkho_hotel_id = hot_id 
                               WHERE bkho_id = $booking_to_cancel AND bkho_hotel_id = $hotel_id")->getOne();

    if (!empty($booking_info) && $booking_info['bkho_status'] != STT_SUCCESS) {
        $result = $BookingModel->unholdBookingHotel($booking_info);
        DB::query("UPDATE booking_hotel SET bkho_status = $status_cancel WHERE bkho_id = $booking_to_cancel");
        if (is_array($result) && isset($result['error']) && $result['error'] == 1) {
            $error_message = $result['message'];
        } else {
            unset($_SESSION['booking_completed']);
            unset($_SESSION['time_limit']);
            unset($_SESSION['payment_data']);
            unset($_SESSION['payment_token']);
            $success_message = "Đã hủy booking thành công";
        }
    } elseif (empty($booking_info)) {
        $error_message = "Booking không tồn tại";
    }
}

// Kiểm tra payment_data
$payment_data = getValue('payment_data', GET_ARRAY, GET_SESSION, []);
if (!empty($payment_data) && $payment_data['status'] == 'PENDING' && $payment_data['expiredAt'] > CURRENT_TIME && $booking_id_session == 0) {
    $redirect_url = $payment_data['checkoutUrl'];
} else {
    $redirect_url = '';
}
if ($cancel == 'true' && $status == 'CANCELLED' && $booking_id > 0) {
    $booking_info = DB::query("SELECT booking_hotel.*, hot_id, hot_name, hot_id_mapping 
                               FROM booking_hotel INNER JOIN hotel ON bkho_hotel_id = hot_id 
                               WHERE bkho_id = $booking_id")->getOne();

    if (empty($booking_info)) {
        $error_message = "Booking không tồn tại";
    } else {
        $result = $BookingModel->unholdBookingHotel($booking_info);
        DB::query("UPDATE booking_hotel SET bkho_status = $status_cancel WHERE bkho_id = $booking_id");
        if (is_array($result) && isset($result['error']) && $result['error'] == 1) {
            $error_message = $result['message'];
        } else {
            unset($_SESSION['time_limit']);
            unset($_SESSION['url_cancel']);
            unset($_SESSION['booking_completed']);
            $success_message = "Đã hủy booking thành công";
        }
    }
}
$redirect_url = ''; // Để trống, sẽ được xử lý trong hold_room.php

$total_discount = $total_price + ($total_price * (15 / 100));
$total_price = format_number($total_price);
$total_discount = format_number($total_discount);