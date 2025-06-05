<div id="st-content-wrapper">
    <div class="st-breadcrumb">
        <div class="container">
            <?=generate_navbar($arr_breadcrum)?>
        </div>
    </div>
    <div class="container">
        <div class="st-checkout-page">
            <?
            if ($hotel_info['hot_active'] == 1) {
                ?>
                <div class="row">
                    <form id="cc-form" class="" method="POST" name="checkout_form" onsubmit="validate_checkout();return false;">
                        <div class="col-md-12">
                            <h2 class="note_step">Đặt phòng<span class="hide_mb">:<span class="text-orange"> Chỉ 1 bước duy nhất - Chưa cần thanh toán ngay!</span></span></h2>
                            <p class="text-orange hide_pc text-center">Chỉ 1 bước duy nhất - Chưa cần thanh toán ngay!</p>
                        </div>
                        <div class="col-lg-8 col-md-8" id="checkout_top">
                            <div class="alert alert-danger form_alert" style="<?=(empty($error) ? 'display:none;' : '')?>">
                                <?
                                if (!empty($error)) {
                                    foreach ($error as $e) {
                                        echo    '&bull; ' . $e . '<br>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="top_checkout">
                                <div class="item-service">
                                    <div class="row item-service-wrapper">
                                        <div class="col-sm-4 thumb-wrapper">
                                            <div class="thumb">
                                                <img src="<?=$hotel_info['image']?>" class="img-responsive wp-post-image loaded" alt="<?=$hotel_info['full_name']?>" loading="lazy">
                                            </div>
                                        </div>
                                        <div class="col-sm-8 item-content">
                                            <div class="item-content-w">
                                                <h1><a href="<?=$hotel_info['url'] . param_box_web(37, true)?>"><?=$hotel_info['full_name']?></a></h1>
                                                <?=gen_star($hotel_info['hot_star'])?>
                                                <?
                                                if ($hotel_info['hot_review_count'] > 0) {
                                                    ?>
                                                    <p class="review_info"><span class="rating"><?=$hotel_info['hot_review_score']?></span><span class="review"><?//=$hotel_info['hot_review_count']?> Đánh giá</span></p>
                                                    <?
                                                }
                                                ?>
                                                <p class="service-location">
                                                    <i class="input-icon field-icon fas fa-map-marker-alt"></i><?=substr($hotel_info['hot_address_full'], strpos($hotel_info['hot_address_full'], ',') + 1)?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cart-info" id="cart-info">
                                <h3 class="title">Thông tin phòng</h3>
                                <?
                                foreach($rooms as $row) {
                                    //Các thông tin của phòng
                                    $arr_more   =   [
                                        $HotelModel->showRoomView($row, false),
                                        $HotelModel->showRoomBed($row, false),
                                        $HotelModel->showSquare($row["roo_square_meters"], false),
                                        $HotelModel->showAdultNumber($row["roo_person"], $row['roo_max_adult'], $row['roo_id_mapping'], false),
                                    ];
                                    foreach ($arr_more as $key => $info) {
                                        if (empty($info)) {
                                            unset($arr_more[$key]);
                                        }
                                    }
                                    ?>
                                    <div class="service-section" data-id="<?=$row['roo_id']?>">
                                        <div class="service-left">
                                            <h4 class="title"><?=$row['roo_name']?></h4>
                                            <?
                                            if (!empty($arr_more)) {
                                                echo    '<p class="room_more">' . implode(', ', $arr_more) . '.</p>';
                                            }
                                            ?>
                                            <?= $HotelModel->showExtraBed($row["roo_extra_bed"], false) ?>
                                            <p class="mb-5"><span class="show-price"><?=$row["qty"] ?> phòng <i class="fal fa-times"></i> <?=$cfg_total_night?> đêm<?=($row["price_total"] > 0 ? ': ' . show_money($row["price_total"]) : '') ?></span></p>
                                        </div>
                                        <div class="service-right">
                                            <img width="110" height="110" src="<?=$row['image']?>" class="img-responsive wp-post-image" alt="<?=$row['roo_name']?>" loading="lazy" srcset="<?=$row['image']?> 110w" sizes="(max-width: 110px) 100vw, 110px" />
                                        </div>
                                    </div>
                                    <?
                                }
                                ?>
                                <div class="service-section">
                                    <a href="<?=$hotel_info['url'] . param_box_web(38, true)?>#box_list_room">Thay đổi phòng</a>
                                </div>
                                <div class="info-section">
                                    <ul>
                                        <li class="parent_calendar form-date-search" data-format="DD/MM/YYYY">
                                            <p>
                                                <span class="label">Nhận phòng:</span>
                                                <span class="value checkin_show"><span class="check-in-render"><?=getVietnameseDay($cfg_time_checkin)?>, <?=$date_checkin?></span> <?=$hotel_info["hot_checkin"]?></span>
                                            </p>
                                            <p>
                                                <span class="label">Trả phòng:</span>
                                                <span class="value checkout_show "><span class="check-out-render"><?=getVietnameseDay($cfg_time_checkout)?>, <?=$date_checkout?></span> <?=$hotel_info["hot_checkout"]?></span>
                                            </p>
                                            <p>
                                                <span class="label">Số đêm:</span>
                                                <span class="value checkout_show "><span class="total_night"><span><?=$cfg_total_night?></span> đêm</span></span>
                                            </p>
                                            <a href="javascript:;" id="change-date">Thay đổi ngày
                                                <input type="text" class="vietgoing-check-in-out field_date_hide change_date_co" value="<?=$date_checkin .' - '. $date_checkout ?>" />
                                            </a>
                                            <input type="hidden" name="checkin" value="<?=$date_checkin ?>" />
                                            <input type="hidden" name="checkout" value="<?=$date_checkout ?>" />
                                        </li>
                                        <li class="ad-info">
                                            <ul>
                                                <li>
                                                    <span class="label">Người lớn:</span>
                                                    <input type="number" class="form-control people_number" id="adult" name="adult" value="<?=$adult_number?>" min="<?=$min_adult_number?>" max="<?=$max_adult_number?>" />
                                                </li>
                                                <li>
                                                    <span class="label">Trẻ em <i>(6-11 tuổi)</i>:</span>
                                                    <input type="number" class="form-control people_number" id="children" name="child" value="<?=$child_number?>" min="0" max="<?=$max_children_number?>" />
                                                </li>
                                                <li>
                                                    <span class="label">Em bé <i>(Dưới 6 tuổi)</i>:</span>
                                                    <input type="number" class="form-control people_number" id="baby" name="baby" value="<?=$baby_number?>" min="0" max="<?=$max_baby_number?>" />
                                                </li>                                        
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                <div class="coupon-section" data-mode="hotel">
                                    <h5 class="cursor">Bạn có mã giảm giá? Nhập tại đây!</h5>
                                    <div class="form-group">
                                        <input id="field-voucher_code" value="<?=$voucher_code ?>" type="text" name="voucher">
                                        <button type="button" class="btn btn-primary">Áp dụng</button>
                                        <p class="text-danger" style="display: none;"></p>
                                    </div>
                                </div>
                                <div class="invoice-section">
                                    <div class="check">
                                        <input type="checkbox" name="need_invoice" id="need_invoice" <?=($invoice == 1 ? 'checked' : '') ?>>
                                        <label for="need_invoice">Có lấy hóa đơn</label>
                                        <input type="hidden" id="invoice" name="invoice" value="<?=($invoice == 1 ? 1 : 0) ?>">
                                    </div>
                                    <div class="form-group" id="invoice_info" <?=($invoice == 1 ? 'style="display: block"' : '') ?>>
                                        <input id="field-invoice_info" <?=($invoice == 1 ? 'required' : '') ?> placeholder="Thông tin xuất hóa đơn" value="<?=$invoice_info ?>" type="text" name="invoice_info">
                                    </div>
                                </div>
                                <div class="total-section">
                                    <ul>
                                        <li>
                                            <span class="label">Tổng tiền</span>
                                            <span class="value" id="total_money"><?=show_money($total_money)?></span>
                                        </li>
                                        <li class="promotion_discount <?=(empty($promotion_discount) ? 'hide' : '') ?>">
                                            <span class="label">Ưu đãi đặc biệt từ Vietgoing</span>
                                            <span class="value"><?=show_money($promotion_discount)?></span>
                                        </li>
                                        <li class="money_discount">
                                            <span class="label">Giảm trừ</span>
                                            <span class="value"><?=show_money($money_discount)?></span>
                                        </li>
                                        <li class="payment-amount">
                                            <span class="label">Thanh toán</span>
                                            <span class="value"><?=show_money($money_pay)?></span>
                                        </li>
                                    </ul>
                                    <div class="box_free_service <?=($money_pay <= 0 ? 'mb-5' : '')?>">
                                        <div>
                                            <?=$HotelModel->showFreeBreakFast($include_breakfast)?>
                                            <?=$HotelModel->showHTMLFreeAirport($free_airport)?>
                                            <p class="service_free"><i class="input-icon field-icon fad fa-badge-dollar"></i><?=$text_suggest_bonus?></p>
                                        </div>
                                    </div>
                                    <div class="note_price mt15">
                                        <?
                                        //Nếu ko có giá thì show câu gợi ý ra để thu hút khách click đặt
                                        if ($money_pay <= 0) {
                                            ?>
                                            <p>Khách sạn này đang chưa được công khai giá trên Website do chính sách riêng tư và thỏa thuận hợp tác, vui lòng gửi Thông tin đặt phòng để được hưởng giá ưu đãi riêng chỉ có tại Vietgoing!</p>
                                            <?
                                        } else {
                                            ?>
                                            <p>Khách sạn này có thể chúng tôi đang có giá thấp hơn giá công bố mà bạn đang xem do chính sách riêng tư và thỏa thuận hợp tác, vui lòng gửi Thông tin đặt phòng để được hưởng giá ưu đãi riêng chỉ có tại Vietgoing!</p>
                                            <?
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>      
                        <div class="col-lg-4 col-md-4">
                            <div class="check-out-form" id="customer_info_book">
                                <h3 class="title">Thông tin đặt phòng</h3>
                                <div class="clearfix customer_info">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-icon-left">                
                                                <label for="field-st_name">Họ và tên <span class="require">*</span> </label>
                                                <i class="fa fa-user input-icon"></i><input class="form-control required <?=(isset($error['name']) ? 'error' : '')?>" id="field-st_name" value="<?=$name?>" name="name" placeholder="Họ và tên" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-group-icon-left">                
                                                <label for="field-st_phone">Điện thoại <span class="require">*</span> </label>
                                                <i class="fa fa-user input-icon"></i><input class="form-control required <?=(isset($error['phone']) ? 'error' : '')?>" id="field-st_phone" value="<?=convert_phone_number($phone)?>" name="phone" placeholder="Điện thoại" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-group-icon-left">
                                                <label for="field-st_email">Email</label>
                                                <?
                                                if ($User->logged) {
                                                    ?>
                                                    <i class="fa fa-envelope input-icon"></i><input class="form-control" disabled="disabled" id="field-st_email" value="<?=$User->email?>" name="email" placeholder="Email" type="text">
                                                    <?
                                                } else {
                                                    ?>
                                                    <i class="fa fa-envelope input-icon"></i><input class="form-control <?=(isset($error['email']) ? 'error' : '')?>" id="field-st_email" value="<?=$email?>" name="email" placeholder="Email" type="text">
                                                    <?
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <label for="field-st_note">Yêu cầu riêng</label>
                                                <input type="text" class="form-control" id="field-st_note" name="note" placeholder="VD: Đặt thêm vé máy bay, Nhận phòng sớm..." value="<?=$note?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div id="alert_bottom" class="alert alert-danger form_alert" style="display:none;"></div>
                            <div class="text-center" id="checkout_button">
                                <?
                                    if($hotel_info['hot_ota'] > 0 && $hotel_info['hot_id_mapping'] > 0) {
                                        ?>
                                        <button type="submit" class="btn btn-primary btn-checkout btn-st-checkout-submit btn-st-big">Thanh toán</button>
                                        <?
                                    } else {
                                        ?>
                                        <button type="submit" class="btn btn-primary btn-checkout btn-st-checkout-submit btn-st-big">Đặt phòng</button>
                                        <p class="notice_book">Chưa cần thanh toán ngay!</p>
                                        <?
                                    }
                                ?>
                                <input type="hidden" name="action" value="booking" />
                                <div class="note_bonus">
                                    <?
                                    if ($money_saving > 0) {
                                        ?>
                                        <p><i class="fas fa-piggy-bank"></i>Bạn tiết kiệm được tối thiểu <?=show_money($money_saving)?> khi đặt với chúng tôi và được tư vấn hỗ trợ 24/7.</p>
                                        <?
                                    }
                                    ?>
                                    <p><i class="far fa-money-bill-alt"></i>Bạn được tặng <?=show_money($cfg_website['con_referral_discount_a'])?> khi giới thiệu bạn bè đặt dịch vụ tại Vietgoing.</p>
                                    <p><i class="far fa-money-bill-alt"></i>Bạn được tặng thêm tối thiểu <?=show_money($money_bonus)?> để đặt dịch vụ lần sau tại Vietgoing.</p>
                            </div>
                        </div>
                        </div>
                    </form>
                </div>
                <?
            } else {
                ?>
                <div class="box_notfound">
                    <p><strong><?=$hotel_info['hot_name']?></strong> đang được Vietgoing cập nhật thêm thông tin nhằm đem lại những dịch vụ và trải nghiệm tốt hơn cho Quý khách hàng!</p>
                    <?
                    include('../../../layout/inc_not_found.php');
                    ?>
                </div>
                <?
            }
            ?>
        </div>
    </div>
</div>