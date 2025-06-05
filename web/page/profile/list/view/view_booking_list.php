<style>
    .col_1 {
        width: 17%;
    }
    .col_3 {
        width: 110px;
    }
    .col_4 {
        width: 200px;
    }
    .col_1 table tr td:first-child {
        width: 70px;
    }
    .col_2 table tr > td:first-child {
        padding-right: 10px;
    }
    .col_3 table tr > td:first-child {
        width: 75px;
    }
</style>
<div class="col-md-12">
    <div class="infor-st-setting list_table">
        <div class="tabbable">
            <div class="form-group">
                <select id="mySelect-tab" class="form-control hidden-md hidden-lg" onchange="window.location.href='<?=$cfg_path_profile?>?mod=booking&status='+this.value;">
                    <?
                    foreach ($arr_status as $k => $v) {
                        echo    '<option value="' . $k . '"' . ($status == $k ? 'selected' : '') . '>' . $v . '</option>';
                    }
                    ?>
                </select>
            </div>
            <ul class="nav nav-tabs hidden-sm hidden-xs" id="myTab">
                <?
                foreach ($arr_status as $k => $v) {
                    echo    '<li class="' . ($status == $k ? 'active' : '') . '"><a href="' . $cfg_path_profile . '?mod=booking&status=' . $k . '">' . $v . '</a></li>';
                }
                ?>
            </ul>
            <?=$Table->createTable()?>
            <?
            //Hiển thị data
            $data   =   $DB->query(implode(PHP_EOL . "UNION ALL" . PHP_EOL, $arr_sql) . PHP_EOL . "ORDER BY id DESC LIMIT 100")->toArray();
            $stt    =   0;
            foreach ($data as $row) {
                $Table->setRowData($row);
                $stt++;
                ?>
                <?=$Table->createTR($stt, $row['id']);?>
                <td class="col_1">
                    <table class="table_child">
                        <tr>
                            <td>Mã</td>
                            <td>
                                <b><?=$row['code']?></b>    
                            </td>
                        </tr>
                        <tr>
                            <td>Ngày đặt</td>
                            <td title="<?=date('H:i:s', $row['time_create'])?>"><?=date('d/m/Y', $row['time_create'])?></td>
                        </tr>
                        <tr>
                            <td>Trạng thái</td>
                            <td class=""><?=$BookingModel->showStatus($row['status'])?></td>
                        </tr>
                    </table>
                </td>
                <td class="col_2">
                    <?
                    $url    =   $row['group_bk'] == 'booking_hotel' ? $Router->detailHotel(['hot_id' => $row['object_id'], 'hot_name' => $row['object_name']])
                                : $Router->detailTour(['tou_id' => $row['object_id'], 'tou_name' => $row['object_name'], 'tou_group' => $row['object_cate']]);
                    ?>
                    <p><a href="<?=$url?>" target="_blank"><?=$row['object_name']?></a></p>
                    <p><?=($row['group_bk'] == 'booking_hotel' ? 'Ngày nhận phòng' : 'Khởi hành')?>: <?=date('d/m/Y', $row['checkin'])?></p>
                </td>
                <td class="col_4">
                    <table class="table_child">
                        <tr>
                            <td>Tổng</td>
                            <td class="bold"><?=format_number($row['money_pay'])?></td>
                        </tr>
                        <tr>
                            <td>Đã T/Toán</td>
                            <td><?=format_number($row['money_paid'])?></td>
                        </tr>
                        <tr>
                            <td>Còn thiếu</td>
                            <td class="bold">
                                <?=format_number($row['money_pay'] - $row['money_paid'])?>
                            </td>
                        </tr>
                    </table>
                </td>
                <?=$Table->closeTR($row['id']);?>
                <?
            }
            ?>
            <?=$Table->closeTable();?>
        </div>
    </div>
</div>
<div class="modal fade modal-cancel-booking modal-info-booking" id="info-booking-modal" tabindex="-1" role="dialog" aria-labelledby="infoBookingLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="infoBookingLabel">Chi tiết đơn <span></span></h4>
            </div>
            <div class="modal-body">
                <div style="display: none;" class="overlay-form"><i class="fas fa-spinner text-color"></i></div>
                <div class="modal-content-inner"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-cancel-booking modal-info-booking" id="info-booking-payment" tabindex="-1" role="dialog" aria-labelledby="infoBookingPayment">
    <div class="modal-dialog modal-md" role="document" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="infoBookingPayment">Thông tin thanh toán <span></span></h4>
            </div>
            <div class="modal-body">
                <div style="display: none;" class="overlay-form"><i class="fas fa-spinner text-color"></i></div>
                <div class="modal-content-inner"></div>
            </div>
        </div>
    </div>
</div>