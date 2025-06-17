<?php

use src\Facades\DB;
// Cấu hình HTTP header cho JSON
header('Content-Type: application/json; charset=UTF-8');

// Include các file cần thiết
include('../Core/Config/require_web.php');

// Hàm chuyển đổi định dạng ngày từ dd/mm/yyyy sang timestamp
function convertToTimestamp($date)
{
    $parts = explode('/', $date);
    if (count($parts) === 3) {
        return strtotime($parts[2] . '-' . $parts[1] . '-' . $parts[0]);
    }
    return strtotime($date);
}

// Hàm tạo chữ ký HMAC-SHA256
function generateSignature($payload, $secretKey)
{
    return hash_hmac('sha256', $payload, $secretKey);
}

try {
    // Lấy thông tin từ session
    $booking_data = getValue('booking_data', GET_ARRAY, GET_SESSION);
    $rooms = $booking_data['selectedRooms'] ?? [];
    $checkIn = $booking_data['checkIn'] ?? '';
    $checkOut = $booking_data['checkOut'] ?? '';
    $hotel_id = $booking_data['hotel_id'] ?? 0;

    // Lấy thông tin khách hàng từ request
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';

    // Nếu không có dữ liệu đặt phòng
    if (empty($booking_data) || empty($rooms) || empty($checkIn) || empty($checkOut) || $hotel_id <= 0) {
        throw new Exception('Dữ liệu đặt phòng không hợp lệ');
    }
    if (empty($username) || empty($phone)) {
        throw new Exception('Họ tên và số điện thoại là bắt buộc');
    }
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email không hợp lệ');
    }
    if (!preg_match('/^(0|\+84)\d{9,10}$/', $phone)) {
        throw new Exception('Số điện thoại không hợp lệ');
    }

    // Lấy thông tin khách sạn
    $hotel_info = \src\Models\Hotel::where(['hot_id' => $hotel_id, 'hot_active' => STATUS_ACTIVE])->getOne();

    if (empty($hotel_info)) {
        throw new Exception('Không tìm thấy thông tin khách sạn');
    }

    // Kiểm tra và lấy hot_id_mapping
    if (!isset($hotel_info['hot_id_mapping'])) {
        throw new Exception('Khách sạn không có hot_id_mapping');
    }

    $hotel_id_mapping = $hotel_info['hot_id_mapping'];

    // Chuyển đổi ngày thành timestamp
    $checkin_timestamp = convertToTimestamp($checkIn);
    $checkout_timestamp = convertToTimestamp($checkOut);

    // Kiểm tra ngày hợp lệ
    if ($checkin_timestamp === false || $checkout_timestamp === false) {
        throw new Exception('Định dạng ngày không hợp lệ');
    }

    if ($checkin_timestamp >= $checkout_timestamp) {
        throw new Exception('Ngày check-out phải sau ngày check-in');
    }

    // Tính tổng số tiền
    $total_amount = 0;
    // Thay đổi khởi tạo $room_details từ array sang object
    $room_details = [];

    foreach ($rooms as $roomType) {
        $room_id = (int) $roomType['roomId'];
        $room_count = (int) $roomType['roomCount'];
        $room_price = (float) $roomType['roomPrice'];
        $total_amount += $room_price * $room_count;

        $room_info = \src\Models\Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();

        if (empty($room_info)) {
            throw new Exception("Không tìm thấy thông tin phòng ID: $room_id");
        }

        if (!isset($room_info['roo_id_mapping'])) {
            throw new Exception("Phòng không có roo_id_mapping");
        }
        $room_id_mapping = $room_info['roo_id_mapping'];

        // Xử lý thông tin từng phòng
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

    // Cập nhật cách tính tổng số người
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

    // Thêm thông tin giá phòng theo ngày
    $daterange = $checkIn . ' - ' . $checkOut;
    $room_prices = [];

    // Tạo các timestamp cho từng ngày từ check-in đến check-out
    $current_time = $checkin_timestamp;
    $end_time = $checkout_timestamp;

    while ($current_time < $end_time) {
        $day_key = date('d/m/Y', $current_time);

        foreach ($rooms as $roomType) {
            $room_id = (int) $roomType['roomId'];
            $room_count = (int) $roomType['roomCount'];
            $room_info = \src\Models\Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();

            if (!empty($room_info) && isset($room_info['roo_id_mapping'])) {
                $room_id_mapping = $room_info['roo_id_mapping'];
                $date_range_single_day = $day_key . ' - ' . date('d/m/Y', $current_time + 86400);
                $price_info = \src\Services\HotelService::getRoomPriceAndAvailability($hotel_id, $date_range_single_day, $room_id);

                $price = 0; // Giá mặc định

                // Nếu có thông tin giá phòng thì lấy giá
                if (!empty($price_info['price_per_day']) && isset($price_info['price_per_day'][$day_key])) {
                    $price = $price_info['price_per_day'][$day_key];
                }

                // Tạo bản ghi giá phòng theo yêu cầu
                $price_record = [
                    'timestamp' => $current_time,
                    'room_id' => $room_id_mapping
                ];

                // Thêm các room_item
                for ($i = 1; $i <= $room_count; $i++) {
                    $price_record['room_item_' . $i] = $price;
                }

                $room_prices[] = $price_record;
            }
        }

        $current_time += 86400;
    }
    $status_confirm = STT_CONFIRM;
    $status_cancel = STT_CANCEL;

    $booking_id = DB::pass()->executeReturn("INSERT INTO booking_hotel (bkho_hotel_id, bkho_checkin, bkho_checkout, bkho_name, bkho_phone, bkho_email, bkho_address, bkho_adult, bkho_children, bkho_baby, bkho_money_pay, bkho_status) 
        VALUES ($hotel_id, $checkin_timestamp, $checkout_timestamp, '$username', '$phone', '$email', '$address', $total_adults, $total_children, $total_infants, $total_amount, $status_confirm)");

    foreach ($rooms as $roomType) {
        $room_id = (int)$roomType['roomId'];
        $room_count = (int)$roomType['roomCount'];
        $room_price = (float)$roomType['roomPrice'];
        DB::pass()->execute("INSERT INTO booking_hotel_room (bhr_booking_hotel_id, bhr_room_id, bhr_qty, bhr_price) 
            VALUES ($booking_id, $room_id, $room_count, $room_price)");
    }
    // Chuẩn bị payload để gửi tới SenNet API
    $booking_payload = [
        'hotel_id' => $hotel_id_mapping,
        'hotel_id_mapping' => $hotel_id_mapping,
        'bkho_id' => $booking_id,
        'bkho_name' => $username,
        'bkho_phone' => $phone,
        'bkho_email' => $email,
        'bkho_address' => $address,
        'bkho_checkin' => $checkin_timestamp,
        'bkho_checkout' => $checkout_timestamp,
        'bkho_adult' => $total_adults,
        'bkho_children' => $total_children,
        'bkho_baby' => $total_infants,
        'bkho_money_total' => $total_amount,
        'bkho_money_pay' => $total_amount,
        'bkho_money_received' => 0,
        'bkho_money_discount' => 0,
        'bkho_note_process' => 'Đặt phòng online qua website',
        'bkho_customer_type' => 0,
        'rooms' => $room_details,
        'room_prices' => $room_prices,
        'bkho_hotel_id' => $hotel_id // Thêm bkho_hotel_id cho BookingModel
    ];

    // Gửi booking qua Sennet và cập nhật trạng thái
    try {
        $sennet_response = \src\Services\Sennet::sendBookingSennet($booking_payload);
        if (!is_array($sennet_response)) {
            throw new Exception('Phản hồi từ Sennet không hợp lệ');
        }

        if (isset($sennet_response['success']) && $sennet_response['success']) {
            // Gọi BookingModel để cập nhật trạng thái và trừ tồn phòng
            $booking_model = new \src\Services\BookingModel();
            $model_response = $booking_model->sendBookingToSennet($booking_payload);

            if (is_array($model_response) && isset($model_response['status']) && $model_response['status'] === false) {
                throw new Exception($model_response['message']);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Đặt phòng thành công',
                'booking_id' => $sennet_response['booking_id'] ?? $booking_id,
                'booking_code' => $sennet_response['booking_code'] ?? ''
            ]);
        } else {
            throw new Exception($sennet_response['message'] ?? 'Có lỗi xảy ra khi đặt phòng');
        }
    } catch (Exception $e) {
        // Xóa room_hold_temp nếu có lỗi
        DB::pass()->execute("DELETE FROM room_hold_temp WHERE roht_booking_id = $booking_id");
        // Cập nhật trạng thái thất bại
        DB::pass()->execute("UPDATE booking_hotel SET bkho_status = $status_cancel WHERE bkho_id = $booking_id");
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'error_code' => 'SENNET_ERROR'
        ]);
    }

} catch (Exception $e) {
    // Trả về lỗi nếu có
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>