<?
include('../Core/Config/require_web.php');
if (!$User->logged) {
    exit('Vui lòng đăng nhập để sử dụng tính năng này!');
}

$code   =   getValue('code', GET_STRING, GET_POST, '', 1);
//Lấy ký tự đầu tiên để xem bk tour hay hotel
$char   =   substr($code, 0, 1);

switch ($char) {
    case 'T':
        $table  =   'booking_tour';
        $prefix =   'bkto_';
        $name   =   "tou_id AS object_id, tou_name AS object_name";
        $join   =   "INNER JOIN tour ON bkto_tour_id = tou_id";
        break;
    case 'H':
        $table  =   'booking_hotel';
        $prefix =   'bkho_';
        $name   =   "hot_id AS object_id, hot_name AS object_name";
        $join   =   "INNER JOIN hotel ON bkho_hotel_id = hot_id";
        break;
    default:
        exit('Dữ liệu này không tồn tại!');
}

$booking_info   =   $DB->query("SELECT " . $prefix . "id AS id, $name, " . $prefix . "note AS note,
                                " . $prefix . "code AS code, " . $prefix . "status AS status, " . $prefix . "time_create AS time_create,
                                " . $prefix . "checkin AS checkin, " . $prefix . "adult AS adult, " . $prefix . "children AS children, " . $prefix . "baby AS baby,
                                " . $prefix . "money_total AS money_total, " . $prefix . "money_discount AS money_discount,
                                " . $prefix . "money_pay AS money_pay, " . $prefix . "money_paid AS money_paid
                                FROM $table
                                $join
                                WHERE " . $prefix . "code = '$code' AND " . $prefix . "user_id = " . $User->id)
                                ->getOne();
if (empty($booking_info)) {
    exit('Dữ liệu này không tồn tại!');
}
$BookingModel   =   new BookingModel;

//Lý do hủy
$arr_fail_type  =   $BookingModel->getFailType();
?>
<div class="st_tab st_tab_order tabbable">
    <div class="tab-content modal_content">
        <div id="modal_detail" class="tab-pane fade active in modal_info">
            <div class="info">
                <div class="row">
                    <div class="col-md-6 item_block">
                        <div class="item_booking_detail">
                            <strong>Mã đơn:</strong>
                            #<?=$booking_info['code']?>
                        </div>
                    </div>
                    <div class="col-md-6 item_block">
                        <div class="item_booking_detail">
                            <strong>Ngày đặt:</strong>
                            <?=date('d/m/Y', $booking_info['time_create'])?>
                        </div>
                    </div>
                    <div class="col-md-12 item_block">
                        <div class="item_booking_detail">
                            <strong>Dịch vụ:</strong>
                            <?
                            $url    =   $char == 'T' ? $Router->detailHotel(['hot_id' => $booking_info['object_id'], 'hot_name' => $booking_info['object_name']])
                                        : $Router->detailTour(['tou_id' => $booking_info['object_id'], 'tou_name' => $booking_info['object_name']]);
                            ?>
                            <a href="<?=$url?>" target="_blank"><?=$booking_info['object_name']?></a>
                        </div>
                    </div>
                    <div class="col-md-12 item_block">
                        <div class="item_booking_detail">
                            <strong>Ghi chú:</strong><?=$booking_info['note']?></div>
                    </div>
                    <div class="line col-md-12"></div>
                    <div class="col-md-12 item_block">
                        <div class="item_booking_detail">
                            <strong><?=($char == 'T' ? 'Ngày khởi hành' : 'Ngày nhận phòng')?>:</strong><?=date('d/m/Y', $booking_info['checkin'])?></div>
                    </div>
                    <div class="col-md-12 item_block">
                        <div class="item_booking_detail">
                            <strong>Số người:</strong>
                            Người lớn: <?=format_number($booking_info['adult'])?>, Trẻ em: <?=format_number($booking_info['children'])?>, Em bé: <?=format_number($booking_info['baby'])?>
                        </div>
                    </div>
                    <div class="line col-md-12"></div>
                    <div class="col-md-12 item_block amount_block">
                        <strong>Tổng tiền:</strong>
                        <div class="pull-right">
                            <?=show_money($booking_info['money_total'], ' VNĐ')?>
                        </div>
                    </div>
                    <div class="col-md-12 item_block amount_block">
                        <strong>Giảm trừ:</strong>
                        <div class="pull-right form-group">
                            <?=show_money($booking_info['money_discount'], ' VNĐ')?>
                        </div>
                    </div>
                    <div class="col-md-12 item_block amount_block">
                        <strong>Thanh toán:</strong>
                        <div class="pull-right">
                            <?=show_money($booking_info['money_pay'], ' VNĐ')?>
                        </div>
                    </div>
                    <div class="col-md-12 item_block amount_block">
                        <strong>Đã thanh toán:</strong>
                        <div class="pull-right form-group">
                            <?=show_money($booking_info['money_paid'], ' VNĐ')?>
                        </div>
                    </div>
                    <div class="col-md-12 item_block amount_block">
                        <strong>Còn thiếu:</strong>
                        <div class="pull-right">
                            <?=show_money($booking_info['money_pay'] - $booking_info['money_paid'], ' VNĐ')?>
                        </div>
                    </div>
                    <div class="col-md-12 item_block amount_block">
                        <strong>Trạng thái:</strong>
                        <div class="form-group">
                            <?=$BookingModel->showStatus($booking_info['status'])?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .note_process {
        font-style: italic;
        margin: 7px 0 0;
    }
</style>