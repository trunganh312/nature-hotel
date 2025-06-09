<?php
use src\Facades\DB;
use src\Models\Hotel;
use src\Models\HotelPicture;
use src\Models\Room;
use src\Models\RoomPicture;
use src\Services\HotelService;

define('PATH_ROOT', realpath(__DIR__ .'/..'));
define('PATH_CORE', PATH_ROOT . '/Core');
require_once(PATH_ROOT .'/vendor/autoload.php');

require_once(PATH_CORE . '/Config/constants.php');
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Nếu request là POST và có file upload (tên trường là 'file')
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $id = $_POST['id'] ?? null;
    $type = $_POST['type'] ?? null;
    if($type == 'hotel') {
        $hotel_id = $id;
        // Lấy thông tin khách sạn
        $hotel = Hotel::where('hot_id_mapping', $hotel_id)->getOne();
        if(!$hotel) {
            exit;
        }
        $uploadDir = realpath(__DIR__ .'/..') . '/uploads/hotel/' . $hotel['hot_id'] . '/';
    } else {
        $room_id = $id;
        $room = Room::where('roo_id_mapping', $room_id)->getOne();
        if(!$room) {
            exit;
        }
        $uploadDir = realpath(__DIR__ .'/..') . '/uploads/room/' . $room['roo_id'] . '/';
    }
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $file = $_FILES['file'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        exit;
    }
    if (!in_array($file['type'], $allowedTypes)) {
        exit;
    }
    $filename = basename($file['name']);
    $targetPath = $uploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        exit;
    }
    exit;
}
// --- Kết thúc xử lý upload file ảnh ---

/**
 * Class Webhook
 * Xử lý các webhook cho hệ thống Vietgoing CRM
 */
class Webhook {
    protected $logFile = 'webhook_log.txt';
    protected $secret = 'xd7SgxKleXxw2TXXauM5c';
    protected $app_id = '3075836128630070920';


    public function __construct() {
    }

    /**
     * Hàm chính để xử lý webhook
     * @param array $server
     * @param string $input
     */
    public function handle($server, $input)
    {
        // Lấy dữ liệu từ body
        $jsonPayload = base64_decode($input);
        if ($jsonPayload === false) {
            $this->writeLog('Lỗi giải mã Base64: ' . $input);
            $this->response(400, ['error' => 'Invalid Base64']);
        }

        // Giải mã JSON
        $data = json_decode($jsonPayload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->writeLog('Lỗi giải mã JSON: ' . $jsonPayload);
            $this->response(400, ['error' => 'Invalid JSON']);
        }

        // Lấy signature từ header
        $signature = isset($server['HTTP_X_SIGNATURE']) ? $server['HTTP_X_SIGNATURE'] : '';
        if (empty($signature)) {
            $this->writeLog('Thiếu header X-Signature');
            $this->response(400, ['error' => 'Missing X-Signature']);
        }

        // Lấy app_id từ payload
        $app_id = isset($data['app_id']) ? $data['app_id'] : '';
        if (empty($app_id)) {
            $this->writeLog('Thiếu app_id trong payload');
            $this->response(400, ['error' => 'Missing app_id']);
        }

        if($app_id != $this->app_id) {
            $this->writeLog('app_id không khớp: ' . $app_id);
            $this->response(400, ['error' => 'Invalid app_id']);
        }

        // Lấy secret từ DB theo app_id (bảo mật hơn)
        $secret = $this->secret;

        // Xác thực signature
        $expectedSignature = hash_hmac('sha256', $input, $secret);
        
        if (!hash_equals($expectedSignature, $signature)) {
            $this->writeLog('Signature không khớp');
            $this->response(401, ['error' => 'Invalid signature']);
        }
        // Xử lý sự kiện
        $event = isset($data['event']) ? $data['event'] : '';
        $payload_data = array_diff_key($data, array_flip(['app_id', 'event', 'timestamp']));
        $this->writeLog('Data: ' . json_encode($payload_data));
        $data = $payload_data['data'];

        try {
            switch ($event) {
                // Nhận và lưu ảnh từ webhook
                case 'update_image':
                    $this->updateImage($data);
                    break;
                // Cập nhật thông tin khách sạn
                case 'update_hotel':
                    $this->handleUpdateHotel($data);
                    break;
                case 'update_room':
                    $this->handleRoom($data, $event);
                    break;
                case 'add_room':
                    $this->handleRoom($data, $event);
                    break;
                // Cập nhật số phòng trống theo ngày
                case 'update_room_available':
                    $this->handleUpdateRoomAvailable($data);
                    break;
                case 'update_total_room':
                    $this->handleUpdateTotalRoom($data);
                    break;
                case 'update_price':
                    $this->updatePrice($data);
                    break;
                case 'info_attribute':
                    $this->updateAttribute($data);
                    break;
                case 'delete_image':
                    $this->deleteImage($data);
                    break;
                default:
                    $this->writeLog('Sự kiện không được hỗ trợ: ' . $event);
                    $this->writeLog('Sự kiện không được hỗ trợ: ' . json_encode($data));
                    $this->response(400, ['error' => 'Unsupported event']);
            }
            $this->response(200, ['status' => 'success', 'message' => 'Webhook processed']);
        } catch (\Throwable $e) {
            $this->writeLog('Lỗi xử lý webhook: ' . $e->getMessage());
            $this->response(500, ['error' => 'Internal server error']);
        }
    }

