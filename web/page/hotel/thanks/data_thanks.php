<?php
use src\Services\HotelService;
use src\Services\Sennet;
use src\Models\HotelPicture;
use src\Models\RoomPicture;

include('../../../Core/Config/require_web.php');
require_once('../../../libraries/payos/vendor/autoload.php');
include('../../../libraries/payos/src/PayOSWrapper.php');
use src\Facades\DB;
use src\Models\Hotel;
use src\Models\Room;
use src\Services\BookingModel;

$BookingModel = new BookingModel();
use Lib\PayOS\PayOSWrapper;
$payOS = new PayOSWrapper(
    PAYOS_CLIENT_ID,
    PAYOS_API_KEY, 
    PAYOS_CHECKSUM_KEY
);
// Lấy thông tin từ session
$booking_data = getValue('booking_data', GET_ARRAY, GET_SESSION);
$rooms = $booking_data['selectedRooms'] ?? [];
$checkIn = $booking_data['checkIn'] ?? '';
$checkOut = $booking_data['checkOut'] ?? '';
$hotel_id = $booking_data['hotel_id'] ?? 0;

function convertToTimestamp($date)
{
    $parts = explode('/', $date);
    if (count($parts) === 3) {
        return strtotime($parts[2] . '-' . $parts[1] . '-' . $parts[0]);
    }
    return strtotime($date);
}

// Định dạng thời gian nhận phòng và trả phòng
$checkInTimestamp = convertToTimestamp($checkIn);
$checkOutTimestamp = convertToTimestamp($checkOut);
$checkInFormatted = $checkIn ? date('d/m/Y', $checkInTimestamp) : '';
$checkOutFormatted = $checkOut ? date('d/m/Y', $checkOutTimestamp) : '';

// Tính số đêm
$nights = ($checkOutTimestamp - $checkInTimestamp) / 86400;

$booking_id = getValue('booking_completed', GET_INT, GET_GET, 0);

//token
$received_token = getValue('token', GET_STRING, GET_GET, '');
$session_token = getValue('payment_token', GET_STRING, GET_SESSION, '');

if (empty($received_token) || $received_token !== $session_token) {
    // $error_message = "Yêu cầu không hợp lệ";
    // http_response_code(403);
      header('Location: ' . "/");
    exit;
}

