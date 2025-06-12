<?php
// Cấu hình HTTP header cho JSON
header('Content-Type: application/json; charset=UTF-8');

use src\Models\Hotel;
use src\Models\Room;

// Include các file cần thiết
include('../Core/Config/require_web.php');

// Hàm chuyển đổi định dạng ngày từ dd/mm/yyyy sang yyyy-mm-dd
function convertDateFormat($date) {
    $parts = explode('/', $date);
    if (count($parts) === 3) {
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return $date;
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
    
    // Nếu không có dữ liệu đặt phòng
    if (empty($booking_data)) {
        throw new Exception('Không tìm thấy dữ liệu đặt phòng');
    }
    
    // Lấy thông tin khách sạn
    $hotel_info = Hotel::where(['hot_id' => $hotel_id, 'hot_active' => STATUS_ACTIVE])->getOne();
    
    if(empty($hotel_info)) {
        throw new Exception('Không tìm thấy thông tin khách sạn');
    }  
    // Tạo mảng ngày từ check-in đến check-out
    $start_date = strtotime(convertDateFormat($checkIn));
    $end_date = strtotime(convertDateFormat($checkOut));
    $dates = [];
    
    // Kiểm tra ngày hợp lệ
    if ($start_date === false || $end_date === false) {
        throw new Exception('Định dạng ngày không hợp lệ');
    }
    
    if ($start_date >= $end_date) {
        throw new Exception('Ngày check-out phải sau ngày check-in');
    }
    
    for ($date = $start_date; $date < $end_date; $date += 86400) {
        $dates[] = date('d/m/Y', $date);
    }
    
    $days_count = count($dates);
    if ($days_count <= 0) {
        throw new Exception('Không có ngày nào trong khoảng thời gian đã chọn');
    }
    
    // Xử lý thông tin chi tiết cho từng phòng
    foreach($rooms as $roomType) {
        $room_id = (int) $roomType['roomId'];
        $room_info = Room::where(['roo_id' => $room_id, 'roo_active' => STATUS_ACTIVE])->getOne();
        
        // Lấy giá phòng theo từng ngày từ database
        $daterange = $checkIn . ' - ' . $checkOut;
        $room_price_info = \src\Services\HotelService::getRoomPriceAndAvailability($hotel_id, $daterange, $room_id);
        
        // Sử dụng giá theo từng ngày từ database hoặc phân bổ đều nếu không có
        $daily_prices = [];
        if (!empty($room_price_info['price_per_day'])) {
            $daily_prices = $room_price_info['price_per_day'];
        } else {
            // Phân bổ đều nếu không có giá từ database
            $price_per_day = $roomType['roomPrice'] / $days_count;
            foreach($dates as $date) {
                $daily_prices[$date] = round($price_per_day, 0);
            }
        }
        
        // Tạo thông tin chi tiết về phòng
        $room_details[$room_id] = [
            'room_id' => $room_id,
            'room_count' => $roomType['roomCount'],
            'rooms' => [], // Thêm mảng chứa thông tin từng phòng
            'price_by_date' => $daily_prices
        ];
        
        // Thêm thông tin từng phòng riêng biệt
        foreach ($roomType['rooms'] as $room) {
            $room_details[$room_id]['rooms'][] = [
                'adults' => $room['adults'],
                'children' => $room['children'], 
                'infants' => $room['infants']
            ];
        }
    }
    
    // Chuẩn bị dữ liệu để trả về
    $response = [
        'success' => true,
        'data' => [
            'hotel_id' => $hotel_id,
            'dates' => [
                'check_in' => strtotime(convertDateFormat($checkIn)),
                'check_out' => strtotime(convertDateFormat($checkOut)),
            ],
            'guest' => [
                'name' => $username,
                'email' => $email,
                'phone' => $phone
            ],
            'rooms' => $room_details
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Trả về lỗi nếu có
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>