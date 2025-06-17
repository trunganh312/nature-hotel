<?php

use src\Facades\DB;
use src\Services\CommonService;
include('../Core/Config/require_web.php');
require_once('../libraries/payos/vendor/autoload.php');
include('../libraries/payos/src/PayOSWrapper.php');
use Lib\PayOS\PayOSWrapper;
use src\Services\BookingModel;

$payOS = new PayOSWrapper(
    PAYOS_CLIENT_ID,
    PAYOS_API_KEY, 
    PAYOS_CHECKSUM_KEY
);
$BookingModel = new BookingModel();

$username = $_POST['username'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$checkIn = $_POST['checkIn'] ?? '';
$checkOut = $_POST['checkOut'] ?? '';
$hotel_id = (int)($_POST['hotel_id'] ?? 0);
$rooms_json = $_POST['rooms'] ?? '';

if (empty($username) || empty($phone) || empty($checkIn) || empty($checkOut) || $hotel_id <= 0 || empty($rooms_json)) {
    CommonService::resJson(['error' => 'Dữ liệu không hợp lệ']);
}

// Validate định dạng ngày
$checkInTime = strtotime(str_replace('/', '-', $checkIn));
$checkOutTime = strtotime(str_replace('/', '-', $checkOut));
if (!$checkInTime || !$checkOutTime || $checkOutTime <= $checkInTime) {
    CommonService::resJson(['error' => 'Ngày không hợp lệ']);
}

// Validate số điện thoại
if (!preg_match('/^(0|\+84)\d{9,10}$/', $phone)) {
    CommonService::resJson(['error' => 'Số điện thoại không hợp lệ']);
}

// Parse rooms JSON
try {
    $rooms = json_decode($rooms_json, true);
    if (empty($rooms)) {
        throw new Exception('Dữ liệu phòng không hợp lệ');
    }
} catch (Exception $e) {
    CommonService::resJson(['error' => $e->getMessage()]);
}
$status_new = STT_NEW;

// Tính tổng số người và tổng giá từ tất cả phòng
$total_adult = 0;
$total_children = 0;
$total_baby = 0;
$total_price = 0;
foreach ($rooms as $room) {
    $total_adult += $room['adult'] * $room['roomCount'];
    $total_children += $room['child'] * $room['roomCount'];
    $total_baby += $room['infant'] * $room['roomCount'];
    $formatted_price = $room['price'];
    $room_price = (float)str_replace(',', '', $formatted_price);
    $total_price += $room_price;
}
function generateCode()
{
    $code   =   'H';
    $code   .=  substr(date('Y'), -1);   //Đầu tiên là số cuối của năm
    $code   .=  date('md'); //Tiếp theo là Tháng-Ngày
    $code   .=  rand(101, 999);

    //Check xem có bị trùng ko
    $check  =   DB::query("SELECT bkho_id FROM booking_hotel WHERE bkho_code = '$code'")->getOne();

    if (!empty($check)) {
        return generateCode();
    }

    return $code;
}
// Tạo booking mới
$checkInTime = (int)$checkInTime;
$checkOutTime = (int)$checkOutTime;
$current_time = CURRENT_TIME;
$booking_code = generateCode();
$booking_id = DB::pass()->executeReturn("INSERT INTO booking_hotel 
    (bkho_hotel_id, bkho_checkin, bkho_checkout, bkho_name, bkho_phone, bkho_email, bkho_status, bkho_money_pay, bkho_money_total, bkho_adult, bkho_children, bkho_baby, bkho_time_create, bkho_code) 
    VALUES ($hotel_id, $checkInTime, $checkOutTime, '$username', '$phone', '$email', $status_new, $total_price, $total_price, $total_adult, $total_children, $total_baby, $current_time, '$booking_code')");

// Lưu phòng vào booking_hotel_room
$booking_rooms = [];
foreach ($rooms as $room) {
    $room_id = (int)$room['roomId'];
    $room_count = (int)$room['roomCount'];
    $formatted_price = $room['price'];
    $room_price = (float)str_replace(',', '', $formatted_price) / $room_count;

    DB::pass()->execute("INSERT INTO booking_hotel_room (bhr_booking_hotel_id, bhr_room_id, bhr_qty, bhr_price, bhr_created_at) 
        VALUES ($booking_id, $room_id, $room_count, $room_price, $current_time)");

    $room_info = DB::query("SELECT roo_id, roo_name, roo_id_mapping FROM room WHERE roo_id = $room_id")->getOne();
    $booking_rooms[] = [
        'roo_id' => $room_id,
        'roo_name' => $room_info['roo_name'],
        'roo_id_mapping' => $room_info['roo_id_mapping'],
        'bhr_qty' => $room_count,
        'bhr_price' => $room_price
    ];
}

// Cập nhật tổng tiền
DB::pass()->execute("UPDATE booking_hotel SET bkho_money_pay = $total_price WHERE bkho_id = $booking_id");

// Lưu booking_id vào session
$_SESSION['booking_completed'] = $booking_id;

// Phần 2: Tạo booking và giữ phòng (khi nhận POST request)
$booking_info = DB::query("SELECT booking_hotel.*, hot_id, hot_name, hot_id_mapping 
                           FROM booking_hotel INNER JOIN hotel ON bkho_hotel_id = hot_id 
                           WHERE bkho_id = $booking_id")->getOne();

if (empty($booking_info)) {
    CommonService::resJson([
        'success' => 0,
        'error' => 'Booking không tồn tại',
        'msg' => 'Failed'
    ]);
    exit;
}

if ($booking_info['bkho_status'] != STT_SUCCESS) {
    $time_expired = CURRENT_TIME + 5 * 60;
    $_SESSION['time_limit'] = $time_expired;

    $bookingResult = $BookingModel->holdBookingHotel($booking_info, $booking_rooms, $time_expired);
    if (is_array($bookingResult) && isset($bookingResult['error']) && $bookingResult['error'] == 1) {
        DB::pass()->execute("UPDATE booking_hotel SET bkho_status = " . STT_CANCEL . " WHERE bkho_id = $booking_id");
        DB::pass()->execute("DELETE FROM booking_hotel_room WHERE bhr_booking_hotel_id = $booking_id");
        unset($_SESSION['booking_completed']);
        CommonService::resJson(['error' => $bookingResult['message']]);
    }

    // Tạo link thanh toán PayOS
    $room_items = [];
    foreach ($booking_rooms as $room) {
        if ((int)$room['bhr_qty'] < 1) continue;
        $room_items[] = [
            'name' => $room['roo_name'],
            'quantity' => (int)$room['bhr_qty'],
            'price' => (int)$room['bhr_price']
        ];
    }

    try {
        if ($total_price <= 0) {
            throw new Exception("Số tiền thanh toán không hợp lệ");
        }

        $time_limit = CURRENT_TIME + 5 * 60;
        $orderCode = intval(substr(strval(microtime(true) * 10000), -6));

        $payment_token = bin2hex(random_bytes(16));
        $_SESSION['payment_token'] = $payment_token;
        
        $paymentLink = $payOS->createBookingPayment(
            $orderCode,
            (int)$total_price,
            // 10000,
            'TT TIEN PHONG NATURE',
            DOMAIN_WEB . "/thanks.html?booking_completed=$booking_id", 
            DOMAIN_WEB . "/checkout.html?booking_completed=$booking_id",
            $room_items,
            $time_limit
        );
        // Lưu paymentLink vào session
        $_SESSION['payment_data'] = [
            'orderCode' => $orderCode,
            'checkoutUrl' => $paymentLink['checkoutUrl'],
            'status' => 'PENDING',
            'expiredAt' => $time_limit
        ];
        
        // Trả về JSON thay vì redirect trực tiếp
        echo json_encode(['redirect_url' => $paymentLink['checkoutUrl']]);
        exit;
    } catch (Exception $e) {
        DB::query("UPDATE booking_hotel SET bkho_status = $stt_cancel WHERE bkho_id = $booking_id");
        DB::query("DELETE FROM booking_hotel_room WHERE bhr_booking_hotel_id = $booking_id");
        unset($_SESSION['booking_completed']);
        http_response_code(400);
        echo json_encode(['error' => 'Lỗi tạo link thanh toán: ' . $e->getMessage()]);
        exit;
    }
} else {
    CommonService::resJson(['error' => 'Booking đã hoàn tất']);
}
?>