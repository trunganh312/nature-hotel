<div id="st-content-wrapper" class="search-result-page">
    <div class="container">
        <div class="st-breadcrumb">
            <?=generate_navbar($arr_breadcrum, true)?>
        </div>
    </div>
    <div class="container">
        <div class="box_search_detail search_full search_hide_mb suggest_search" data-box="119">
            <?
            include('../../../layout/inc_box_search_hotel.php');
            ?>
        </div>
        <?
        if ($hotel_info['hot_active'] == 1) {
            ?>
            <div class="st-hotel-content">
                <div class="hotel-target-book-mobile">
                    <div class="form-group form-date-field form-date-search clearfix has-icon" data-format="DD/MM/YYYY">
                        <div class="date-wrapper clearfix">
                            <?
                            if (!empty($promotion_apply['value'])) {
                                echo    '<p class="price_bt"><span class="note_promotion">Ưu đãi đặt hôm nay:</span><span class="price_discount">-' . format_number($promotion_apply['value']) . ($promotion_apply['type'] == PromotionModel::TYPE_PERCENT ? '%' : '₫') . '</span></p>';
                            }
                            ?>
                            <div class="check-in-wrapper">
                                <div class="mb_room_selected">
                                    <h5>Phòng đã chọn:</h5>
                                </div>
                                <label>Ngày nhận/trả phòng <a class="event_hotel_detail_change_date" href="javascript:;">(Đổi ngày)</a></label>
                                <div class="render check-in-render"><?=$cfg_date_checkin ?></div>
                                <span> - </span>
                                <div class="render check-out-render"><?=$cfg_date_checkout ?></div>
                                <input type="text" inputmode="none" class="vietgoing-mobile-check-in-out field_date_hide" value="<?=$cfg_date_checkin .' - '. $cfg_date_checkout ?>" />
                                <span class="total_night">(<span><?=$cfg_total_night?></span> đêm)</span>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:;" class="btn btn-primary btn_book">ĐẶT NGAY</a>
                    <p class="notice_book text-center"><?=($hotel_info['price_min'] > 0 ? 'Chưa cần thanh toán ngay!' : 'Đặt để nhận báo giá và xác nhận phòng ngay lập tức!')?></p>
                </div>
            </div>
            <div class="st-hotel-header">
                <div class="left">
                    <h1 class="title_page"><?=$page_h1?></h1>
                    <div class="detail_star_line">
                        <div class="hotel_type"><?=$cfg_hotel_type[$hotel_info['hot_type']] ?? 'Khách sạn' ?></div>
                        <?=gen_star($hotel_info['hot_star'], '&nbsp;')?>
                        <?
                        if ($hotel_info['hot_review_score'] > 0) {
                            ?>
                            <div class="review-score">
                                <span class="st-link" onclick="goto_box('#box_review_detail', -200, 500)"><?//=$hotel_info['hot_review_count']?> Đánh giá</span>
                                <span class="rating rate_mb"><?=$hotel_info['hot_review_score']?></span>
                            </div>
                            <?
                        }
                        ?>
                    </div>
                    <div class="sub-heading">
                        <i class="input-icon field-icon fas fa-map-marker-alt"></i>
                        <?=$hotel_info["hot_address_full"]?>
                        (<a href="javascript:;" class="st-link map-view event_hotel_detail_open_map" data-mode-view="detail" data-id="<?=$hotel_id ?>">Xem bản đồ</a>)
                        <?
                        if ($User->isVGStaff()) {
                            echo    '<p style="margin:5px 0 0;"><a href="/page/hotel/vg_info.php?id=' . $hotel_id . '" class="show_modal" data-modal-name="' . $hotel_info['hot_name'] . '">Xem thông tin liên hệ</a></p>';
                        }
                        ?>
                    </div>
                </div>
                <?
                if ($hotel_info['hot_review_score'] > 0) {
                    ?>
                    <div class="right">
                        <div class="review-score">
                            <span class="rating rate_pc"><?=$hotel_info['hot_review_score']?></span>
                            <p class="st-link event_hotel_scroll_review" onclick="goto_box('#box_review_detail')"><?//=$hotel_info['hot_review_count']?> Đánh giá</p>
                        </div>
                    </div>
                    <?
                }
                ?>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-9">
                    <div class="st-gallery" id="box_hotel_gallery" data-width="100%" data-nav="thumbs" data-height="100%" data-allowfullscreen="true">
                        <div class="fotorama" data-auto="false">
                            <?
                            foreach ($list_image as $img) {
                                echo    '<img src="' . $img . '" alt="' . $page_h1 . '">';
                            }
                            ?>
                        </div>
                    </div>
                    <h2 class="title_section" id="box_list_room">Phòng còn trống tại <?=$hotel_info['hot_name']?>:</h2>
                    <?
                    /** Nếu là KS có giá thì show câu lưu ý để tránh bị trường hợp khách thấy giá cao nên ko đặt **/
                    if ($hotel_info['price_min'] > 0) {
                        ?>
                        <div class="note_price">
                            <p><?=$hotel_type?> này có thể chúng tôi đang có giá thấp hơn giá công bố mà bạn đang xem do chính sách riêng tư và thỏa thuận hợp tác, vui lòng click <b>"ĐẶT NGAY"</b> để được hưởng giá ưu đãi riêng chỉ có tại Vietgoing!</p>
                        </div>
                        <?
                    } else {
                        ?>
                        <div class="note_price">
                            <p><?=$hotel_type?> này đang chưa được công khai giá trên Website do chính sách riêng tư và thỏa thuận hợp tác, vui lòng click <b>"ĐẶT NGAY"</b> để nhận được báo giá ưu đãi riêng chỉ có tại Vietgoing!</p>
                        </div>
                        <?
                    }
                    ?>
                    <div class="st-list-rooms relative" data-toggle-section="st-list-rooms">
                        <div class="loader-wrapper" style="display: none;">
                            <div class="st-loader"></div>
                        </div>
                        <div class="fetch">
                            <?
                            foreach($rooms as $index => $row) {
                                $max_qty = $row["qty"];
                                $attributes =   $AttributeModel->getAttributeOfId($row['roo_id'], GROUP_ROOM, "AND atn_hot = 1");
                                ?>
                                <div class="item room-item" data-id="<?=$row["roo_id"] ?>" id="room-<?=$row["roo_id"] ?>" data-max-qty="<?=$max_qty?>">
                                    <div class="form-booking-inpage">
                                        <div class="row">
                                            <div class="col-xs-12 col-md-4">
                                                <div class="image">
                                                    <p class="show-room-detail" data-box="11">
                                                        <img data-src="<?=$Router->srcRoom($row["roo_id"], $row['roo_picture'], SIZE_MEDIUM) ?>" alt="<?=($page_h1 . ' - ' . $row["roo_name"])?>" class="lazyload img-responsive img-full">
                                                    </p>
                                                </div>
                                                <p class="show_room_detail"><span class="show-room-detail" data-box="13">Xem hình ảnh &amp; tiện nghi</span></p>
                                            </div>
                                            <div class="col-xs-12 col-md-8 room_box_right">
                                                <h2 class="heading"><span class="text_link show-room-detail" data-box="12"><?=$row["roo_name"] ?></span></h2>
                                                <div class="room_info">
                                                    <div class="room_info_left">
                                                        <div class="facilities">
                                                            <?=$HotelModel->showRoomView($row)?>
                                                            <?=$HotelModel->showSquare($row["roo_square_meters"])?>
                                                            <?=$HotelModel->showRoomBed($row)?>
                                                            <?=$HotelModel->showAdultNumber($row["roo_person"], $row['roo_max_adult'], $row['roo_id_mapping'])?>
                                                            <?=$HotelModel->getFreeChildren($row["roo_person"])?>
                                                            <?=$HotelModel->showExtraBed($row["roo_extra_bed"])?>
                                                            <?=$HotelModel->showFreeBreakFast($row["is_breakfast_price"])?>
                                                            <?=$HotelModel->showHTMLFreeAirport($hotel_free_airport)?>
                                                            <p class="room_attr_hot service_free"><span><i class="input-icon field-icon fad fa-badge-dollar"></i></span><?=$text_suggest_bonus?></p>
                                                        </div>
                                                    </div>
                                                    <div class="room_info_right">
                                                        <div class="price-wrapper <?=($row['historical_cost'] > 0 ? 'has_historical' : '')?>">
                                                            <?
                                                            if ($row['historical_cost'] > 0) {
                                                                ?>
                                                                <span class="price_public"><?=show_money($row['historical_cost']) ?></span><br />
                                                                <?
                                                            }
                                                            ?>
                                                            <span class="price"><?=show_money($row['price_min'])?></span>
                                                        </div>
                                                        <div class="st-number-wrapper clearfix bound_choose_qty">
                                                            <span class="prev event_hotel_detail_room_qty"><i class="fal fa-minus"></i></span>
                                                            <?
                                                                if($row['roo_id_mapping'] > 0) {
                                                                    echo "<input type='text' name='room_qty' value='0' class='form-control st-input-number' autocomplete='off' readonly='' data-min='0' data-max='$max_qty' />";
                                                                }else {
                                                                    echo "<input type='text' name='room_qty' value='0' class='form-control st-input-number' autocomplete='off' readonly='' data-min='0' data-max='20' />";
                                                                }
                                                            ?>
                                                            <?
                                                                if($row['roo_id_mapping'] > 0) {
                                                                    echo "<span class='name_phong'><span>0/$max_qty</span>phòng</span>";
                                                                }else {
                                                                    echo "<span class='name_phong'><span>0</span>phòng</span>";
                                                                }
                                                            ?>
                                                            <span class="next event_hotel_detail_room_qty"><i class="fal fa-plus"></i></span>
                                                        </div>
                                                        <input type="hidden" name="room_id" value="<?=$row['roo_id']?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?
                            }
                            ?>
                            <?
                            if (!empty($list_combo)) {
                                foreach ($list_combo as $row) {
                                    $url    =   $Router->detailTour($row, param_box_web(14));
                                    ?>
                                    <div class="item item_extend">
                                        <div class="form-booking-inpage">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-4">
                                                    <div class="image">
                                                        <p>
                                                            <a href="<?=$url?>" target="_blank">
                                                                <img data-src="<?=$Router->srcTour($row['tou_id'], $row['tou_image'], SIZE_MEDIUM) ?>" alt="<?=$row['tou_name']?>" class="lazyload img-responsive img-full">
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-md-8 room_box_right">
                                                    <h2 class="heading"><a href="<?=$url?>" target="_blank"><?=$row['tou_name'] ?></a></h2>
                                                    <div class="room_info">
                                                        <div class="room_info_left">
                                                            <div class="facilities service_included">
                                                                <?=$row['tou_include']?>
                                                            </div>
                                                        </div>
                                                        <div class="room_info_right">
                                                            <div class="price-wrapper">
                                                                <span class="price"><?=show_money($row['tou_price'])?></span>
                                                            </div>
                                                            <div class="st-number-wrapper clearfix hide_pc">
                                                                <a href="<?=$url?>" target="_blank"><i class="fal fa-hand-point-right"></i> Xem chi tiết Combo</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                }
                            }
                            ?>
                        </div>
                        <div class="modal fade modal_popup_half" id="st-modal-show-room" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog container">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="top-filter">
                                            <ul>
                                                <li class="map-popup-title">
                                                    <h3 class="title">Chi tiết phòng</h3>
                                                </li>
                                            </ul>
                                            <span  class="close" data-dismiss="modal" aria-label="Close">
                                                <i class="input-icon field-icon fa">
                                                    <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                                        <!-- Generator: Sketch 49 (51002) - http://www.bohemiancoding.com/sketch -->
                                                        <defs></defs>
                                                        <g id="Ico_close" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round">
                                                            <g id="Group" stroke="#1A2B48" stroke-width="1.5">
                                                                <g id="close">
                                                                    <path d="M0.75,23.249 L23.25,0.749" id="Shape"></path>
                                                                    <path d="M23.25,23.249 L0.75,0.749" id="Shape"></path>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                        <iframe width="100%" height="100%" frameborder="0" marginheight="0" marginwidth="0" src=""></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt40">
                        <h2 class="title_section" id="box_hotel_policy">Chính sách tại <?=$hotel_info["hot_name"]?></h2>
                        <div class="box_policy st-properties">
                            <div class="row">
                                <div>Nhận phòng:</div>
                                <div>Sau <?=$hotel_info["hot_checkin"] ?></div>
                            </div>
                            <div class="row">
                                <div>Trả phòng:</div>
                                <div>Trước <?=$hotel_info["hot_checkout"] ?></div>
                            </div>
                            <div class="row long_policy">
                                <div>Chính sách chung:</div>
                                <div>
                                    <?
                                    if ($hotel_info['hot_policy_config'] == 1) {
                                        echo    $cfg_website['con_policy_hotel'];
                                    } else {
                                        echo    $hotel_info["hot_policy"];
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row long_policy">
                                <div>Chính sách hủy:</div>
                                <div>
                                    <?
                                    //Sử dụng chính sách hủy mặc định
                                    if ($hotel_info['hot_policy_cancel_config'] == 1) {
                                        echo    $cfg_website['con_policy_cancel'];
                                    } else {
                                        //Chính sách hủy riêng của KS
                                        echo    $hotel_info['hot_policy_cancel'];
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="st-hr"></div>
                    <div class="stt-attr-hotel_facilities">
                        <h2 class="title_section" id="box_hotel_attribute">Tiện ích</h2>
                        <div class="facilities">
                            <?
                            foreach($hotel_attribute as $row) {
                                ?>
                                <div class="row group_attribute">
                                    <p><?=$row['info']['name']?></p>
                                    <?
                                    foreach($row["data"] as $row) {
                                        ?>
                                        <div class="col-xs-6 col-sm-4">
                                            <div class="item">
                                                <i class="input-icon field-icon <?=$row["icon"] ?>"></i>
                                                <?=$row["name"] ?>
                                            </div>
                                        </div>
                                        <?
                                    }
                                    ?>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                    </div>
                    <?
                    if (!empty($list_destination_center[$hotel_id]) || !empty($hotel_location)) {
                        ?>
                        <div class="st-hr"></div>
                        <h2 class="title_section" id="box_hotel_place">Vị trí của <?=$hotel_info["hot_name"] ?> </h2>
                        <div class="hotel_near_by">
                            <div class="sub-heading">
                                <i class="input-icon field-icon fas fa-map"></i><a href="javascript:;" class="st-link map-view event_hotel_detail_open_map" data-mode-view="detail" data-id="<?=$hotel_id ?>">Xem bản đồ</a>.
                            </div>
                            <?
                            if (!empty($hotel_location)) {
                                ?>
                                <div class="sub-heading">
                                    <i class="input-icon field-icon fas fa-compass"></i><?=$page_h1 ?> có vị trí ở <?=$hotel_location?>.
                                </div>
                                <?
                            }
                            foreach ($list_destination_center[$hotel_id] as $des_center) {
                                ?>
                                <div class="sub-heading">
                                    <i class="input-icon field-icon fas fa-compass"></i>Cách <a target="_tblank" class="open-modal-destination" data-id="<?= $des_center["des_id"] ?>" data-box="15" href="<?= $Router->detailDestination($des_center, param_box_web(15)) ?>"><?= $des_center['des_name'] ?></a> <?= showDistanceText($des_center['distance_in_km']) ?>.
                                </div>
                                <?
                            }
                            ?>
                        </div>
                        <?
                    }
                    ?>
                    <div class="st-hr"></div>
                    <h2 class="title_section" id="box_hotel_description">Giới thiệu về <?=$hotel_info['hot_name'] ?></h2>
                    <div class="st-description">
                        <?=$hotel_info['hot_description']?>
                    </div>
                    <div class="st-hr large"></div>
                    <!--Review Option-->
                    <h2 id="box_review_detail" class="title_section">Đánh giá <?=$page_h1?></h2>
                    <div id="reviews" data-toggle-section="st-reviews">
                        <div class="review-box">
                            <?
                            if ($hotel_info['hot_review_score'] > 0) {
                                ?>
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="review-box-score">
                                            <div class="review-score">
                                                <?=$hotel_info['hot_review_score']?><span class="per-total"></span>
                                            </div>
                                            <div class="review-score-base">
                                                <p>Dựa trên <span><?=$hotel_info['hot_review_count']?> đánh giá</span></p>
                                                <p class="note_review">Cam kết 100% các đánh giá đều được đánh giá bởi các khách hàng đã đặt phòng tại Vietgoing!</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="review-sumary">
                                            <?
                                            //Lấy ra điểm trung bình của từng tiêu chí
                                            $data_score =   $DB->query("SELECT *
                                                                        FROM reviews_score
                                                                        WHERE resc_group = " . GROUP_HOTEL . " AND resc_item_id = " . $hotel_id)
                                                                        ->getOne();
                                            foreach ($cfg_review_criteria_hotel as $key => $label) {
                                                $percent    =   0;
                                                $score      =   0;
                                                if (isset($data_score['resc_score_' . $key])) {
                                                    $score      =   $data_score['resc_score_' . $key];
                                                    $percent    =   round($score / REVIEW_MAX_SCORE, 2) * 100;
                                                }
                                                ?>
                                                <div class="item">
                                                    <div class="label">
                                                        <?=$label?><span class="number_mb">(<?=$score?>)</span>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="percent green" style="width: <?=$percent?>%;"></div>
                                                    </div>
                                                    <div class="number"><?=$score?></div>
                                                </div>
                                                <?
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?
                            } else {
                                echo    '<p style="margin-bottom:0;text-align:center;">Chưa có đánh giá nào.</p>';
                            }
                            ?>
                        </div>
                        <div class="wp-reviews" data-group="<?=GROUP_HOTEL ?>">
                            <?
                            $inc_reviews_data = [
                                'item'  =>  $hotel_id
                            ];
                            include($path_root . 'layout/inc_reviews.php')
                            ?>
                        </div>
                    </div>
                    <!--End Review Option-->
                    <div class="stoped-scroll-section"></div>
                </div>
                <div class="col-xs-12 col-md-3">
                    <div class="widgets">
                        <div class="fixed-on-mobile" data-screen="992px">
                            <div class="close-icon hide">
                                <i class="input-icon field-icon fa">
                                    <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <!-- Generator: Sketch 49 (51002) - http://www.bohemiancoding.com/sketch -->
                                        <defs></defs>
                                        <g id="Ico_close" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round">
                                            <g id="Group" stroke="#1A2B48" stroke-width="1.5">
                                                <g id="close">
                                                    <path d="M0.75,23.249 L23.25,0.749" id="Shape"></path>
                                                    <path d="M23.25,23.249 L0.75,0.749" id="Shape"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </i>
                            </div>
                            <div class="form-book-wrapper relative">
                                <div class="form-head">
                                    <div class="price">
                                        <span class="value">
                                            <span class="text-small lh1em onsale"><?= $hotel_info["historical_cost"] > 0 ? show_money($hotel_info["historical_cost"]) : '' ?></span>
                                            <?=($hotel_info['price_min'] > 0 ? '<span class="text-lg lh1em item">' . show_money($hotel_info['price_min']) . '/đêm</span>' : '<span class="text-lg lh1em item fs15">Đặt để nhận báo giá ngay lập tức!</span>') ?>
                                        </span>
                                    </div>
                                </div>
                                <form id="form-booking-inpage" action="/checkout/hotel/" class="tour-booking-form search_detail">
                                    <div class="form-group form-date-field form-date-search clearfix has-icon" data-format="DD/MM/YYYY">
                                        <div class="date-wrapper clearfix">
                                            <div class="check-in-wrapper">
                                                <label>Từ ngày - Đến ngày</label>
                                                <input type="text" class="vietgoing-check-in-out form_input_text event_hotel_detail_change_date" value="<?=$cfg_date_checkin .' - '. $cfg_date_checkout ?>" />
                                                <span class="total_night">(<span><?=$cfg_total_night?></span> đêm)</span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="checkin" value="<?=$cfg_date_checkin ?>" />
                                        <input type="hidden" name="checkout" value="<?=$cfg_date_checkout ?>" />
                                    </div>
                                    <div class="form-group form-guest-search clearfix ">
                                        <div class="wp-room_number hide">
                                            <?
                                            if(isset($rooms[0])) {
                                                ?>
                                                <input type="hidden" name="room[<?=$rooms[0]['roo_id'] ?>]" value="1" />
                                                <?
                                            }
                                            ?>
                                        </div>
                                        <div class="wp-rooms-select" style="display: none;">
                                            <div class="item">
                                                <label class="">Phòng đã chọn</label>
                                            </div>
                                            <div class="rooms-select">
                                                <table></table>
                                            </div>
                                        </div>
                                        <!--
                                        <div class="item clearfix">
                                            <label>Người lớn</label>
                                            <div class="select-wrapper">
                                                <div class="st-number-wrapper">
                                                    <input type="text" name="adult" value="<?=getValue('adult', GET_INT, GET_COOKIE, 2)?>" class="form-control st-input-number" autocomplete="off" data-min="1" data-max="20"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item clearfix">
                                            <label>Trẻ em</label>
                                            <div class="select-wrapper">
                                                <div class="st-number-wrapper">
                                                    <input type="text" name="child" value="<?=getValue('child', GET_INT, GET_COOKIE, 0)?>" class="form-control st-input-number" autocomplete="off" data-min="0" data-max="20"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item clearfix">
                                            <label>Em bé</label>
                                            <div class="select-wrapper">
                                                <div class="st-number-wrapper">
                                                    <input type="text" name="baby" value="<?=getValue('baby', GET_INT, GET_COOKIE, 0)?>" class="form-control st-input-number" autocomplete="off" data-min="0" data-max="20"/>
                                                </div>
                                            </div>
                                        </div>
                                        -->
                                    </div>
                                    <div class="message-wrapper" id="message_book_tour"></div>
                                    <div class="submit-group mb-20">
                                        <?
                                        if (!empty($promotion_apply['value'])) {
                                            echo    '<p class="price_bt"><span class="note_promotion">Ưu đãi nếu đặt hôm nay:</span><span class="price_discount">-' . format_number($promotion_apply['value']) . ($promotion_apply['type'] == PromotionModel::TYPE_PERCENT ? '%' : '₫') . '</span></p>';
                                        }
                                        ?>
                                        <button class="btn btn-primary btn-large btn-full upper" type="submit">
                                            ĐẶT NGAY
                                            <i class="fa fa-spinner fa-spin hide"></i>
                                        </button>
                                        <? if($hotel_info['hot_ota'] <= 0 && $hotel_info['hot_id_mapping'] <= 0) { ?>
                                        <p class="notice_book text-center">Chưa cần thanh toán ngay!</p>
                                        <? } else { ?>
                                        <p class="notice_book text-center">Đặt ngay để nhận thêm ưu đãi!</p>
                                        <? } ?>
                                        <input type="hidden" name="utm_web" value="134" />
                                    </div>
                                </form>
                            </div>
                            <?
                            /** Nếu là KS có giá thì show câu lưu ý để tránh bị trường hợp khách thấy giá cao nên ko đặt **/
                            if ($hotel_info['price_min'] > 0) {
                                ?>
                                <div class="note_price hide_mb hide_pc">
                                    <p><?=$hotel_type?> này có thể chúng tôi đang có giá thấp hơn giá công bố mà bạn đang xem do chính sách riêng tư và thỏa thuận hợp tác, vui lòng click <b>"ĐẶT NGAY"</b> để được hưởng giá ưu đãi riêng chỉ có tại Vietgoing!</p>
                                </div>
                                <?
                            } else {
                                ?>
                                <div class="note_price hide_mb hide_pc">
                                    <p><?=$hotel_type?> này đang chưa được công khai giá trên Website do chính sách riêng tư và thỏa thuận hợp tác, vui lòng click <b>"ĐẶT NGAY"</b> để nhận được báo giá ưu đãi riêng chỉ có tại Vietgoing!</p>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?
        } else {
            ?>
            <div class="box_notfound">
                <p><strong><?=$page_h1?></strong> đang được Vietgoing cập nhật thêm thông tin nhằm đem lại những dịch vụ và trải nghiệm tốt hơn cho Quý khách hàng!</p>
                <?
                include('../../../layout/inc_not_found.php');
                ?>
            </div>
            <?
        }
        ?>        
        <div class="st-hr mt-0-small"></div>
        <?
        if (!empty($list_hotel_distance)) {
            ?>
            <h2 class="title_box_relate text-center">Khách sạn ở gần <?=$hotel_info["hot_name"] ?> <span><a href="<?=$url_list_district . param_box_web(17, true)?>" title="Xem các khách sạn ở gần <?=$hotel_info["hot_name"] ?>" target="_blank">Xem tất cả</a></span></h2>
            <div class="services-grid services-nearby hotel-nearby grid mt20 grid-item">
                <div class="row">
                    <?
                    foreach ($list_hotel_distance as $row) {
                        $row['param']   =   param_box_web(16);
                        echo    $HotelModel->showHotelItem2($row);
                    }
                    ?>
                </div>
            </div>
            <?
        }
        //Show link KS Tỉnh/Quận huyện để SEO.
        ?>
        <div class="">
            <div class="box_links box_relate_border">
                <h3 class="suggest_title mt0">Xem thêm:</h3>
                <div class="box_links__body row">
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                        <a href="<?=$url_list_district . param_box_web(18, true)?>" title="Khách sạn ở <?=$district_name?>" target="_blank">Khách sạn ở <?=$district_name?></a>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                        <a href="<?=$url_list_city . param_box_web(18, true)?>" title="Khách sạn ở <?=$city_name?>" target="_blank">Khách sạn ở <?=$city_name?></a>
                    </div>
                    <?
                    //Nếu là khách sạn thì show link các KS cùng hạng sao
                    if ($hotel_info['hot_type'] == 1) { //ID 1 là KS
                        if ($hotel_info['hot_star'] > 0) {
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                <a href="<?=$url_list_district?>?star=<?=$hotel_info['hot_star']?><?=param_box_web(18, true, '&')?>" title="Xem các khách sạn <?=$hotel_info['hot_star']?> sao ở <?=$district_name?>" target="_blank">Khách sạn <?=$hotel_info['hot_star']?> sao ở <?=$district_name?></a>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                <a href="<?=$url_list_city?>?star=<?=$hotel_info['hot_star']?><?=param_box_web(18, true, '&')?>" title="Xem các khách sạn <?=$hotel_info['hot_star']?> sao ở <?=$city_name?>" target="_blank">Khách sạn <?=$hotel_info['hot_star']?> sao ở <?=$city_name?></a>
                            </div>
                            <?
                        }
                    } else {
                        //Nếu là Resort, Homestay... thì show link đến Resort, Homestay
                        ?>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                            <a href="<?=$url_list_district?>?type=<?=$cfg_hotel_type[$hotel_info['hot_type']]?><?=param_box_web(18, true, '&')?>" title="Xem các <?=$cfg_hotel_type[$hotel_info['hot_type']]?> ở <?=$district_name?>" target="_blank"><?=$cfg_hotel_type[$hotel_info['hot_type']]?> ở <?=$district_name?></a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                            <a href="<?=$url_list_city?>?type=<?=$cfg_hotel_type[$hotel_info['hot_type']]?><?=param_box_web(18, true, '&')?>" title="Xem các <?=$cfg_hotel_type[$hotel_info['hot_type']]?> ở <?=$city_name?>" target="_blank"><?=$cfg_hotel_type[$hotel_info['hot_type']]?> ở <?=$city_name?></a>
                        </div>
                        <?
                    }
                    ?>
                </div>
            </div>
            <?
            //Show các Collection liên quan
            if (!empty($list_collection)) {
                ?>
                <div class="list_collection_bottom list_collection_hot box_relate_border">
                    <h3 class="suggest_title">Các bộ sưu tập được du khách chọn đặt nhiều:</h3>
                    <div class="box_list_col row">
                        <div class="col-lg-6 col-md-12">
                            <?
                            $i  =   0;
                            foreach ($list_collection as $row) {
                                $i++;
                                ?>
                                <p><i class="far fa-list-alt"></i><a href="<?=$Router->detailCollection($row, param_box_web(167))?>" title="<?=$row['col_name']?>"><?=$row['col_name']?></a></p>
                                <?
                                if ($i == 3)    echo    '</div><div class="col-lg-6 col-md-12">';
                            }
                            ?>
                        </div>
                    </div>
                    <?
                    $list_d =   $DB->query("SELECT dis_id, dis_name, dis_name_show, dis_hot
                                            FROM district
                                            WHERE dis_active = 1 AND dis_hot = 1 AND dis_city = " . $city_info['cit_id'])
                                            ->toArray();
                    foreach ($list_d as $d) {
                        ?>
                        <h4 class="top_list_view_more"><i class="fal fa-hand-point-right"></i><a href="<?=$Router->listCollectionDistrict($d, param_box_web(167))?>">Xem thêm các bộ sưu tập ở <?=$PlaceModel->getDistrictName($d)?></a></h4>
                        <?
                    }  
                    ?> 
                    <h4 class="top_list_view_more"><i class="fal fa-hand-point-right"></i><a href="<?=$Router->listCollectionCity($city_info, param_box_web(167))?>">Xem thêm các bộ sưu tập ở <?=$city_info['cit_name']?></a></h4>
                </div>
                <?
            }
            ?>
        </div>
        <?
        /** Gợi ý các tour **/
        if (!empty($list_tour_relate)) {
            ?>
            <h2 class="title_box_relate text-center mt50">Các tour du lịch ở <?=$tour_relate_title?></h2>
            <div class="st-list-tour-related row">
                <div class="search-result-page st-tours">
                    <div class="st-hotel-result list_item_hoz" style="margin-top: 0;">
                        <?
                        foreach ($list_tour_relate as $row) {
                            $row['param']   =   param_box_web(19);
                            echo    $TourModel->showTourItemHorizontal($row, 'col-xs-12 col-sm-6 col-md-3 item-service grid-item has-matchHeight');
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?
        }
        
        if (!empty($list_destination_distance)) {
            ?>
            <div class="">
                <h2 class="title_box_relate text-center">Địa điểm thăm quan ở gần <?=$hotel_info["hot_name"] ?> <span><a href="<?=$Router->listDestinationDistrict($hotel_info, param_box_web(22))?>" title="Xem các địa danh du lịch ở gần <?=$hotel_info["hot_name"]?>" target="_blank">Xem tất cả</a></span></h2>
                <div class="services-grid services-nearby hotel-nearby grid mt15 grid-item destination_near list_item_hoz">
                    <div class="row box_relate_item">
                        <?
                        foreach ($list_destination_distance as $row) {
                            $row['param']   =   param_box_web(21);
                            echo $PlaceModel->showDestination($row, true);
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?
        } 
        ?>
    </div>
</div>
<div class="modal fade" id="modal_iframe" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="input-icon field-icon fa"><svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs></defs>
                    <g id="Ico_close" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round">
                        <g id="Group" stroke="#1A2B48" stroke-width="1.5">
                            <g id="close">
                                <path d="M0.75,23.249 L23.25,0.749" id="Shape"></path>
                                <path d="M23.25,23.249 L0.75,0.749" id="Shape"></path>
                            </g>
                        </g>
                    </g>
                    </svg></i>
                </button>
                <iframe width="100%" height="100%" frameborder="0" marginheight="0" marginwidth="0" src=""></iframe>
            </div>
        </div>
    </div>
</div>