    /**
     * Ghi log ra file
     * @param string $message
     */
    protected function writeLog($message)
    {
        $timestamp = date('d/m/Y H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    /**
     * Trả về response JSON
     * @param int $code
     * @param array $data
     */
    protected function response($code, $data)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Xử lý cập nhật tên khách sạn
     * @param array $data
     */
    protected function handleUpdateHotel($data)
    {
        if (!$data) {
            $this->writeLog('Thiếu dữ liệu cho event: update_hotel');
            $this->response(400, ['error' => 'Missing data']);
        }
        // Check xem KS đã tồn tại trong DB hay chưa
        $hotel = DB::query("SELECT * FROM hotel WHERE hot_id_mapping = {$data['hot_id']}")->getOne();
        if (!$hotel) {
            $this->writeLog('Khách sạn không tồn tại trong DB: ' . $data['hot_id']);
            $payload = $data;
            $payload['hot_id_mapping'] = $data['hot_id'];
            $payload['hot_time_create'] = time();
            unset($payload['hot_id']);
            unset($payload['hot_company_id']);
            Hotel::insert($payload);
        } else {
            // Cập nhật
            $payload = $data;
            unset($payload['hot_id']);
            unset($payload['hot_time_create']);
            unset($payload['hot_company_id']);
            $payload['hot_last_update'] = time();
            Hotel::update($payload, ['hot_id' => $hotel['hot_id']]);
        }
    }

    /**
     * Xử lý cập nhật hạng phòng cập nhật hạng phòng thì cập nhật cả ở bảng room_sennet để mapping
     * @param array $data
     */
    protected function handleRoom($data, $event)
    {
        if (!$data) {
            $this->writeLog('Thiếu dữ liệu cho event: update_room');
            $this->response(400, ['error' => 'Missing data']);
        }
        $room = $data['room'];
        $beds = $data['beds'] ?? [];
        $hotel_id_mapping = $room['roo_hotel_id'];
        // Lấy thông tin khách sạn
        $hotel = Hotel::where('hot_id_mapping', $hotel_id_mapping)->getOne();
        if (!$hotel) {
            $this->writeLog('Khách sạn không tồn tại trong DB: ' . $hotel_id_mapping);
            $this->response(400, ['error' => 'Hotel not found']);
        }
        $room_info = Room::where(['roo_id_mapping' => $room['roo_id'], 'roo_hotel_id' => $hotel['hot_id']])->getOne();
        $id =  $room_info ? $room_info['roo_id'] : '';
        if (!$room_info) {
            // Thêm hạng phòng
            $payload = $room;
            $payload = array_merge($payload, ['roo_hotel_id' => $hotel['hot_id'], 'roo_id_mapping' => $payload['roo_id']]);
            unset($payload['roo_id']);
            $this->writeLog('Thêm hạng phòng: ' . json_encode($payload));
            $id = Room::insert($payload);
        } else {
            // Cập nhật hạng phòng
            $payload = $room;
            unset($payload['roo_id']);
            unset($payload['roo_hotel_id']);
            Room::update($payload, ['roo_id_mapping' => $room['roo_id']]);
        }

        if($beds) {
            // Thêm hạng giường
            // Xóa các bản ghi hiện tại trước
            DB::query("DELETE FROM room_bed WHERE robe_room_id = $id");
            // Thêm các hạng giường mới
            foreach ($beds as $bed_id => $qty) {
                DB::query("INSERT INTO room_bed (robe_room_id, robe_bed_id, robe_quantity, robe_hotel_id) VALUES ($id, {$bed_id}, {$qty}, {$hotel['hot_id']})");
            }
        }
    }


    /**
     * Xử lý cập nhật số phòng trống
     * @param array $payload_data
     */
    protected function handleUpdateRoomAvailable($data)
    {
        if (!$data) {
            $this->writeLog('Thiếu dữ liệu cho event: update_room_available');
            $this->response(400, ['error' => 'Missing data']);
        }
        $rooms_available = $data['rooms_available'];
        $hotel_id   = (int)$data['hotel_id'];
        $this->writeLog("Cập nhật số phòng trống " . json_encode($data));
        if (!is_array($rooms_available) || empty($rooms_available)) {
            $this->writeLog('booking_all không phải mảng hoặc rỗng: ' . json_encode($rooms_available));
            return;
        }
        try {
            $hotel_info = DB::query("SELECT * FROM hotel WHERE hot_id_mapping = {$hotel_id}")->getOne();
            if (!$hotel_info) return;
            $rooms = DB::query("SELECT roo_id, roo_id_mapping FROM room WHERE roo_hotel_id = {$hotel_info['hot_id']} AND roo_id_mapping > 0")->toArray();
            $this->writeLog("Cập" . json_encode($rooms));
            $room_mapping = [];
            foreach ($rooms as $map) {
                $room_mapping[$map['roo_id_mapping']] = $map['roo_id'];
            }
            $mapped_rooms_available = [];
            foreach ($rooms_available as $hms_room_id => $availability) {
                if (isset($room_mapping[$hms_room_id])) {
                    $ta_room_id = $room_mapping[$hms_room_id];
                    $mapped_rooms_available[$ta_room_id] = $availability;
                } else {
                    unset($mapped_rooms_available[$hms_room_id]);
                }
            }
            if (empty($mapped_rooms_available)) return;
            foreach ($mapped_rooms_available as $room_id => $values) {
                $current_time = time();
                foreach ($values as $time => $qty) {
                    $tbl = HotelService::getTablePartitionRoomPrice('room_price', $hotel_info["hot_id"], $time);
                    HotelService::existTableRoomPrice($tbl, true);
                    // Cập nhật tạm giá bán trước
                    $row = DB::query("SELECT * FROM `" . $tbl . "` WHERE `rop_room_id` = {$room_id} AND `rop_day` = {$time}")->getOne();
                    if ($row) {
                        DB::query("UPDATE `" . $tbl . "` SET `rop_qty` = {$qty}, `rop_created_at` = {$current_time} WHERE `rop_room_id` = {$room_id} AND `rop_day` = {$time}");
                    } else {
                        DB::query("INSERT INTO `" . $tbl . "` (`rop_price`, `rop_currency_unit`, `rop_type`, `rop_room_id`, `rop_qty`, `rop_hotel_id`, `rop_day`, `rop_created_by`, `rop_created_at`) VALUES (1, 1, 0, {$room_id}, {$qty}, {$hotel_info['hot_id']}, {$time}, 0, {$current_time})");
                    }
                    $this->writeLog("Cập nhật số phòng trống 1" . json_encode($mapped_rooms_available));
                }
            }
        } catch (\Throwable $th) {
            $this->writeLog("Cập nhật số phòng trống " . $th->getMessage());
        }
    }

     /**
     * Xử lý cập nhật số lượng phòng
     * @param array $data
     */
    protected function handleUpdateTotalRoom($data)
    {
        if (!$data) {
            $this->writeLog('Thiếu dữ liệu cho event: update_total_rooms');
            $this->response(400, ['error' => 'Missing data']);
        }
        $rooms = isset($data['rooms']) ? $data['rooms'] : [];
        if ($rooms && is_array($rooms)) {
            foreach ($rooms as $id => $info) {
                $total_rooms = (int) $info['total_rooms'];
                $changed_rooms = (int) $info['changed_rooms'];
                $this->updateSingleRoomQuantity($id, $total_rooms, $changed_rooms);
            }
            return;
        }
    }

    /**
     * Cập nhật số lượng phòng trống
     * @param int $id
     * @param int $total_rooms
     * @param int $changed_rooms
     */
    protected function updateSingleRoomQuantity($id, $total_rooms, $changed_rooms)
    {
        $this->writeLog("Cập nhật số lượng phòng cho id={$id}, Số phòng tổng={$total_rooms}, Số phòng giảm/ tăng={$changed_rooms}");
        try {
            if (empty($id) || !is_numeric($id)) {
                $this->writeLog("Bỏ qua cập nhật: id mapping không hợp lệ ({$id})");
                return;
            }
            if ($total_rooms >= 0) {
                DB::query("UPDATE room SET roo_quantity = {$total_rooms} WHERE roo_id_mapping = {$id}");
            }
            $room_info = DB::query("SELECT hot_id, roo_id FROM room INNER JOIN hotel ON roo_hotel_id = hot_id WHERE roo_id_mapping = {$id}")->getOne();
            if (!$room_info) return;
            $hotel_id = $room_info['hot_id'];
            $room_id = $room_info['roo_id'];
            $max_days = 365;
            $today = strtotime(date('Y-m-d'));
            for ($i = 0; $i < $max_days; $i++) {
                $day = $today + $i * 86400;
                $tbl = HotelService::getTablePartitionRoomPrice('room_price', $hotel_id, $day);
                if (!HotelService::existTableRoomPrice($tbl, false)) {
                    $this->writeLog("Bỏ qua ngày " . date('Y-m-d', $day) . " cho room_id={$room_id} vì không có bảng partition: {$tbl}");
                    continue;
                }
                // Mặc định cập nhật giá bán ra trước
                $row = DB::query("SELECT * FROM `" . $tbl . "` WHERE `rop_room_id` = {$room_id} AND `rop_day` = {$day}")->getOne();
                if (!$row) {
                    $this->writeLog("Bỏ qua ngày " . date('Y-m-d', $day) . " cho room_id={$room_id} vì không có dữ liệu tồn: {$tbl}");
                    continue;
                }
                if ($row) {
                    $new_qty = $row['rop_qty'] + $changed_rooms;
                    DB::query("UPDATE `" . $tbl . "` SET `rop_qty` = {$new_qty} WHERE `rop_room_id` = {$room_id} AND `rop_day` = {$day}");
                    $this->writeLog("Trừ tồn phòng room_id={$room_id} ngày={$day}, tồn cũ={$row['rop_qty']}, tồn mới={$new_qty}");
                }
            }
        } catch (\Throwable $th) {
            $this->writeLog("Cập nhật số lượng phòng " . $th->getMessage());
        }
    }

     /**
     * Cập nhật giá phòng
     * @param int $id
     * @param int $total_rooms
     * @param int $changed_rooms
     */
    protected function updatePrice($data)
    {
        if (!$data || !is_array($data)) {
            $this->writeLog('Dữ liệu updatePrice không hợp lệ: ' . json_encode($data));
            return;
        }
        foreach ($data as $room_id_mapping => $dates) {
            $room = DB::query("SELECT roo_id, roo_hotel_id, roo_quantity FROM room WHERE roo_id_mapping = {$room_id_mapping}")->getOne();
            if (!$room) {
                $this->writeLog("Không tìm thấy room với roo_id_mapping={$room_id_mapping}");
                continue;
            }
            $broken = $dates['broken_rooms'] ?? 0;
            $roo_id = $room['roo_id'];
            $hotel_id = $room['roo_hotel_id'];
            $qty = $room['roo_quantity'] - $broken;
            foreach ($dates['prices'] as $timestamp => $price) {
                $timestamp = (int) $timestamp;
                $tbl = HotelService::getTablePartitionRoomPrice('room_price', $hotel_id, $timestamp);
                HotelService::existTableRoomPrice($tbl, true);
                $row = DB::query("SELECT * FROM `{$tbl}` WHERE `rop_room_id` = {$roo_id} AND `rop_day` = {$timestamp}")->getOne();
                if ($row) {
                    DB::query("UPDATE `{$tbl}` SET `rop_price` = {$price} WHERE `rop_room_id` = {$roo_id} AND `rop_day` = {$timestamp}");
                    $this->writeLog("Đã cập nhật giá phòng: room_id={$roo_id}, ngày={$timestamp}, giá mới={$price}");
                } else {
                    // Insert mới nếu chưa có
                    DB::query("INSERT INTO `{$tbl}` (`rop_price`, `rop_currency_unit`, `rop_type`, `rop_room_id`, `rop_qty`, `rop_hotel_id`, `rop_day`, `rop_created_by`, `rop_created_at`) VALUES ({$price}, 1, 0, {$roo_id}, {$qty}, {$hotel_id}, {$timestamp}, 0, " . CURRENT_TIME . ")");
                    $this->writeLog("Đã thêm mới giá phòng: room_id={$roo_id}, ngày={$timestamp}, giá={$price}");
                }
            }
        }
    }

    protected function processAttributeName($data, $mode)
    {
        $atn_id = $data['atn_id'];
        if ($mode == 'update') {
            DB::query("UPDATE attribute_name SET atn_attribute_id = {$data['atn_attribute_id']},  
                                                    atn_group = {$data['atn_group']}, atn_column = {$data['atn_column']}, 
                                                    atn_name = {$data['atn_name']}, atn_type = {$data['atn_type']}, 
                                                    atn_canonical = {$data['atn_canonical']}, atn_alias_search = {$data['atn_alias_search']}, 
                                                    atn_show_filter = {$data['atn_show_filter']}, atn_active = {$data['atn_active']}, 
                                                    atn_hot = {$data['atn_hot']}, atn_note = {$data['atn_note']}, 
                                                    atn_order = {$data['atn_order']}, atn_join_meta = {$data['atn_join_meta']}, 
                                                    atn_text_join_meta = {$data['atn_text_join_meta']} WHERE atn_id = {$atn_id}");
        } else {
            DB::query("INSERT INTO attribute_name (atn_attribute_id, atn_group, atn_column, 
                                                    atn_name, atn_type, atn_canonical, atn_alias_search, 
                                                    atn_show_filter, atn_active, atn_hot, atn_note, 
                                                    atn_order, atn_join_meta, atn_text_join_meta) 
                                                    VALUES ({$data['atn_attribute_id']}, {$data['atn_group']}, 
                                                    {$data['atn_column']}, {$data['atn_name']}, {$data['atn_type']}, 
                                                    {$data['atn_canonical']}, {$data['atn_alias_search']}, 
                                                    {$data['atn_show_filter']}, {$data['atn_active']}, {$data['atn_hot']}, 
                                                    {$data['atn_note']}, {$data['atn_order']}, {$data['atn_join_meta']}, 
                                                    {$data['atn_text_join_meta']})");
        }
    }

    protected function processAttributeValue($data, $mode)
    {
        $atv_id = $data['atv_id'];
        if ($mode == 'update') {
            DB::query("UPDATE attribute_value SET atv_attribute_id = {$data['atv_attribute_id']},  
                                                    atv_name = {$data['atv_name']}, atv_value_hexa = {$data['atv_value_hexa']}, 
                                                    atv_keyword_text = {$data['atv_keyword_text']}, 
                                                    atv_show_position = {$data['atv_show_position']}, 
                                                    atv_show_keyword = {$data['atv_show_keyword']}, 
                                                    atv_active = {$data['atv_active']}, 
                                                    atv_hot = {$data['atv_hot']}, 
                                                    atv_fee = {$data['atv_fee']}, 
                                                    atv_icon = {$data['atv_icon']}, 
                                                    atv_order = {$data['atv_order']} WHERE atv_id = {$atv_id}");
        } else {
            DB::query("INSERT INTO attribute_value (atv_attribute_id, atv_name, atv_value_hexa, 
                                                    atv_keyword_text, atv_show_position, atv_show_keyword, 
                                                    atv_active, atv_hot, atv_fee, atv_icon, atv_order) 
                                                    VALUES ({$data['atv_attribute_id']}, {$data['atv_name']}, 
                                                    {$data['atv_value_hexa']}, {$data['atv_keyword_text']}, 
                                                    {$data['atv_show_position']}, {$data['atv_show_keyword']}, 
                                                    {$data['atv_active']}, {$data['atv_hot']}, {$data['atv_fee']}, 
                                                    {$data['atv_icon']}, {$data['atv_order']})");
        }
    }

    /**
     * Cập nhật thông tin thuộc tính
     * @param array $data
     */
    protected function updateAttribute($data)
    {
        if (!$data) {
            $this->writeLog('Thiếu dữ liệu cho event: update_attribute');
            $this->response(400, ['error' => 'Missing data']);
        }

        $this->writeLog('update_attribute: ' . json_encode($data));
        $mode = $data['mode'];
        $table = $data['table'];
        $data = $data['data'];
        switch ($table) {
            case 'attribute_name':
                $this->processAttributeName($data, $mode);
                break;
            case 'attribute_value':
                $this->processAttributeValue($data, $mode);
                break;
        }
    }

     /**
     * Cập nhật ảnh khách sạn   
     * @param array $data
     */
    protected function updateImage($data)
    {
        $this->writeLog('update_image: ' . json_encode($data));
        $type = $data['type'];
        $id = $data['id'];
        $picture_main = $data['picture_main'];
        $list_picture = $data['list_picture'];
        if ($type == 'hotel') {
            // Lấy thông tin khách sạn
            $hotel = DB::query("SELECT * FROM hotel WHERE hot_id_mapping = {$id}")->getOne();
            if (!$hotel) {
                $this->writeLog("Không tìm thấy khách sạn với id={$id}");
                return;
            }
            $this->updateHotelImage($hotel['hot_id'], $picture_main, $list_picture);
        } else {
            // Lấy thông tin phòng
            $room = DB::query("SELECT * FROM room WHERE roo_id_mapping = {$id}")->getOne();
            if (!$room) {
                $this->writeLog("Không tìm thấy phòng với id={$id}");
                return;
            }
            $this->updateRoomImage($room['roo_id'], $picture_main, $list_picture);
        }
    }

    protected function updateHotelImage($id, $picture_main, $list_picture) {
        // Update ảnh khách sạn
        $picture_main = $picture_main;
        DB::query("UPDATE hotel SET hot_picture = '{$picture_main}' WHERE hot_id = {$id}");

        // Update ảnh phụ
        DB::query("DELETE FROM hotel_picture WHERE  hopi_hotel_id = {$id}");
        foreach ($list_picture as $picture) {
            $image = $picture['hopi_picture'];
            $order = $picture['hopi_order'];
            $group = $picture['hopi_group'];
            DB::query("INSERT INTO hotel_picture (hopi_hotel_id, hopi_picture, hopi_order, hopi_group) VALUES ({$id}, '{$image}', {$order}, {$group})");
        }
        // Log
        $this->writeLog("Cập nhật ảnh khách sạn: id={$id}, picture_main={$picture_main}, list_picture=" . json_encode($list_picture));
    }

    protected function updateRoomImage($id, $picture_main, $list_picture) {
        $picture_main = $picture_main;
        // Update ảnh phòng
        DB::query("UPDATE room SET roo_picture = '{$picture_main}' WHERE roo_id = {$id}");

        // Update ảnh phụ
        DB::query("DELETE FROM room_picture WHERE  rop_room_id = {$id}");
        foreach ($list_picture as $picture) {
            $image = $picture['rop_picture'];
            $order = $picture['rop_order'];
            DB::query("INSERT INTO room_picture (rop_room_id, rop_picture, rop_order) VALUES ({$id}, '{$image}', {$order})");
        }
        // Log
        $this->writeLog("Cập nhật ảnh phòng: id={$id}, picture_main={$picture_main}, list_picture=" . json_encode($list_picture));
    }

      /**
     * Xóa ảnh hạng phòng và KS
     * @param array $data
     */
    protected function deleteImage($data) {
        $this->writeLog("Xóa ảnh: " . json_encode($data));
        $type = $data['type'];
        $id = $data['id'];
        $picture = $data['picture'];
        if ($type == 'hotel') {
            $this->deleteHotelImage($id, $picture);
        } else {
            $this->deleteRoomImage($id, $picture);
        }
    }

    protected function deleteHotelImage($id, $picture) {
        // Lấy khách sạn
        $hotel = DB::query("SELECT * FROM hotel WHERE hot_id_mapping = {$id}")->getOne();
        if (!$hotel) {
            $this->writeLog("Không tìm thấy khách sạn với id={$id}");
            return;
        }
        // Xóa trong DB
        DB::query("DELETE FROM hotel_picture WHERE hopi_hotel_id = {$hotel['hot_id']} AND hopi_picture = '{$picture}'");
        // Xóa ảnh trong thư mục
        $uploadDir = realpath(__DIR__ .'/..') . '/uploads/hotel/' . $hotel['hot_id'] . '/' . $picture;
        if (file_exists($uploadDir)) {
            unlink($uploadDir);
        }
    }

    protected function deleteRoomImage($id, $picture) {
        // Lấy phòng
        $room = DB::query("SELECT * FROM room WHERE roo_id_mapping = {$id}")->getOne();
        if (!$room) {
            $this->writeLog("Không tìm thấy phòng với id={$id}");
            return;
        }
        // Xóa trong DB
        DB::query("DELETE FROM room_picture WHERE rop_room_id = {$room['roo_id']} AND rop_picture = '{$picture}'");
        // Xóa ảnh trong thư mục
        $uploadDir = realpath(__DIR__ .'/..') . '/uploads/hotel/' . $room['roo_id'] . '/' . $picture;
        if (file_exists($uploadDir)) {
            unlink($uploadDir);
        }
    }
}
$input = file_get_contents('php://input');
$handler = new Webhook();
$handler->handle($_SERVER, $input);
