<div class="col-md-12">
    <div class="infor-st-setting" id="email-referral" style="margin-bottom: 15px;">
        <form method="POST" action="#email-referral">        
            <div class="row">
                <div class="col-md-12">
                    <h4>Giới thiệu bạn bè - Hưởng nhiều ưu đãi</h4>
                    <div class="msg">
                        <p>Với mỗi 01 email bạn bè được giới thiệu và đặt dịch vụ tại Vietgoing.com được xử lý thành công:</p>
                        <p style="margin: 0;">- Bạn sẽ được tặng một Voucher trị giá <?=format_number($cfg_website['con_referral_discount_a'])?> VNĐ.</p>
                        <p style="margin: 0;">- Người được giới thiệu cũng sẽ được giảm <?=$cfg_website['con_referral_discount_b']?>%  giá trị của đơn đặt dịch vụ đầu tiên.</p>
                        <?
                        /** Nếu cập nhật thông tin thành công **/
                        $referral_result    =   getValue('referral_result', GET_STRING, GET_SESSION, '');
                        if (!empty($referral_result)) {
                            unset($_SESSION['referral_result']);
                            ?>
                            <div class="alert alert-success">
                                <p class="text-small">Đã gửi yêu cầu giới thiệu bạn bè thành công. Vui lòng gửi mã voucher <b><?=$referral_result?></b> cho người vừa được bạn giới thiệu để được giảm giá khi sử dụng đặt dịch vụ tại Vietgoing.com</p>
                            </div>
                            <?
                        }
                        /** Nếu cập nhật thông tin lỗi **/
                        if (!empty($error_email_referral)) {
                            ?>
                            <div class="alert alert-danger">
                                <?
                                foreach ($error_email_referral as $e) {
                                    echo    '<p class="text-small">' . $e . '</p>';
                                }
                                ?>
                            </div>
                            <?
                        }
                        ?>
                        <? if(!$email_referral_add_is): ?>
                        <div class="alert alert-warning">
                            <p class="text-small">Bạn cần có ít nhất 01 đơn đặt phòng hoặc tour thành công để sử dụng được tính năng giới thiệu bạn bè</p>
                        </div>
                        <? endif; ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-filled">
                        <label for="email_referral">Email của bạn bè muốn giới thiệu</label>
                        <input name="email_referral" <?=$email_referral_add_is ? '' : 'readonly disabled' ?> value="<?=$email_referral ?>" class="form-control" type="email" />
                    </div>
                </div>
            </div>
            <? if($email_referral_add_is): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-filled">
                        <input name="btn_update_pass" class="btn btn-primary" type="submit" value="Gửi giới thiệu" />
                        <input type="hidden" name="action" value="email_referral" />
                    </div>
                </div>
            </div>
            <? endif; ?>
        </form>
    </div>

    <div class="infor-st-setting" style="margin-bottom: 15px;">
        <form method="POST" action="#infor-st-setting">        
            <div class="row">
                <div class="col-md-12">
                    <h4>Bạn bè đã được giới thiệu</h4>
                    <p>Gửi mã giới thiệu cho bạn bè của bạn để họ được giảm giá khi đặt dịch vụ tại Vietgoing.com, sau khi đơn đặt đầu tiên được xác nhận thành công, bạn cũng sẽ nhận được mã giảm giá (hoa hồng) thay cho lời cảm ơn từ Vietgoing!</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="infor-st-setting list_table tbl_referral">
                        <div class="tabbable">
                            <?=$Table->createTable()?>
                            <?
                            //Hiển thị data
                            $data   =   $DB->query($Table->sql_table)->toArray();
                            $stt    =   0;
                            foreach ($data as $row) {
                                $Table->setRowData($row);
                                $stt++;
                                ?>
                                <?=$Table->createTR($stt, $row['urh_id']);?>
                                <?=$Table->showFieldText('urh_email_referral')?>
                                <?=$Table->showFieldText('urh_referral_code')?>
                                <?=$Table->showFieldDate('urh_created_at')?>
                                <?=$Table->showFieldNumber('urh_commission')?>
                                <td class="text-center">
                                    <? if($row['urh_status'] == STT_SUCCESS): ?>
                                        <span class="badge bg-olive">Thành công</span>
                                    <? else: ?>
                                        <span class="badge bg-danger">Chờ xác nhận</span>
                                    <? endif; ?>
                                </td>
                                <?=$Table->closeTR($row['urh_id']);?>
                                <?
                            }
                            ?>
                            <?=$Table->closeTable();?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>