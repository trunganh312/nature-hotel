<?

use src\Services\HotelService;
header('Content-Type: application/json; charset=UTF-8');

include('../../../Core/Config/require_web.php');
use src\Facades\DB;
use src\Models\Hotel;
use src\Models\Room;
use src\Services\BookingModel;

$BookingModel = new BookingModel();

// Xử lý callback PayOS cho trạng thái PAID
$cancel = getValue('cancel', GET_STRING, GET_GET, '');
$status = getValue('status', GET_STRING, GET_GET, '');
$booking_id = getValue('booking_completed', GET_INT, GET_GET, 0);

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

if ($booking_id > 0) {
    $booking_info = DB::query("SELECT booking_hotel.*
        FROM booking_hotel
        WHERE bkho_id = $booking_id")->getOne();
}

// Xử lý callback PayOS cho trạng thái PAID
if ($cancel == 'false' && $status == 'PAID' && $booking_id > 0) {

    // // Lấy thông tin từ session
    // $booking_data = getValue('booking_data', GET_ARRAY, GET_SESSION);
    // $rooms = $booking_data['selectedRooms'] ?? [];
    // $checkIn = $booking_data['checkIn'] ?? '';
    // $checkOut = $booking_data['checkOut'] ?? '';
    // $hotel_id = $booking_data['hotel_id'] ?? 0;


    // Lấy thông tin khách sạn
    $hotel_info = Hotel::where(['hot_id' => $hotel_id, 'hot_active' => STATUS_ACTIVE])->getOne();

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

        $room_info = Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();

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
            $room_info = Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();

            if (!empty($room_info) && isset($room_info['roo_id_mapping'])) {
                $room_id_mapping = $room_info['roo_id_mapping'];
                $date_range_single_day = $day_key . ' - ' . date('d/m/Y', $current_time + 86400);
                $price_info = HotelService::getRoomPriceAndAvailability($hotel_id, $date_range_single_day, $room_id);

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
    $booking_info = DB::query("SELECT booking_hotel.*, hot_id, hot_name, hot_id_mapping 
        FROM booking_hotel INNER JOIN hotel ON bkho_hotel_id = hot_id 
        WHERE bkho_id = $booking_id")->getOne();

    if (empty($booking_info)) {
        $error_message = "Booking không tồn tại";
    }
    // Chuẩn bị payload để gửi tới SenNet API
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
        'bkho_hotel_id' => $hotel_id // Thêm bkho_hotel_id cho BookingModel
    ];

    $stt_cancel = STT_CANCEL;

    // Kiểm tra nếu booking đã được xác nhận (status = 7) thì không gửi lại
    if (isset($sennet_response['data']['booking']['status']) && $sennet_response['data']['booking']['status'] == STT_CONFIRM) {
        return;
    }

    // Gửi booking qua Sennet và cập nhật trạng thái
    $sennet_response = \src\Services\Sennet::sendBookingSennet($booking_payload);

    if (!is_array($sennet_response)) {
        throw new Exception('Phản hồi từ Sennet không hợp lệ');
    }

    if (isset($sennet_response['success'])) {
        // Gọi BookingModel để cập nhật trạng thái và trừ tồn phòng
        $booking_model = new BookingModel();
        $model_response = $booking_model->sendBookingToSennet($booking_payload);

        if (is_array($model_response) && isset($model_response['status']) && $model_response['status'] === false) {
            throw new Exception($model_response['message']);
        }

        // echo json_encode([
        //     'success' => true,
        //     'message' => 'Đặt phòng thành công',
        //     'booking_id' => $sennet_response['data']['booking']['id'] ?? $booking_id,
        //     'booking_code' => $sennet_response['data']['booking']['code'] ?? ''
        // ]);
    } else {
        throw new Exception($sennet_response['message'] ?? 'Lỗi không xác định từ Sennet');
    }
}
