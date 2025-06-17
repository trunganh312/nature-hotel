<?

use src\Facades\DB;
use src\Services\CommonService;
use src\Services\HotelService;

include('../Core/Config/require_web.php');

// Nhận vào CI CO
$checkIn = getValue('checkIn', GET_STRING, GET_POST, getValue('search_checkin', GET_STRING, GET_COOKIE, ''));
$checkOut = getValue('checkOut', GET_STRING, GET_POST, getValue('search_checkout', GET_STRING, GET_COOKIE, ''));
$hotel_id = getValue('hotel_id', GET_INT, GET_POST, 0);

if (empty($checkIn) || empty($checkOut) || $hotel_id <= 0) {
    CommonService::resJson(['error' => 'Dữ liệu đầu vào không hợp lệ']);
}

// Kiểm tra ngày hợp lệ
$checkInTime = strtotime(str_replace('/', '-', $checkIn));
$checkOutTime = strtotime(str_replace('/', '-', $checkOut));
if (!$checkInTime || !$checkOutTime || $checkOutTime <= $checkInTime) {
    CommonService::resJson(['error' => 'Ngày trả phòng phải sau ngày nhận phòng']);
}

$daterange = $checkIn . ' - ' . $checkOut;

// Lây các hạng phòng của KS
$rooms = DB::query("SELECT * FROM room WHERE roo_hotel_id = $hotel_id AND roo_active = 1")->toArray();
if (empty($rooms)) {
    CommonService::resJson(['error' => 'Không tìm thấy phòng nào cho khách sạn này']);
}
// Trả về mảng mỗi hạng phòng sẽ có thông tin: giá phòng, số lượng phòng trống
$data = [];
foreach($rooms as $room) {
    $info = HotelService::getRoomPriceAndAvailability($room['roo_hotel_id'], $daterange, $room['roo_id']);
    $data[] = [
        'room_id' => $room['roo_id'],
        'room_name' => $room['roo_name'],
        'price' => format_number($info['total_price']),
        'min_qty' => $info['min_qty'],
        'total_price' => $info['total_price'],
        'price_per_day' => $info['price_per_day'] // Thêm giá phòng theo từng ngày
    ];
}

CommonService::resJson($data);