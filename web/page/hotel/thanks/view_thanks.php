<div id="st-content-wrapper">
	<div class="st-breadcrumb">
		<div class="container">
			<?=generate_navbar($arr_breadcrum)?>
		</div>
	</div>
	<div class="container">
        <?
        if (!empty($booking_info)) {
            $total_night    =   round(($booking_info['bkho_checkout'] - $booking_info['bkho_checkin']) / 86400);
            ?>
            <div class="st-checkout-page thanks_page">
                <?
                // Nếu là KS OTA thì phải là đơn thành công ms show cảm ơn
                    if($isShowThank && $status != 'CANCELLED') {
                ?>
                <div class="booking-success-notice">
                	<div class="mb-20">
                		<img src="<?=$cfg_path_svg?>ico_success.svg" alt="Đặt đơn thành công"/>
                		<div class="notice-success">
                			<h2>Cảm ơn Quý khách đã sử dụng dịch vụ tại Vietgoing.com</h2>
                		</div>
                	</div>
                    <div>
                        <p>Chúng tôi sẽ liên hệ ngay theo số điện thoại <b><?=$booking_info['bkho_phone']?></b> của Quý khách để xác nhận các thông tin và tình trạng phòng trống.</p>
                        <p><b>Lưu ý:</b></p>
                        <p>- Nếu số điện thoại Quý khách vừa nhập (<?=$booking_info['bkho_phone']?>) bị sai, Quý khách vui lòng đặt lại đơn khác để chúng tôi có thể liên hệ được với Quý khách, hoặc liên hệ Hotline của chúng tôi: <a href="tel:0931 666 900">0931 666 900</a> (Gọi điện, Zalo, Viber, WhatsApp...) để được hỗ trợ.</p>
                    </div>
                </div>
                <?
                }else {
                ?>
                <div id="countdown-timer" style="text-align: center; padding: 15px; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; font-size: 18px; font-weight: bold; color: #333;margin-bottom: 10px;">
                    Thời gian thanh toán: <span id="timer" style="color: #f68b1e;" data-time="<?= $remaining_seconds ?>"></span>
                </div>
                <?
                }
                ?>
                <div class="row">
                	<div class="col-lg-8 col-md-8">
                		<h3 class="title">Thông tin người đặt</h3>
                		<div class="info-form">
                			<ul>
                                <li><span class="label">Mã đơn:</span><span class="value"><?=$booking_info['bkho_code']?></span></li>
                				<li><span class="label">Họ và tên:</span><span class="value"><?=$booking_info['bkho_name']?></span></li>
                				<li><span class="label">Điện thoại:</span><span class="value" id="thanks_phone"><?=convert_phone_number($booking_info['bkho_phone'])?></span></li>
                				<li><span class="label">Email:</span><span class="value" id="thanks_email"><?=($booking_info['bkho_email'] != VG_EMAIL ? $booking_info['bkho_email'] : '')?></span></li>
                				<li><span class="label">Yêu cầu riêng:</span><span class="value"><?=$booking_info['bkho_note']?></span></li>
                			</ul>
                		</div>
                        <div class="top_checkout">
                            <div class="item-service">
                                <div class="row item-service-wrapper">
                                    <div class="col-sm-4 thumb-wrapper">
                                        <div class="thumb">
                                            <img width="450" height="417" src="<?=$Router->srcHotel($booking_info["hot_id"], $booking_info['hot_picture'], SIZE_SMALL)?>" class="img-responsive wp-post-image loaded" alt="<?=$booking_info['hot_name']?>" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="col-sm-8 item-content">
                                        <div class="item-content-w">
                                            <h1><a href="<?=$Router->detailHotel($booking_info)?>"><?=$cfg_hotel_type[$booking_info['hot_type']] . ' ' . $booking_info['hot_name']?></a></h1>
                                            <?=gen_star($booking_info['hot_star'])?>
                                            <p class="service-location">
                                                <i class="input-icon field-icon fas fa-map-marker-alt"></i><?=substr($booking_info['hot_address_full'], strpos($booking_info['hot_address_full'], ',') + 1)?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                	</div>
                	<div class="col-lg-4 col-md-4">
                        <h3 class="title">Thông tin phòng</h3>
                		<div class="cart-info" id="cart-info">
                            <?
                            foreach($rooms as $row) {
                                ?>
                                <div class="service-section">
                                    <div class="service-left">
                                        <h4 class="title"><?=$row['roo_name']?></h4>
                                        <p class="mb-5"><span class="show-price fs13"><?=$row["bhr_qty"]?> phòng <i class="fal fa-times"></i> <?=$total_night?> đêm<?=($row["bhr_price"] > 0 ? ': ' . show_money($row["bhr_price"] * $row["bhr_qty"]) : '') ?></span></p>
                                    </div>
                                    <div class="service-right">
                                        <img width="110" height="110" src="<?=$row['image']?>" class="img-responsive wp-post-image" alt="<?=$row['roo_name']?>" loading="lazy" srcset="<?=$row['image']?> 110w" sizes="(max-width: 110px) 100vw, 110px" />
                                    </div>
                                </div>
                                <?
                            }
                            ?>
                            <div class="info-section">
                                <ul>
                                    <li class="">
                                        <span class="label">Nhận phòng</span>
                                        <span class="value checkin_show"><?=getVietnameseDay($booking_info["bkho_checkin"])?>, <?=date("d/m/Y", $booking_info["bkho_checkin"])?> <?=$booking_info["hot_checkin"]?></span>
                                    </li>
                                    <li>
                                        <span class="label">Trả phòng</span>
                                        <span class="value checkin_show"><?=getVietnameseDay($booking_info["bkho_checkout"])?>, <?=date("d/m/Y", $booking_info["bkho_checkout"])?> <?=$booking_info["hot_checkout"]?></span>
                                    </li>
                                    <li><span class="label">Người lớn</span><span class="value"><?=$booking_info['bkho_adult']?></span></li>
                                    <li><span class="label">Trẻ em</span><span class="value"><?=$booking_info['bkho_children']?></span></li>
                                    <li><span class="label">Em bé</span><span class="value"><?=$booking_info['bkho_baby']?></span></li>                                
                                </ul>
                            </div>
                            <?
                            if ($booking_info['bkho_money_pay'] > 0) {
                                ?>
                                <div class="total-section">
                                    <ul>
                                        <li>
                                            <span class="label">Tổng tiền</span>
                                            <span class="value"><?=show_money($booking_info['bkho_money_total'])?></span>
                                        </li>
                                        <?
                                        if ($booking_info['bkho_promotion_discount'] > 0) {
                                            ?>
                                            <li>
                                                <span class="label">Ưu đãi đặc biệt từ Vietgoing</span>
                                                <span class="value"><?=show_money($booking_info['bkho_promotion_discount'])?></span>
                                            </li>
                                            <?
                                        }
                                        ?>
                                        <li class="money_discount">
                                            <span class="label">Giảm trừ <?=($booking_info['bkho_money_discount'] > 0 ? '(Mã: ' . $booking_info['bkho_voucher_code'] . ')' : '')?></span>
                                            <span class="value"><?=show_money($booking_info['bkho_money_discount'])?></span>
                                        </li>
                                        <li class="payment-amount">
                                            <span class="label">Thanh toán</span>
                                            <span class="value"><?=show_money($booking_info['bkho_money_pay'])?></span>
                                        </li>
                                        <?
                                            if(!empty($redirect_url)) {
                                        ?>
                                        <li class="payment-amount" style="justify-content: center; margin-top: 20px;">
                                            <button class="btn btn-success" onclick="window.open('<?=$redirect_url?>', '_blank')">
                                                <i class="fas fa-credit-card"></i> Thanh toán ngay với PayOS
                                            </button>
                                        </li>
                                        <?
                                            }
                                        ?>
                                    </ul>
                                </div>
                                <?
                            }
                            ?>
                		</div>
                	</div>
                </div>
                <div class="mt20">
                    <?
                    if ($booking_info['bkho_email'] != VG_EMAIL) {
                        ?>
                        <p>Thông tin chi tiết đơn đặt phòng đã được gửi tới địa chỉ Email: <b><?=$booking_info['bkho_email']?></b></p>
                        <?
                    }
                    ?>
                </div>
                <p class="mt20"><a href="<?=DOMAIN_WEB?>">Quay về trang chủ</a></p>
			</div>
            <?
        } else {
            echo    '<h4 class="mt30">Đơn đặt phòng không tồn tại hoặc đã hết thời gian xử lý.';
        }
        ?>
		</div>
	</div>
<div id="timeoutPopup" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; text-align: center;">
        <h3>Hết hạn giữ phòng</h3>
        <p>Thời gian giữ phòng của bạn đã hết. Vui lòng đặt lại.</p>
        <button onclick="window.location.href='<?=DOMAIN_WEB?>'" style="padding: 10px 20px; background: #f68b1e; color: white; border: none; border-radius: 5px; cursor: pointer;">Quay về trang chủ</button>
    </div>
</div>