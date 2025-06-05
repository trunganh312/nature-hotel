<?
include('../Core/Config/require_web.php');

/** Các nghiệp vụ xử lý bằng Ajax sau khi hoàn thành 1 logic nào đó: Gửi email đăng ký, báo đặt hàng... **/
$code   =   getValue('code', GET_STRING, GET_POST, '', 1);
$user_id    =   0;
$error  =   '';
$check_new  =   false;  //Check lỗi ko lưu được User_ID;
$send_email =   false;

//Lấy ký tự đầu tiên của mã đơn để xem là module nào
$first_char =   substr($code, 0, 1);
switch ($first_char) {
    case 'H':
        $table  =   'booking_hotel';
        $prefix =   'bkho_';
        break;
    case 'T':
        $table  =   'booking_tour';
        $prefix =   'bkto_';
        break;
    case 'V':
        $table  =   'booking_ticket';
        $prefix =   'bkti_';
        break;
    default:
        save_log('error_thanks_page.cfn', 'Code Error: ' . $code);
        exit;
}

/** Lấy thông tin booking để xử lý **/
$booking_info   =   $DB->query("SELECT {$prefix}id AS id, {$prefix}user_id AS user_id, {$prefix}email AS email, {$prefix}name AS name, 
                                    {$prefix}phone AS phone, {$prefix}sent_email_book AS sent_email, {$prefix}city AS city
                                FROM $table
                                WHERE {$prefix}code = '$code'
                                LIMIT 1")
                                ->getOne();
if (!empty($booking_info)) {
    
    //Check để chỉ gửi email 1 lần, tránh F5 gửi nhiều lần
    if ($booking_info['sent_email'] == 0) {
        
        //Gửi email cho User đặt
        $Mailer =   new Mailer;
        switch ($first_char) {
            case 'H':
                $Mailer->bookingHotelNewToCustomer($booking_info['id']);
                break;
            case 'T':
                $Mailer->bookingTourNewToCustomer($booking_info['id']);
                break;
            case 'V':
                $Mailer->bookingTicketNewToCustomer($booking_info['id']);
                break;
        }
        
        //Push thông báo vào Telegram
        pushNotifyTelegram($cfg_website['con_message_new_request']);
    }
    
    /** --- Tạo User nếu chưa có TK --- **/
    if ($booking_info['user_id'] <= 0) {
        
        //Check xem email của đơn này đã có tk chưa
        $user_info  =   $User->getUserInfoByEmail($booking_info['email'], 'use_id, use_email');
        
        if (!empty($user_info)) {
            $user_id    =   $user_info['use_id'];
        } else {
            //Nếu chưa có tk thì tạo mới
            $User->register($booking_info);
            $user_id    =   $User->id;
        }
        
        //Update User ID cho đơn
        $DB->execute("UPDATE $table SET {$prefix}user_id = " . $user_id . " WHERE {$prefix}id = " . $booking_info['id'] . " LIMIT 1");
        
        //Update tổng số BK và Last BK cho User, các BK đặt ở web thì update ở đây chứ ko update ở hàm Create BK để tránh bị nặng
        $DB->execute("UPDATE user SET use_last_time_book = " . CURRENT_TIME . ", use_total_booking = use_total_booking + 1 WHERE use_id = $user_id LIMIT 1");
        
    }
} else {
    save_log('error_thanks_page.cfn', 'Code 404: ' . $code);
}

?>