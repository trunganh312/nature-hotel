<?php

use src\Models\BookingHotel;
use src\Models\BookingRoomItem;
use src\Models\Hotel;
use src\Models\Room;
use src\Models\RoomItem;
use src\Services\BookingHotelService;
use src\Services\CommonService;
use src\Services\RoomItemFlowService;

$disable_save_log_access = true;
include(__DIR__ . '/../config_module.php');

// Lấy tham số từ URL
$ID = getValue('ID');
$company_id = getValue('company');
$hotel_id = getValue('hotel_id');
$errors = [];

// Kiểm tra tham số bắt buộc
if (is_null($ID) || empty($ID)) {
    $errors[] = "Vui lòng cung cấp ID phòng.";
}
if (is_null($company_id) || empty($company_id)) {
    $errors[] = "Vui lòng cung cấp company_id.";
}
if (is_null($hotel_id) || empty($hotel_id)) {
    $errors[] = "Vui lòng cung cấp hotel_id.";
}
if (!empty($errors)) {
    CommonService::resJson(['success' => 0, 'data' => $errors]);
    exit;
}

$record_info = RoomItem::select('roi_id', 'roi_name', 'roo_code', 'roo_id', 'roo_adult', 'roi_status', 'roi_booking_current', 'roo_name', 'roo_max_adult', 'roo_max_people')
    ->join(Room::table(), 'roi_room_id', 'roo_id')
    ->join(Hotel::table(), 'roi_hotel_id', 'hot_id')
    ->where('roi_id', $ID)
    ->where('roi_hotel_id', $hotel_id)
    ->where('hot_company_id', $company_id)
    ->getOne();

if (!$record_info) {
    CommonService::resJson(['success' => 0, 'data' => "Không tìm thấy thông tin phòng hoặc phòng không thuộc khách sạn/công ty này."]);
    exit;
}

// Lấy booking của phòng theo thời gian hiện tại
$booking = null;
if ((int)$record_info['roi_status'] === RoomItem::STT_EMPTY) {
    $booking = BookingRoomItem::select('bkho_id', 'bri_id', 'bkho_status', 'bkho_code', 'bkho_name', 'bkho_source', 'bri_checkin', 'bri_checkout', 'bri_adult', 'bri_baby', 'bri_children')
        ->join(BookingHotel::table(), 'bri_booking_id', 'bkho_id')
        ->where('bri_room_item_id', $record_info['roi_id'])
        ->where('bri_checkin', '<=', strtotime('today 23:59:59'))
        ->where('bri_checkout', '>=', strtotime('today'))
        ->where('bri_active', STATUS_ACTIVE)
        ->where('bkho_hotel_id', $hotel_id)
        ->where('bkho_company_id', $company_id)
        ->orderBy('bri_checkin', 'ASC')
        ->getOne();
    if ($booking) {
        if ($booking['bkho_status'] === STT_COMPLETE && $record_info['roi_status'] === RoomItem::STT_EMPTY) {
            BookingRoomItem::pass()->update(
                ['bri_room_item_id' => 0, 'bri_status' => RoomItem::STT_EMPTY],
                ['bri_booking_id' => $booking['bkho_id'], 'bri_room_item_id' => $ID]
            );
            RoomItem::pass()->update(
                ['roi_status' => RoomItem::STT_EMPTY, 'roi_booking_current' => 0],
                ['roi_id' => $ID]
            );
            $record_info['roi_status'] = RoomItem::STT_EMPTY;
        } else {
            $record_info['roi_status'] = RoomItem::STT_BOOKED;
        }
    }
} else if ($record_info['roi_booking_current'] > 0) {
    $booking_today = BookingRoomItem::pass()->select('bri_checkin')
        ->where('bri_checkout', '>=', strtotime('today'))
        ->where('bri_checkin', '<', strtotime('today 23:59:59'))
        ->where('bri_room_item_id', $record_info['roi_id'])
        ->where('bri_active', STATUS_ACTIVE)
        ->toArray();
    $booking = BookingHotel::select('bkho_id', 'bri_id', 'bkho_code', 'bkho_status', 'bkho_name', 'bkho_source', 'bri_checkin', 'bri_checkout', 'bri_adult', 'bri_baby', 'bri_children')
        ->join(BookingRoomItem::table(), 'bri_booking_id', 'bkho_id')
        ->where('bri_room_item_id', $record_info['roi_id'])
        ->where('bkho_id', $record_info['roi_booking_current'])
        ->where('bkho_hotel_id', $hotel_id)
        ->where('bkho_company_id', $company_id)
        ->getOne();
    if ($booking && $booking['bkho_status'] === STT_COMPLETE && $record_info['roi_status'] === RoomItem::STT_EMPTY) {
        BookingRoomItem::pass()->update(
            ['bri_room_item_id' => 0, 'bri_status' => RoomItem::STT_EMPTY],
            ['bri_booking_id' => $booking['bkho_id'], 'bri_room_item_id' => $ID]
        );
        RoomItem::pass()->update(
            ['roi_status' => RoomItem::STT_EMPTY, 'roi_booking_current' => 0],
            ['roi_id' => $ID]
        );
        $record_info['roi_status'] = RoomItem::STT_EMPTY;
    }
}

if ($booking) {
    $booking['source_label'] = array_get($cfg_data_source_hms, $booking['bkho_source']);
    if ($booking['bkho_source'] == SOURCE_OTA) {
        $booking['source_label'] = array_get($cfg_source_ota, $booking['bkho_source']);
    }
    $booking['checkin'] = date('d/m H:i', $booking['bri_checkin']);
    $booking['checkout'] = date('d/m H:i', $booking['bri_checkout']);
}

$record_info['bg'] = array_get(RoomItem::$status_bg_color, (int) $record_info['roi_status'], RoomItem::STT_EMPTY);
$record_info['icon'] = array_get(RoomItemFlowService::$status_icon, (int) $record_info['roi_status']);
$record_info['status_text'] = array_get(RoomItemFlowService::$status_label, (int) $record_info['roi_status']);
$record_info['checkout'] = isset($booking) && $record_info['roi_booking_current'] > 0 && date('dmY', $booking['bri_checkout']) == date('dmY') && $record_info['roi_status'] !== RoomItem::STT_EMPTY;
$record_info['checkout_miss'] = isset($booking) && $record_info['roi_booking_current'] > 0 
    && date('dmY', $booking['bri_checkout']) == date('dmY') 
    && $booking['bri_checkout'] < CURRENT_TIME 
    && $record_info['roi_status'] == RoomItem::STT_STAY;
$record_info['checkin_today'] = isset($booking_today) && count($booking_today) == 2;
$record_info['time_checkin_today'] = isset($booking_today) && count($booking_today) == 2 ? date('H:i', $booking_today[1]['bri_checkin']) : '';
$record_info['room_flow'] = RoomItemFlowService::getNodesFlow((int) $record_info['roi_status']);

CommonService::resJson([
    'room' => $record_info,
    'booking' => $booking,
]);