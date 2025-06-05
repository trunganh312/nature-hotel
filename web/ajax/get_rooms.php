<?
include('../Core/Config/require_web.php');

//Ajax load nhanh quá khách sẽ ko chú ý giá bị thay đổi nên cần phải cho sleep
usleep(300000);

$hotel_id    =   getValue('id');
$hotel_info  =   $DB->query("SELECT hot_id, hot_free_airport, hot_col_1, hot_col_8, hot_id_mapping, hot_ota
                            FROM hotel
                            WHERE hot_id = $hotel_id AND hot_active = 1")
                            ->getOne();
if (empty($hotel_info)) {
    exit("Khách sạn không tồn tại!");
}

$rooms  =   [];
// Nếu khách sạn OTA và đã mapping r thì chi show các hạng phòng mapping
$room_sql = "roo_hotel_id = $hotel_id AND roo_active = 1";
$isOTA = $hotel_info['hot_id_mapping'] > 0 && $hotel_info['hot_ota'] >= 1;
if($isOTA) {
    $room_sql .= " AND roo_id_mapping > 0";
}
$rows   =   $DB->query("SELECT * FROM room WHERE $room_sql")->toArray();

// Sắp xếp giá từ thấp đến cao 
foreach ($rows as $k => $room) {
    $room["price_min"]          = $HotelModel->getRoomPrice($hotel_id, true, $room["roo_id"]);
    $room["qty"]                = $HotelModel->getTotalRoomAvailable($hotel_id, null, $room["roo_id"]);
    $room["is_breakfast_price"] = $HotelModel->is_breakfast_price;
    $room["historical_cost"]    = $HotelModel->historical_cost;
    
    // Nếu phòng nào mapping với phòng ở OTA mà giá <= 0 thì k show ra nữa
    if ($room["price_min"] <= 0 && !empty($room["roo_id_mapping"]) && $room["roo_id_mapping"] > 0) {
        continue;
    }
    
    $i = $room["price_min"];
    $i = empty($i) ? 10e10 : $i; // Với các phòng k giá thì cho xuống cuối
    do{
        $i += 1;
    } while(isset($rooms[$i]));
    $rooms[$i] = $room;
}

ksort($rooms, SORT_NUMERIC);
$rooms = array_values($rooms);
$hotel_free_airport =   $HotelModel->getFreeAirport($hotel_info);

foreach($rooms as $index => $row) {
    $max_qty = $row["qty"];
    ?>
    <div class="item room-item" data-id="<?=$row["roo_id"] ?>" id="room-<?=$row["roo_id"] ?>">
        <div class="form-booking-inpage">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="image">
                        <p class="show-room-detail" data-box="11">
                            <img src="<?=$Router->srcRoom($row["roo_id"], $row['roo_picture'], SIZE_MEDIUM) ?>" alt="<?=$row["roo_name"] ?>" class="img-responsive img-full">
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
                                <p class="room_attr_hot service_free"><span><i class="input-icon field-icon fad fa-badge-dollar"></i></span>Giá rẻ hơn nếu đặt cùng vé máy bay</p>
                            </div>
                        </div>
                        <div class="room_info_right">
                            <div class="price-wrapper">
                                <? if ($row['historical_cost'] > 0): ?>
                                <span class="price_public"><?=show_money($row['historical_cost']) ?></span><br />
                                <? endif; ?>
                                <span class="price"><?=show_money($row["price_min"]) ?></span>
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
                            <input type="hidden" name="room_id" value="<?=$row["roo_id"] ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?
}
?>
<script>
    $('[data-toggle="tooltip"]').tooltip();
</script>