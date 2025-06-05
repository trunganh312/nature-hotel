<?
include('../Core/Config/require_web.php');

$room_id    =   getValue('id');
$room_qty   =   getValue('qty');
$room_info  =   $DB->query("SELECT room.*, hot_id, hot_free_airport, hot_col_1, hot_col_8, hot_ota, hot_id_mapping
                            FROM room
                            INNER JOIN hotel ON roo_hotel_id = hot_id
                            WHERE roo_id = $room_id AND roo_active = 1")
                            ->getOne();
if (empty($room_info)) {
    exit("Phòng không tồn tại!");
}

$room_info["price_min"]             =   $HotelModel->getRoomPrice($room_info["roo_hotel_id"], true, $room_info["roo_id"]);
$room_info["max_qty"]               =   $HotelModel->getTotalRoomAvailable($room_info["roo_hotel_id"], null, $room_info["roo_id"]);
$room_info["is_breakfast_price"]    =   $HotelModel->is_breakfast_price;
$room_info["historical_cost"]       =   $HotelModel->historical_cost;

/** Lấy các thông tin liên quan đến Tour **/
$list_image         =   $HotelModel->getRoomImage($room_id);
$hotel_free_airport =   $HotelModel->getFreeAirport($room_info);
$room_attributes    =   $AttributeModel->getAttributeOfId($room_id, GROUP_ROOM);
?>

<div class="page-room-hotel clearfix" data-room_id="<?=$room_id ?>">
    <div class="col-xs-12 col-md-7 col_room_left">
        <div class="st-gallery" data-width="100%" data-nav="thumbs" data-allowfullscreen="true">
            <div class="fotorama" data-auto="false" data-width="100%" data-max-height="500">
                <?
                foreach ($list_image as $img) {
                    echo    '<img src="' . $Router->srcRoom($room_info["roo_id"], $img['rop_picture'], SIZE_LARGE) . '" alt="' . $room_info["roo_name"] . '">';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-5 col-xs-12 col_room_right">
        <div class="stt-attr-hotel_facilities page_detail_room">
            <h1 class="title_section_popup"><?=(mb_strpos($room_info["roo_name"], 'phòng', 0, 'UTF-8') == false ? 'Phòng: ' : '')?><?=$room_info["roo_name"]?></h1>
            <div class="facilities main_attr_room">
                <div class="price-wrapper clearfix">
                    <? if ($room_info['historical_cost'] > 0): ?>
                    <span class="price_public"><?=show_money($room_info['historical_cost']) ?></span><br />
                    <? endif; ?>
                    <span class="price"><?=show_money($room_info["price_min"]) ?></span>
                    <div class="st-number-wrapper clearfix bound_choose_qty">
                        <span class="prev"><i class="fal fa-minus"></i></span>
                        <?
                            if($room_info['roo_id_mapping'] > 0) {
                                echo '<input type="text" name="room_qty" value="'. $room_qty .'" class="form-control st-input-number" autocomplete="off" readonly="" data-min="0" data-max="'.$room_info['max_qty'].'" />';
                            }else {
                                echo '<input type="text" name="room_qty" value="'. $room_qty .'" class="form-control st-input-number" autocomplete="off" readonly="" data-min="0" data-max="20" />';
                            }
                        ?>
                        <?
                            if($room_info['roo_id_mapping'] > 0) {
                                echo "<span class='name_phong'><span>$room_qty/{$room_info['max_qty']}</span>phòng</span>";
                            }else {
                                echo "<span class='name_phong'><span>$room_qty</span>phòng</span>";
                            }
                        ?>
                        <span class="next"><i class="fal fa-plus"></i></span>
                    </div>
                </div>
                <div class="quick_button">
                    <button class="btn btn-primary btn-large btn-full upper event_hotel_room_detail_book">Đặt ngay</button>
                    <p>hoặc <span class="text_link cursor">Chọn thêm phòng khác</span></p>
                </div>
                <div class="row mb-20">
                    <?=$HotelModel->showSquare($room_info["roo_square_meters"])?>
                    <?=$HotelModel->showRoomBed($room_info)?>
                    <?=$HotelModel->showAdultNumber($room_info["roo_person"], $room_info['roo_max_adult'], $room_info['roo_id_mapping'])?>
                    <?=$HotelModel->getFreeChildren($room_info["roo_person"])?>
                    <?=$HotelModel->showExtraBed($room_info["roo_extra_bed"])?>
                    <?=$HotelModel->showFreeBreakFast($room_info["is_breakfast_price"])?>
                    <?=$HotelModel->showHTMLFreeAirport($hotel_free_airport)?>
                </div>
                <?
                if (!empty($room_info['roo_note'])) {
                    if (mb_substr($room_info['roo_note'], -1) != '.')   $room_info['roo_note']  .=  '.';
                    ?>
                    <div class="row room_description">
                        <h5>Mô tả phòng:</h5>
                        <p><?=str_replace([chr(13), PHP_EOL], '. ', $room_info['roo_note'])?></p>
                    </div>
                    <?
                }
                if (!empty($room_attributes)) {
                    ?>
                    <div class="facilities attributes row">
                        <?
                        foreach($room_attributes as $row) {
                            ?>
                            <div class="row group_attribute">
                                <p><?=$row['info']['name']?></p>
                                <?
                                $count  =   count($row["data"]);
                                foreach($row["data"] as $row) {
                                    ?>
                                    <div class="<?=($count > 1 ? 'col-half' : 'col-full')?>">
                                        <div class="item">
                                            <i class="input-icon field-icon <?=$row["icon"] ?>"></i><?=$row["name"] ?>
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
                    <?
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?
//Count click cac box cua website
set_click_box_website();
?>
<script>
    $('[data-toggle="tooltip"]').tooltip();
</script>