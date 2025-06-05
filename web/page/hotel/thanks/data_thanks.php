<?
include('../../../Core/Config/require_web.php');
include('../../../libraries/qr_payment/src/QRPayment.php');
require_once('../../../libraries/payos/vendor/autoload.php');
include('../../../libraries/payos/src/PayOSWrapper.php');
use Vietgoing\PayOS\PayOSWrapper;
$BookingModel   =   new BookingModel;
$payOS = new PayOSWrapper(
    PAYOS_CLIENT_ID,
    PAYOS_API_KEY, 
    PAYOS_CHECKSUM_KEY
);

$cancel     = getValue('cancel', GET_STRING, GET_GET, '');
$status     = getValue('status', GET_STRING, GET_GET, '');
$booking_id =   getValue('booking_completed', GET_INT, GET_SESSION, getValue('booking_completed', GET_INT, GET_GET, 0));
$booking_info   =   $DB->query("SELECT booking_hotel.*, hot_id, hot_name, hot_picture, hot_checkin, hot_checkout, 
                                hot_type, hot_address_full, hot_star, hot_ota, hot_id_mapping
                                FROM booking_hotel
                                INNER JOIN hotel ON bkho_hotel_id = hot_id
                                WHERE bkho_id = " . $booking_id)->getOne();

$rooms = $DB->query("SELECT * FROM booking_hotel_room 
                        INNER JOIN room ON (bhr_room_id = roo_id)
                        WHERE bhr_booking_hotel_id = $booking_id")->toArray();

foreach($rooms as $k => $row) {
    $rooms[$k]['image'] =   $Router->srcRoom($row["roo_id"], $row['roo_picture'], SIZE_SMALL);
}
$Layout->setTitle('Gửi thông tin đơn phòng thành công')
        ->setDescription('Gửi thông tin đơn phòng thành công')
        ->setIndex(false)
        ->setJS(['page.basic', 'qrcode.min']);

$text = 'Gửi đơn đặt phòng thành công';   
// Nếu KS là OTA và được MAPPING
$isOTA = !empty($booking_info) && $booking_info['hot_ota'] == 1 && $booking_info['hot_id_mapping'] > 0;
$error = false;
$message = 'Giữ phòng thất bại. Vui lòng thử lại!';
$qr_content = '';
$redirect_url = '';
//Breadcrum bar
$arr_breadcrum  =   [
    ['text' => $text]
    ];

//Set module để active màu menu, ko muốn dùng ở .htaccess
$page_module    =   'hotel';
$url_return = DOMAIN_WEB;
$isShowThank    = ($isOTA && $booking_info['bkho_status'] == STT_SUCCESS) || (!empty($booking_info) && ($booking_info['hot_ota'] <= 0 || $booking_info['hot_id_mapping'] <= 0));
// Nếu là KS OTA và chưa thanh toán ms nhảy vào đây
if($isOTA && $booking_info['bkho_status'] != STT_SUCCESS) {
    // Call API gửi BK sang SENNET
    if($cancel == 'false' && $status == 'PAID') {
        $BookingModel->sendBookingToSennet($booking_info);
        return;
    }
    // Nếu hủy đơn thì bỏ hold phòng đi
    if($cancel == 'true' && $status == 'CANCELLED') {
        // Call api unhold
        $BookingModel->unholdBookingHotel($booking_info);
        $message = 'Thanh toán thất bại. Vui lòng đặt lại Booking!';
        $error = true;
        $url_return = isset($_SESSION['url_cancel']) ? $_SESSION['url_cancel'] : DOMAIN_WEB;
        return;
    }
    // Thời gian hold phòng mặc định là 5 phút
    // Nếu chưa có time limit trong session thì set time limit
    // Nếu còn thời gian thì lấy thời gian trong session
    // Nếu hết thời gian thì xóa time limit
    // Gửi yêu cầu hold phòng
    $remaining_seconds = 5 * 60;
    if (!isset($_SESSION['time_limit']) || $_SESSION['time_limit'] < CURRENT_TIME) {
        $time_expired = CURRENT_TIME + 5 * 60; 
        $_SESSION['time_limit'] = $time_expired;
        $bookingResult = $BookingModel->holdBookingHotel($booking_info, $rooms, $time_expired);
        $error = is_array($bookingResult) && isset($bookingResult['error']) ? $bookingResult['error'] == 1 : false;
        if($error) {
            $message = $bookingResult['message'];
            return;
        }
    }
    $time_limit = getValue('time_limit', GET_INT, GET_SESSION, 0);
    if ($time_limit > 0 && $time_limit >= CURRENT_TIME) {
        $remaining_seconds = $time_limit - CURRENT_TIME;
    }
    if ($time_limit > 0 && $time_limit < CURRENT_TIME) {
        unset($_SESSION['time_limit']);
    }

    $text = 'Thanh toán đơn đặt phòng';
    // $qr_content = "THANH TOAN HOA DON #{$booking_info['bkho_code']}";
    // $qr = new QRPayment();
    // $qr->set_transaction_amount($booking_info['bkho_money_pay'])
    //     ->set_beneficiary_organization('970436', '1014328333')
    //     ->set_additional_data_field_template($qr_content);

    // $qr_code = $qr->build();

   

    $room_items = [];
    foreach ($rooms as $room) {
        $qty = (int)$room['bhr_qty'];
        if ($qty < 1) continue;
        $room_items[] = [
            'name' => $room['roo_name'],
            'quantity' => $qty,
            'price' => (int)$room['bhr_price']
        ];
    }

    $paymentLink = $payOS->createBookingPayment(
        intval(substr(strval(microtime(true) * 10000), -6)),
        (int)$booking_info['bkho_money_pay'],
        $booking_info['bkho_code'],
        DOMAIN_WEB."/thanks/hotel/?booking_completed=" . $booking_id,
        DOMAIN_WEB."/thanks/hotel/?booking_completed=" . $booking_id,
        $room_items,
        $time_limit
    );
    $redirect_url = $paymentLink['checkoutUrl'];
}    
?>