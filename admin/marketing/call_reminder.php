<?
include('config_module.php');
$Auth->checkPermission('mkt_voucher_call_reminder');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Gọi điện nhắc khách sử dụng voucher sắp hết hạn';
$table      =   'voucher';
$field_id   =   'vou_id';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT *
                                FROM " . $table . "
                                WHERE vou_booking_id > 0 AND " . $field_id . " = " . $record_id)
                                ->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

/** --- Class query để update dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('vou_call_reminder', DATA_INTEGER, 1)
        ->add('vou_note_reminder', DATA_STRING, '', 'Bạn chưa nhập nội dung phản hồi của khách')
        ->add('vou_time_call', DATA_INTEGER, CURRENT_TIME);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if ($Query->submitForm() && $record_info['vou_call_reminder'] != 1) {
    
    //Kiểm tra lỗi
    if ($Query->validate($field_id, $record_id)) {
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            set_session_toastr();
            reload_parent_window();
        } else {
            $Query->addError('Có lỗi xảy ra khi cập nhật bản ghi');
        }
        
    }
}
/** --- End of Submit form --- **/

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
    <style>
        #vou_note_reminder {
            width: 100%;
        }
    </style>
</head>
<body class="windows-thickbox">
    <?
    $Layout->setPopup(true)->header($page_title);
    ?>
	<?
    //Tạo ra các biến sẵn được lấy ra từ bản ghi này để fill vào form
    $Query->generateVariable($record_info);
    
    //Lấy thông tin booking
    if ($record_info['vou_booking_group'] == GROUP_HOTEL) {
        $booking_info   =   $DB->query("SELECT bkho_code AS code, bkho_checkin AS checkin, bkho_name AS name, bkho_phone AS phone, hot_name AS obj_name
                                        FROM booking_hotel
                                        INNER JOIN hotel ON bkho_hotel_id = hot_id
                                        WHERE bkho_id = " . $record_info['vou_booking_id'])
                                        ->getOne();
    } else if ($record_info['vou_booking_group'] == GROUP_TOUR) {
        $booking_info   =   $DB->query("SELECT bkto_code AS code, bkto_checkin AS checkin, bkto_name AS name, bkto_phone AS phone, tou_name AS obj_name
                                        FROM booking_tour
                                        INNER JOIN tour ON bkto_tour_id = tou_id
                                        WHERE bkto_id = " . $record_info['vou_booking_id'])
                                        ->getOne();
    } else if ($record_info['vou_booking_group'] == GROUP_TICKET) {
        $booking_info   =   $DB->query("SELECT bkti_code AS code, bkti_checkin AS checkin, bkti_name AS name, bkti_phone AS phone, tic_name AS obj_name
                                        FROM booking_ticket
                                        INNER JOIN ticket ON bkti_ticket_id = tic_id
                                        WHERE bkti_id = " . $record_info['vou_booking_id'])
                                        ->getOne();
    }
    if (empty($booking_info))   exit('Booking không tồn tại!');
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->error)?>
    <?=$Form->textHTML('Khách hàng', $booking_info['name'])?>
    <?=$Form->textHTML('Điện thoại', $booking_info['phone'])?>
    <?=$Form->textHTML('Tour/KS', $booking_info['obj_name'])?>
    <?=$Form->textHTML('Ngày đi', date('d/m/Y', $booking_info['checkin']))?>
    <?=$Form->textHTML('Mã voucher', $record_info['vou_code'])?>
    <?=$Form->textHTML('Giá trị giảm', show_money($record_info['vou_value']))?>
    <?=$Form->textHTML('Ngày hết hạn', date('d/m/Y', $record_info['vou_time_expire']))?>
    <?
    if ($record_info['vou_call_reminder'] != 1) {
        echo    $Form->textHTML('Câu hỏi', 'Dạ hồi tháng ' . date('m/Y', $booking_info['checkin']) . ' Anh/Chị có đặt phòng KS/đi tour của bên em và bên em có gửi email cảm ơn tặng kèm mã giảm giá trị giá ' . show_money($record_info['vou_value']) . ', mã này có hạn sử dụng đến ' . date('d/m/Y', $record_info['vou_time_expire']) . ', là còn khoảng ' . floor(($record_info['vou_time_expire'] - CURRENT_TIME)/86400) . ' ngày nữa ạ. Đợt này Anh/Chị có dự định đi du lịch hay đặt phòng ở đâu để sử dụng mã giảm giá này ko ạ?');
        echo    $Form->textarea('Ý kiến khách hàng', 'vou_note_reminder', $vou_note_reminder);
        echo    $Form->button('Cập nhật');
    } else {
        echo    $Form->textHTML('Ý kiến khách hàng', $vou_note_reminder);
    }
    ?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>