if ($booking_id > 0) {
    $booking_info = $DB->query("SELECT booking_hotel.*, hot_id, hot_name, hot_picture, hot_checkin, hot_checkout, 
                                hot_type, hot_address_full, hot_star, hot_id_mapping
                                FROM booking_hotel
                                INNER JOIN hotel ON bkho_hotel_id = hot_id
                                WHERE bkho_id = " . $booking_id)->getOne();

    if (empty($booking_info) ) {
        // $error_message = "Booking không tồn tại";
          header('Location: ' . "/");
    } else {

        // Lấy 1 ảnh khách sạn
        $hotel_picture = HotelPicture::where('hopi_hotel_id', $booking_info['hot_id'])->getOne(); 
        $image_hotel = $hotel_picture ? $Router->srcHotel($booking_info['hot_id'], $hotel_picture['hopi_picture']) : $cfg_default_image;

        // Lấy payment_data từ session
        $payment_data = getValue('payment_data', GET_ARRAY, GET_SESSION, []);
        $code = $payment_data['orderCode'] ?? '';

        if (empty($code) || empty($payment_data) ) {
            $error_message = "Link thanh toán không hợp lệ hoặc đã hết hạn";
        } else {
            // Kiểm tra trạng thái qua API PayOS
                $payment_info = $payOS->getPaymentLinkInformation($code);

                if ($payment_info['status'] == 'PAID') {
                    // Chuẩn bị payload để gửi tới SenNet API
                    $hotel_info = Hotel::where(['hot_id' => $hotel_id, 'hot_active' => STATUS_ACTIVE])->getOne();

                    if (empty($hotel_info)) {
                        throw new Exception('Không tìm thấy thông tin khách sạn');
                    }

                    if (!isset($hotel_info['hot_id_mapping'])) {
                        throw new Exception('Khách sạn không có hot_id_mapping');
                    }

                    $hotel_id_mapping = $hotel_info['hot_id_mapping'];

                    $checkin_timestamp = convertToTimestamp($checkIn);
                    $checkout_timestamp = convertToTimestamp($checkOut);

                    if ($checkin_timestamp === false || $checkout_timestamp === false) {
                        throw new Exception('Định dạng ngày không hợp lệ');
                    }

                    if ($checkin_timestamp >= $checkout_timestamp) {
                        throw new Exception('Ngày check-out phải sau ngày check-in');
                    }

                    $total_amount = 0;
                    $room_details = [];

                    foreach ($rooms as $roomType) {
                        $room_id = (int) $roomType['roomId'];
                        $room_count = (int) $roomType['roomCount'];
                        $room_price = (float) $roomType['roomPrice'];
                        $total_amount += $room_price * $room_count;
                        //TODO: cái này hơi đần
                        // Thêm priceFormatted nếu chưa có
                        if (!isset($roomType['priceFormatted'])) {
                            $roomType['priceFormatted'] = format_number($room_price);
                        }
                        
                        // Thêm totalPrice nếu chưa có
                        if (!isset($roomType['totalPrice'])) {
                            $roomType['totalPrice'] = $room_price * $room_count;
                        }

                        // Lấy ảnh phòng
                        $room_info = Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();
                        $image_room = $room_info ? $Router->srcRoom($room_id, $room_info['roo_picture']) : $cfg_default_image;
                        if (empty($room_info)) {
                            throw new Exception("Không tìm thấy thông tin phòng ID: $room_id");
                        }

                        if (!isset($room_info['roo_id_mapping'])) {
                            throw new Exception("Phòng không có roo_id_mapping");
                        }
                        $room_id_mapping = $room_info['roo_id_mapping'];

                        foreach ($roomType['rooms'] as $index => $room) {
                            if (!isset($room_details[$room_id_mapping])) {
                                $room_details[$room_id_mapping] = [];
                            }

                            $room_details[$room_id_mapping][] = [
                                'room_id' => $room_id_mapping,
                                'qty' => 1,
                                'price' => $room_price,
                                'adults' => $room['adults'],
                                'children' => $room['children'],
                                'infants' => $room['infants']
                            ];
                        }
                    }

                    $total_adults = 0;
                    $total_children = 0;
                    $total_infants = 0;

                    foreach ($room_details as $room_group) {
                        foreach ($room_group as $room) {
                            $total_adults += $room['adults'];
                            $total_children += $room['children'];
                            $total_infants += $room['infants'];
                        }
                    }

                    $daterange = $checkIn . ' - ' . $checkOut;
                    $room_prices = [];

                    $current_time = $checkin_timestamp;
                    $end_time = $checkout_timestamp;

                    while ($current_time < $end_time) {
                        $day_key = date('d/m/Y', $current_time);

                        foreach ($rooms as $roomType) {
                            $room_id = (int) $roomType['roomId'];
                            $room_count = (int) $roomType['roomCount'];
                            $room_info = Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();

                            if (!empty($room_info) && isset($room_info['roo_id_mapping'])) {
                                $room_id_mapping = $room_info['roo_id_mapping'];
                                $date_range_single_day = $day_key . ' - ' . date('d/m/Y', $current_time + 86400);
                                $price_info = HotelService::getRoomPriceAndAvailability($hotel_id, $date_range_single_day, $room_id);

                                $price = 0;

                                if (!empty($price_info['price_per_day']) && isset($price_info['price_per_day'][$day_key])) {
                                    $price = $price_info['price_per_day'][$day_key];
                                }

                                $price_record = [
                                    'timestamp' => $current_time,
                                    'room_id' => $room_id_mapping
                                ];

                                for ($i = 1; $i <= $room_count; $i++) {
                                    $price_record['room_item_' . $i] = $price;
                                }

                                $room_prices[] = $price_record;
                            }
                        }

                        $current_time += 86400;
                    }

                    $booking_payload = [
                        'hotel_id' => $hotel_id_mapping,
                        'hotel_id_mapping' => $hotel_id_mapping,
                        'bkho_id' => $booking_id,
                        'bkho_name' => $booking_info['bkho_name'],
                        'bkho_phone' => $booking_info['bkho_phone'],
                        'bkho_email' => $booking_info['bkho_email'],
                        'bkho_address' => $booking_info['bkho_address'],
                        'bkho_checkin' => $booking_info['bkho_checkin'],
                        'bkho_checkout' => $booking_info['bkho_checkout'],
                        'bkho_adult' => $booking_info['bkho_adult'],
                        'bkho_children' => $booking_info['bkho_children'],
                        'bkho_baby' => $booking_info['bkho_baby'],
                        'bkho_money_total' => $booking_info['bkho_money_total'],
                        'bkho_money_pay' => $booking_info['bkho_money_pay'],
                        'bkho_money_received' => 0,
                        'bkho_money_discount' => 0,
                        'bkho_note_process' => 'Đặt phòng online qua website',
                        'bkho_customer_type' => 0,
                        'rooms' => $room_details,
                        'room_prices' => $room_prices,
                        'bkho_hotel_id' => $hotel_id
                    ];

                    // Gửi booking qua Sennet và cập nhật trạng thái
                    $sennet_response = Sennet::sendBookingSennet($booking_payload);

                    if (!is_array($sennet_response)) {
                        throw new Exception('Phản hồi từ Sennet không hợp lệ');
                    }

                    if (isset($sennet_response['success'])) {
                        $booking_model = new BookingModel();
                        $model_response = $booking_model->sendBookingToSennet($booking_payload);
                        if (is_array($model_response) && isset($model_response['status']) && $model_response['status'] === false) {
                            throw new Exception($model_response['message']);
                        }
                        // Xóa session sau khi thành công
                        unset($_SESSION['time_limit']);
                        unset($_SESSION['booking_completed']);
                    } else {
                        throw new Exception($sennet_response['message'] ?? 'Lỗi không xác định từ Sennet');
                    }
                } 
            
        }
    }
}
?>