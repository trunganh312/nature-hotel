<?
namespace src\Services;
use Exception;
use src\Facades\DB;
/**
 * Class BookingModel
 * Created by Vietgoing 
 **/
class BookingModel
{

    /**
     * BookingModel::holdBookingHotel()
     * Hold booking hotel
     * @param mixed $data_booking
     * @param mixed $data_rooms
     * @param mixed $remaining_seconds
     * @return $response[status, message]
     */
    function holdBookingHotel($data_booking = [], $data_rooms = [], $remaining_seconds = 0)
    {
        // Nếu k có booking info thì return
        if (empty($data_booking)) {
            return ['error' => 1, 'message' => 'Booking không tồn tại'];
        }
        // Nếu k có phòng thì return
        if (empty($data_rooms)) {
            return ['error' => 1, 'message' => 'Phòng không tồn tại'];
        }

        $rooms = [];
        // Xóa bảng hold tạm
        DB::query("DELETE FROM room_hold_temp WHERE roht_booking_id = " . $data_booking['bkho_id']);
        // Lặp qua rooms
        foreach ($data_rooms as $room) {
            $rooms[$room['roo_id_mapping']] = $room['bhr_qty'];
            // Lưu vào bảng hold tạm ở VG trước
            DB::execute("INSERT INTO room_hold_temp (roht_booking_id, roht_hotel_id, roht_room_id, roht_qty, roht_checkin, roht_checkout, roht_day) VALUES (" . $data_booking['bkho_id'] . ", " . $data_booking['hot_id'] . ", " . $room['roo_id'] . ", " . $room['bhr_qty'] . ", " . $data_booking['bkho_checkin'] . ", " . $data_booking['bkho_checkout'] . ", " . $remaining_seconds . ")");
        }

        // Tạo formdata gửi cho KS giữ phòng
        $form_data = [
            'booking_id' => $data_booking['bkho_id'],
            'rooms' => $rooms,
            'time' => $remaining_seconds,
            'hotel_id' => $data_booking['hot_id_mapping'],
            'checkin' => $data_booking['bkho_checkin'],
            'checkout' => $data_booking['bkho_checkout'],
        ];

        return Sennet::requestHoldRoom($form_data);
    }

    /**
     * BookingModel::unHoldBookingHotel()
     * Hold booking hotel
     * @param mixed $booking_info
     * @return $response[status, message]
     */
    function unHoldBookingHotel($booking_info)
    {
        // Nếu k có booking info thì return
        if (empty($booking_info)) {
            return ['error' => 1, 'message' => 'Booking không tồn tại'];
        }

        // Xóa bảng hold tạm
        DB::query("DELETE FROM room_hold_temp WHERE roht_booking_id = " . $booking_info['bkho_id']);

        // Tạo formdata gửi cho KS giữ phòng
        $form_data = [
            'booking_id' => $booking_info['bkho_id'],
            'hotel_id' => $booking_info['hot_id_mapping'],
        ];

        return Sennet::requestUnHoldRoom($form_data);
    }

    /**
     * BookingModel::sendBookingToSennet()
     * Send booking to Sennet
     * @param mixed $data_booking
     */
    function sendBookingToSennet($data_booking = [])
    {
        if (empty($data_booking)) {
            return ['status' => false, 'message' => 'Booking không tồn tại'];
        }
        $booking_id = $data_booking['bkho_id'];
        $hotel_id = $data_booking['bkho_hotel_id'];
        $hotel_info = DB::query("SELECT * FROM hotel WHERE hot_id = $hotel_id")->getOne();
        if (empty($hotel_info)) {
            return ['status' => false, 'message' => 'Khách sạn không tồn tại'];
        }
        $hotel_id_mapping = $hotel_info['hot_id_mapping'];
        $booking_rooms = DB::query("SELECT * FROM booking_hotel_room 
                                    INNER JOIN room ON (bhr_room_id = roo_id)
                                    WHERE bhr_booking_hotel_id = $booking_id")->toArray();
        if (empty($booking_rooms)) {
            return ['status' => false, 'message' => 'Phòng không tồn tại'];
        }
        $rooms = [];
        foreach ($booking_rooms as $room) {
            $rooms[$room['roo_id_mapping']][] = [
                'room_id' => $room['roo_id_mapping'],
                'qty' => 1,
                'price' => $room['bhr_price'],
                'adults' => $data_booking['bkho_adult'] ?? 0,
                'children' => $data_booking['bkho_children'] ?? 0,
                'infants' => $data_booking['bkho_baby'] ?? 0
            ];
        }
        $data_booking['hotel_id_mapping'] = $hotel_id_mapping;
        $data_booking['rooms'] = $rooms;
        $status_failed = STT_CANCEL;
        try {
            $status = STT_SUCCESS;
            $current_time = CURRENT_TIME;
            DB::query("UPDATE booking_hotel SET bkho_status = $status, bkho_time_success = $current_time, bkho_admin_process = 1 WHERE bkho_id = $booking_id");
            DB::query("DELETE FROM room_hold_temp WHERE roht_booking_id = $booking_id");
            BookingModel::subtractRoomInventory($data_booking, $booking_rooms);
            return Sennet::sendBookingSennet($data_booking);
        } catch (Exception $e) {
            DB::query("UPDATE booking_hotel SET bkho_status = $status_failed WHERE bkho_id = $booking_id");
            return ['status' => false, 'message' => 'Lỗi xử lý booking: ' . $e->getMessage()];
        }
    }
    /**
     * Trừ tồn phòng thực tế khi đơn thành công (sau thanh toán)
     * @param array $data_booking
     *
     * Lặp qua từng ngày từ checkin đến trước checkout, trừ đúng số lượng bhr_qty vào partition tồn phòng
     */
    public static function subtractRoomInventory($data_booking, $rooms = [])
    {
        if (empty($rooms) || empty($data_booking['bkho_checkin']) || empty($data_booking['bkho_checkout']))
            return;
        $hotel_id = isset($data_booking['hot_id']) ? $data_booking['hot_id'] : (isset($data_booking['bkho_hotel_id']) ? $data_booking['bkho_hotel_id'] : 0);
        $checkin = $data_booking['bkho_checkin'];
        $checkout = $data_booking['bkho_checkout'];
        try {
            foreach ($rooms as $room) {
                if (empty($room['bhr_room_id']) || empty($room['bhr_qty']))
                    continue;
                $room_id = (int) $room['bhr_room_id'];
                $qty = (int) $room['bhr_qty'];
                for ($day = $checkin; $day < $checkout; $day += 86400) {
                    $tbl = HotelService::getTablePartitionRoomPrice('room_price', $hotel_id, $day);
                    if (!HotelService::existTableRoomPrice($tbl, false))
                        continue;
                    $row = DB::query("SELECT rop_qty FROM `{$tbl}` WHERE rop_room_id = $room_id AND rop_day = $day")->getOne();
                    if (!$row)
                        continue;
                    $new_qty = $row['rop_qty'] - $qty;
                    if ($new_qty < 0) {
                        throw new Exception("Số lượng phòng $room_id không đủ cho ngày " . date('d/m/Y', $day));
                    }
                    DB::execute("UPDATE `{$tbl}` SET rop_qty = $new_qty WHERE rop_room_id = $room_id AND rop_day = $day");
                }
            }
        } catch (Exception $e) {
            // Ghi log lỗi
            error_log("Lỗi trừ tồn phòng: " . $e->getMessage());
            throw $e; // Ném lại để caller xử lý
        }
    }
}
?>