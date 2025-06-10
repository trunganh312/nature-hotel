<?

use src\Models\RoomPicture;

/**
 * Class HotelModel
 * Version 1.0
 */

class HotelModel extends Model
{

    // Biến đánh dấu ks có giá ăn sáng (giá trị này sẽ đc update liên tục theo hàm lấy giá getRoomPrice)
    public $is_breakfast_price = false;
    // Biến lưu thông tin giá gốc của phòng/ks, giá trị của biến được tính toán sau mỗi lần gọi hàm "getRoomPrice" lấy giá
    public $historical_cost =   0;
    private $attr_location  =   []; //Lưu các giá trị của attribute Vị trí của KS, để cache ko cần phải lấy lại nhiều lần

    function __construct()
    {
        parent::__construct();
    }


    /**
     * HotelModel::getGroupInfoByID()
     * Lay thong tin cua Chuoi KS (group) theo ID (hotg_id)
     * @param mixed $group_id
     * @param string $field
     * @return row
     */
    function getGroupInfoByID($group_id, $field = '*')
    {
        $row    =   $this->DB->query("SELECT " . $field . "
                                        FROM hotel_group
                                        WHERE hotg_id = " . (int)$group_id)
            ->getOne();
        return $row;
    }


    /**
     * HotelModel::getListGroup()
     * Lay ra DS Tinh/TP theo dieu kien where truyen vao
     * @param string $field
     * @param string $where
     * @param string $order_by
     * @param string $type_return: key OR row
     * @return key: key=>value, row: row
     */
    function getListGroup($field = 'hotg_id, hotg_name', $where = '', $order_by = 'hotg_name', $type_return = 'key')
    {

        $sql_where  =   "1";
        if ($where != '') $sql_where    .=  " AND " . $where;

        $data   =   $this->DB->query("SELECT " . $field . ", hotg_parent_id
                                        FROM hotel_group
                                        WHERE " . $sql_where . "
                                        ORDER BY " . $order_by)
            ->toArray();
        $data = hotel_group_sort($data);
        //Nếu muốn trả về là mảng ko có key thì return luôn
        if ($type_return == 'row')   return $data;

        //Nếu muốn trả về là mảng có key => value
        $array_return   =   [];
        foreach ($data as $row) {
            $array_return[$row['hotg_id']] =   $row['hotg_name'];
        }

        return $array_return;
    }

    /**
     * HotelModel::getListType()
     * Lay ra D/S type theo dieu kien where truyen vao
     * @param string $field
     * @param string $where
     * @param string $order_by
     * @param string $type_return: key OR row
     * @return key: key=>value, row: row
     */
    // function getListType($field = 'hty_id, hty_name', $where = '', $order_by = 'hty_name', $type_return = 'key') {

    //     $sql_where  =   "1";
    //     if ($where != '') $sql_where    .=  " AND " . $where;

    //     $data   =   $this->DB->query("SELECT " . $field . "
    //                                     FROM hotel_type
    //                                     WHERE " . $sql_where . "
    //                                     ORDER BY " . $order_by)
    //                                     ->toArray();
    //     //Nếu muốn trả về là mảng ko có key thì return luôn
    //     if ($type_return == 'row')   return $data;

    //     //Nếu muốn trả về là mảng có key => value
    //     $array_return   =   [];
    //     foreach ($data as $row) {
    //         $array_return[$row['hty_id']] =   $row['hty_name'];
    //     }

    //     return $array_return;

    // }   

    /**
     * HotelModel::getHotelPicture()
     * Lay DS cac anh cua KS theo ID
     * @param mixed $hotel_id
     * @return $data row
     */
    function getHotelPicture($hotel_id)
    {
        $data   =   $this->DB->query("SELECT * FROM hotel_picture WHERE hopi_hotel_id = " . $hotel_id . " ORDER BY hopi_order")
            ->toArray();
        return $data;
    }

    /**
     * HotelModel::getRoomPicture()
     * Lay DS cac anh cua phòng theo ID
     * @param mixed $room_id
     * @return $data row
     */
    function getRoomPicture($room_id)
    {
        $data   =   RoomPicture::pass()->where('rop_room_id', $room_id)
            ->orderBy('rop_order')
            ->toArray();
        return $data;
    }


    /**
     * HotelModel::getHotelInfoByID()
     * Lay thong tin chi tiet cua KS theo ID
     * @param mixed $hotel_id
     * @param string $field
     * @return row
     */
    function getHotelInfoByID($hotel_id, $field = '*')
    {
        $row    =   $this->DB->query("SELECT " . $field . "
                                        FROM hotel
                                        WHERE hot_id = " . (int)$hotel_id . sql_company('hot_'))
            ->getOne();
        return $row;
    }

    /**
     * GeneralModel::getTablePartitionRoomPrice()
     * Tạo ra tên có thứ tự của table
     * @param mixed $name
     * @param mixed $id
     * @param mixed $time
     * @param int $partition
     * @return row
     */
    function getTablePartitionRoomPrice($name, $id = 0, $time = CURRENT_TIME, $partition = CFG_PARTITION_TABLE)
    {
        $suffixes   = '';

        // Nếu có thời gian thì nối vào name
        if (!empty($time)) {
            $suffixes .= date('ym', $time);
        }

        // Nếu có partition và id thì nối vào name
        if ($partition > 0 && $id > 0) {
            $suffixes .= ($id % $partition) + 1;
        }
        $table = empty($suffixes) ? $name : $name . '_' . $suffixes;
        return $table;
    }

    /**
     * GeneralModel::existTableRoomPrice()
     * Kiểm tra xem table có tồn tại k, và tạo bảng nếu yêu cầu
     * @param mixed $table
     * @param mixed $create_new
     * @return row
     */
    function existTableRoomPrice($table, $create_new = false)
    {
        $row = $this->DB->pass()->query("SHOW TABLES LIKE '" . $table . "'")->getOne();

        // Nếu table không tồn tại thì tạo mới
        if (empty($row)) {
            if (!$create_new) return false;
            $this->DB->pass()->execute("CREATE TABLE `" . $table . "` (
                 `rop_id` int(11) NOT NULL AUTO_INCREMENT,
                 `rop_price` double NOT NULL DEFAULT 0,
                 `rop_currency_unit` tinyint(1) NOT NULL DEFAULT 1,
                 `rop_type` tinyint(1) NOT NULL DEFAULT 0,
                 `rop_room_id` int(11) NOT NULL DEFAULT 0,
                 `rop_qty` int(11) NOT NULL DEFAULT 1,
                 `rop_hotel_id` int(11) NOT NULL DEFAULT 0,
                 `rop_day` int(11) NOT NULL DEFAULT 0,
                 `rop_created_by` int(11) NOT NULL DEFAULT 0,
                 `rop_created_at` int(11) NOT NULL DEFAULT 0,
                 UNIQUE KEY (`rop_day`, `rop_room_id`, `rop_type`),
                 PRIMARY KEY (`rop_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }
        return true;
    }

    /**
     * GeneralModel::getAllTablePriceByHotel()
     * Lấy tất cả table giá của ks
     * @param mixed $table
     * @param mixed $create_new
     * @return row
     */
    function getAllTablePriceByHotel($id)
    {
        $rows = $this->DB->query("SHOW TABLES LIKE 'room_price%" . (($id % CFG_PARTITION_TABLE) + 1) . "'")->toArray();

        $tables = [];
        foreach ($rows as $row) {
            $tables[] = array_values($row)[0];
        }
        return $tables;
    }

    /**
     * HotelModel::showHotelItem()
     * Generate HTML để show KS ở các trang list
     * @param mixed $row
     * @param string $class_css: class CSS
     * @return html
     */
    function showHotelItem($row, $lazy = false)
    {
        global  $Router, $User, $PlaceModel, $page_type, $cfg_hotel_type, $list_destination_center, $PromotionModel, $list_combo_hotel, $HotelModel;

        $src    =   $Router->srcHotel($row['hot_id'], $row['hot_picture'], SIZE_MEDIUM);
        $url    =   $Router->detailHotel($row, !empty($row['param']) ? $row['param'] : []);

        // Địa điểm trung tâm
        $list_destination_center = empty($list_destination_center) ? [] : $list_destination_center;

        $html_destination_center = [];
        if (!empty($list_destination_center[$row['hot_id']])) {
            foreach ($list_destination_center[$row['hot_id']] as $des_center) {
                $detail_destination = $Router->detailDestination($des_center, !empty($row['param']) ? $row['param'] : []);
                $html_destination_center[] = '<p class="service-location dest_distance"><i class="input-icon field-icon fas fa-compass"></i>Cách <a class="open-modal-destination" target="_tblank" data-id="' . $des_center["des_id"] . '" href="' . $detail_destination . '" title="' . $des_center["des_name"] . '">' . (empty($des_center["des_name_short"]) ? $des_center["des_name"] : $des_center["des_name_short"]) . '</a> ' . showDistanceText($des_center["distance_in_km"]) . '</p>';
            }
        }
        $html_destination_center = implode('', $html_destination_center);

        //Nếu có miễn phí đưa đón sân bay
        $hotel_free_airport =   $this->getFreeAirport($row);
        $html_destination_center    .=  $this->showHTMLFreeAirport($hotel_free_airport);

        // Mở lấy giá khi ks đã có giá để kiểm tra tick có ăn sáng
        // Nếu ks chưa lấy trứoc giá thì mới lấy giá lại
        // if (!isset($row['hot_price'])) {
        $row['hot_price'] = $this->getRoomPrice($row["hot_id"]);
        $row['is_breakfast_price']  = $this->is_breakfast_price;
        $row['historical_cost']     = $this->historical_cost;
        // }

        //Nếu có combo thì gán vào
        $html_combo =   '';
        if (isset($list_combo_hotel[$row['hot_id']])) {
            $html_combo =   '<div class="service-tag bestseller"><div class="feature_class featured feature_extend"><a href="' . $Router->detailTour($list_combo_hotel[$row['hot_id']], !empty($row['param']) ? $row['param'] : []) . '"><i class="far fa-tags"></i>' . (!empty($list_combo_hotel[$row['hot_id']]['tou_short_name']) ? $list_combo_hotel[$row['hot_id']]['tou_short_name'] : cutstring($list_combo_hotel[$row['hot_id']]['tou_name'], 40)) . '</a></div></div>';
        } else {
            if ($row['hot_hot'] == 1) {
                $html_combo =   '<div class="service-tag bestseller"><div class="feature_class featured">HOT</div></div>';
            }
        }

        //Chỉ show 1 phần địa chỉ
        $exp    =   explode(',', $row["hot_address_full"]);
        $count  =   count($exp);

        //Gán phường
        $address    =   $PlaceModel->getMainName($exp[$count - 3]);
        if (!empty($row['hot_address_street'])) $address    =   $row['hot_address_street'] . ', ' . $address;

        //Nếu là trang filter city thì show thêm Quận/Huyện
        if ($page_type != 'district') {
            //Nếu là các tên kiểu Quận 1, Quận 2... thì ko remove chữ Quận đi
            $address    .=  ', ' . $PlaceModel->getMainName($exp[$count - 2]);
        }
        //$address    =   str_replace(['Xã ', 'Phường ', 'Thị trấn ', 'Thị Trấn ', 'Huyện ', 'Quận ', 'Thị xã ', 'Thị Xã ', 'Thành phố ', 'Thành Phố '], '', $address);
        $address    =   trim($address);

        //Show thêm câu có KM trên bản PC
        $str_promotion  =   '';

        //Box show giá trên bản Mobile
        $str_price_mb   =   '';
        $promotion  =   $PromotionModel->info();

        if ($row['hot_price'] > 0) {
            $str_price_mb   .=  '<div class="box_price_bottom' . (!empty($row['is_breakfast_price']) ? '' : ' no_breakfast') . '">';
            //Nối thêm đoạn Rẻ hơn nếu đặt cùng vé MB trên bản Mobile
            $str_price_mb   .=  '<p class="note_save text-green"><i class="input-icon field-icon fad fa-badge-dollar"></i>Giá rẻ hơn nếu đặt cùng vé máy bay</p>';
            //$str_price_mb   .=  '<p class="price_bt' . (!empty($promotion['value']) ? '' : ' no_promotion') . '">';
            if (!empty($row['is_breakfast_price'])) {
                $str_price_mb   .=  '<p class="price_breakfast text-green"><i class="input-icon field-icon fal fa-utensils"></i>Miễn phí bữa sáng</p>';
            }
            if ($row['historical_cost'] > 0) {
                $str_price_mb   .=  '<p class="price_public">' . show_money($row['historical_cost']) . '</p>';
            }
            $str_price_mb   .=  '<p class="price_main">' . show_money($row['hot_price']) . '</p>';
            //$str_price_mb   .=  '</p>';
            if (!empty($promotion['value'])) {
                $str_price_mb      .=  '<p class="price_bt"><span class="note_promotion">Ưu đãi nếu đặt hôm nay:</span><span class="price_discount">-' . format_number($promotion['value']) . ($promotion['type'] == PromotionModel::TYPE_PERCENT ? '%' : '₫') . '</span></p>';
                $str_promotion  .=  '<p class="price_bt"><span class="note_promotion">Đặt hôm nay:</span><span class="price_discount">-' . format_number($promotion['value']) . ($promotion['type'] == PromotionModel::TYPE_PERCENT ? '%' : '₫') . '</span></p>';
            }

            //Nối thêm đoạn Rẻ hơn nếu đặt cùng vé MB trên bản PC
            $str_promotion  .=  '<p class="note_save text-green"><span class=""><i class="input-icon field-icon fad fa-badge-dollar"></i>Giá rẻ hơn nếu đặt cùng vé máy bay</p>';

            $str_price_mb   .=  '</div>';
        }

        //Loại hình của lưu trú, ghép với vị trí
        $str_type   =   $cfg_hotel_type[$row["hot_type"]];
        $localtion  =   $this->getLocation($row);
        if (!empty($localtion)) $str_type   .=  ' - ' . $localtion;

        $html = '<div class="item-service item_list_each item_list_horizon">
                    <div class="row item-service-wrapper has-matchHeight">
                        <div class="box_1 thumb-wrapper">
                            <div class="thumb">
                                ' . $html_combo
            . '<a href="' . $url . '" title="' . $row['hot_name'] . '">
                                    <img ' . ($lazy ? 'data-' : '') . 'src="' . $src . '" class="' . ($lazy ? 'lazyload ' : '') . 'img-responsive wp-post-image" alt="' . $row['hot_name'] . '">                
                                </a>
                            </div>
                        </div>
                        <div class="box_2 item-content item_main_info">
                            <div class="item-content-w">
                                <div class="bound_box_2">
                                    <h4 class="service-title">
                                        <a href="' . $url . '" title="' . $row['hot_name'] . '">' . $row['hot_name'] . '</a>
                                    </h4>' . ($User->isVGStaff() ? '<p style="margin:0;"><a href="/page/hotel/vg_info.php?id=' . $row['hot_id'] . '" class="show_modal" data-modal-name="' . $row['hot_name'] . '">Xem thông tin liên hệ</a></p>' : '')
            . '
                                    <div class="hotel_star plr15">
                                    ' . gen_star($row['hot_star'])
            . ($row['hot_review_score'] > 0 ? '<div class="hot_rating_right">Đánh giá&nbsp;<span class="rating">' . $row['hot_review_score'] . '</span></div>' : '') .
            '</div>
                                    <p class="service-type">
                                        <i class="input-icon field-icon fas fa-building"></i>' . $str_type . '
                                    </p>
                                    <p class="service-location">
                                        <i class="input-icon field-icon fas fa-map-marker-alt"></i>' . $address . '
                                    </p>
                                    <p class="service-location line_map">
                                        <a href="javascript:;" class="st-link map-view event_hotel_list_item_map" data-mode-view="detail" data-id="' . $row['hot_id'] . '"><i class="input-icon field-icon fas fa-map"></i>Xem bản đồ</a>
                                    </p>' . $html_destination_center . '
                                    ' . ($row['hot_review_score'] > 0 ? '<div class="service-review item_score_list">
                                        <span class="rating">' . $row['hot_review_score'] . '</span>
                                        <span class="review">' . $row['hot_review_count'] . ' đánh giá</span>
                                    </div>' : '') . '
                                </div>
                            </div>
                        </div>
                        <div class="box_3 section-footer item_price_pc">
                            <div class="box_price_right ' . (empty($row['is_breakfast_price']) ? 'not_breakfast' : '') . '">
                                <div>
                                <p class="service-price price_hoz">'
            . ($row['historical_cost'] > 0 ? '<span class="price_public">' . show_money($row['historical_cost']) . '</span><br />' : '')
            . ($row['hot_price'] > 0 ? '<span class="price">' . show_money($row['hot_price']) . '</span>' : '<span class="empty_price">Liên hệ: 0931 666 900</span>')
            .
            '</p>'
            . (empty($row['is_breakfast_price']) ? '' : '<p class="breakfast-price mb-1">
                                    <i class="input-icon field-icon fal fa-utensils"></i>Miễn phí bữa sáng
                                </p>') . $str_promotion
            . '
                                </div>
                            </div>
                            ' . $str_price_mb . '
                            <a href="' . $url . '" title="Xem chi tiết khách sạn và chọn phòng đặt" class="btn btn-primary btn_blue upper">Xem chi tiết</a>
                        </div>
                    </div>
                </div>';
        //Return HTML
        return $html;
    }

    /**
     * HotelModel::showHotelItem2()
     * 
     * @param mixed $row
     * @param string $class_css
     * @return
     */
    function showHotelItem2($row, $class_css = 'item', $new_tab = true)
    {
        global  $Router, $PlaceModel, $cfg_hotel_type, $list_destination_center, $PromotionModel, $cfg_local_near;

        // Địa điểm trung tâm
        $list_destination_center = empty($list_destination_center) ? [] : $list_destination_center;
        $html_destination_center = '';
        $html_destination_center    .=  '<div class="box_dest_near">';
        if (!empty($list_destination_center[$row['hot_id']])) {

            foreach ($list_destination_center[$row['hot_id']] as $des_center) {
                $detail_destination = $Router->detailDestination($des_center, !empty($row['param']) ? $row['param'] : []);
                $html_destination_center    .=  '<div class="sub-title"><i class="input-icon field-icon fas fa-compass"></i>Cách <a class="open-modal-destination" target="_tblank" data-id="' . $des_center["des_id"] . '" href="' . $detail_destination . '" title="' . $des_center["des_name"] . '">' . (empty($des_center["des_name_short"]) ? $des_center["des_name"] : $des_center["des_name_short"]) . '</a> ' . showDistanceText($des_center["distance_in_km"]) . '</div>';
            }
        }
        $html_destination_center    .=  '</div>';

        $src    =   $Router->srcHotel($row['hot_id'], $row['hot_picture'], SIZE_MEDIUM);
        $url    =   $Router->detailHotel($row, !empty($row['param']) ? $row['param'] : []);
        // Mở lấy giá khi ks đã có giá để kiểm tra tick có ăn sáng
        // Nếu ks chưa lấy trứoc giá thì mới lấy giá lại
        // if (!isset($row['hot_price'])) {
        $row['hot_price']           = $this->getRoomPrice($row["hot_id"]);
        $row['is_breakfast_price']  = $this->is_breakfast_price;
        $row['historical_cost']     = $this->historical_cost;
        // }

        //Chỉ show 1 phần địa chỉ
        $exp    =   explode(',', $row["hot_address_full"]);
        $count  =   count($exp);

        //Các KS gợi ý thì sẽ chỉ show địa chỉ ở cấp Quận/Huyện
        $address    =   $PlaceModel->getMainName($exp[$count - 3]) . ', ' . $PlaceModel->getMainName($exp[$count - 2]);

        $str_hot    = ($row['hot_hot'] == 1 ? '<div class="service-tag bestseller">
                                                    <div class="feature_class st_featured featured">HOT</div>
                                                </div>' : '');
        $str_price  =   '';
        $promotion  =   $PromotionModel->info();

        if ($row['hot_price'] > 0) {
            $str_price  .=  '<div class="box_price_bottom' . (!empty($row['is_breakfast_price']) ? '' : ' no_breakfast') . '">';
            $str_price  .=  '<p class="price_bt' . (!empty($promotion['value']) ? '' : ' no_promotion') . '">';
            if (!empty($row['is_breakfast_price'])) {
                $str_price  .=  '<span class="price_breakfast"><i class="input-icon field-icon fal fa-utensils"></i>Miễn phí bữa sáng</span>';
            }
            if ($row['historical_cost'] > 0) {
                $str_price  .=  '<span class="price_public">' . show_money($row['historical_cost']) . '</span><br />';
            }
            $str_price  .=  '<span class="price_main">' . show_money($row['hot_price']) . '</span>';
            $str_price  .=  '</p>';
            if (!empty($promotion['value'])) {
                $str_price  .=  '<p class="price_bt"><span class="note_promotion">Ưu đãi nếu đặt hôm nay:</span><span class="price_discount">-' . format_number($promotion['value']) . ($promotion['type'] == PromotionModel::TYPE_PERCENT ? '%' : '₫') . '</span></p>';
            }
            $str_price  .=  '</div>';
        }

        //Loại hình của lưu trú, ghép với vị trí
        $str_type   =   $cfg_hotel_type[$row["hot_type"]];
        $localtion  =   $this->getLocation($row);
        if (!empty($localtion)) $str_type   .=  ' - ' . $localtion;

        $html   = '<div class="has-matchHeight list_ht_relate item_list_each ' . $class_css . '">
                    <div class="service-border">
                        <div class="featured-image">
                            ' . $str_hot . '
                            <a href="' . $url . '" title="' . $row['hot_name'] . '"' . ($new_tab ? ' target="_blank"' : '') . '>
                                <img width="680" height="510" data-src="' . $src . '" class="lazyload img-responsive wp-post-image" alt="' . $row['hot_name'] . '" />
                            </a>
                        </div>
                        <h4 class="title plr15"><a href="' . $url . '" title="' . $row['hot_name'] . '"' . ($new_tab ? ' target="_blank"' : '') . ' class="st-link c-main">' . $row["hot_name"] . '</a></h4>
                        <div class="hotel_star plr15">
                            ' . gen_star($row['hot_star'])
            . ($row['hot_review_score'] > 0 ? '<div class="hot_rating_right">
                                Đánh giá&nbsp;<span class="rating">' . $row['hot_review_score'] . '</span>
                            </div>' : '') .
            '
                        </div>
                        <div class="sub-title plr15">
                            <i class="input-icon field-icon fas fa-building"></i>' . $str_type . '
                        </div>
                        <div class="sub-title plr15">
                            <i class="input-icon field-icon fas fa-map-marker-alt"></i>' . $address . '
                        </div>
                        <div class="sub-title plr15 line_map">
                            <a href="javascript:;" class="st-link map-view event_hotel_relate_open_map" data-mode-view="detail" data-id="' . $row['hot_id'] . '"><i class="input-icon field-icon fas fa-map"></i>Xem bản đồ</a>
                        </div>
                        ' .
            (empty($row['distance_in_km']) ? '' : '<div class="sub-title plr15"><i class="input-icon field-icon fas fa-compass"></i>Cách đây ' . showDistanceText($row['distance_in_km']) . '</div>')
            . $html_destination_center
            . $str_price . '
                    </div>
                </div>';
        //Return HTML
        return $html;
    }

    /**
     * HotelModel::getHotelImage()
     * Lay DS anh cua ks
     * @param mixed $hotel_id
     * @param string $field
     * @return [row]
     */
    function getHotelImage($hotel_id, $field = "*")
    {
        $data   =   $this->DB->query("SELECT $field FROM hotel_picture WHERE hopi_hotel_id = " . (int)$hotel_id . " ORDER BY hopi_order")
            ->toArray();
        return $data;
    }

    /**
     * HotelModel::getRoomImage()
     * Lay DS anh cua ks
     * @param mixed $hotel_id
     * @param string $field
     * @return [row]
     */
    function getRoomImage($room_id, $field = "*")
    {
        $data   =   $this->DB->query("SELECT $field FROM room_picture WHERE rop_room_id = " . (int)$room_id . " ORDER BY rop_order")
            ->toArray();
        return $data;
    }

    function getRoomPrice($hotel_id, $min = true, $room_id = null, $daterange = null)
    {
        // Reset value
        $this->historical_cost = 0;

        $result         = $this->getTableRoomPrice($hotel_id, $daterange);
        $tables         = $result["tables"];
        $daterange_int  = $result["daterange_int"];

        // Lấy ra thông tin giá ăn sáng của phòng
        $rooms = $this->getListData('room', 'roo_id, roo_is_breakfast', "roo_hotel_id = $hotel_id AND roo_active = 1 " . (empty($room_id) ? '' : "AND roo_id = {$room_id}"));

        // Lưu thông tin giá bán và giá khuyến mại
        $result = $promotion_price = [
            "is_breakfast" => empty($rooms[$room_id]) ? false : true,
            "value" => 0
        ];

        $room_id = implode(',', array_keys($rooms));

        // Nếu ks k có phòng thì return luôn
        if (empty($room_id)) return 0;

        foreach ($tables as $v) {
            if (!$this->existTableRoomPrice($v)) continue;
            // Lấy giá nhỏ nhất
            if ($min) {
                $row = $this->DB->query("SELECT tbl.rop_price, tbl.rop_room_id, IF(tbl2.rop_price IS NOT NULL AND tbl2.rop_price > 0,tbl2.rop_price,tbl.rop_price) AS price_promotion
                                                        FROM {$v} AS tbl
                                                        LEFT JOIN {$v} as tbl2 ON (tbl.rop_room_id = tbl2.rop_room_id AND tbl.rop_day = tbl2.rop_day AND tbl2.rop_type = " . CON_PRICE_TYPE_PROMOTION . ")
                                                        WHERE tbl.rop_hotel_id = $hotel_id 
                                                            AND tbl.rop_room_id IN({$room_id})
                                                            AND tbl.rop_type = " . CON_PRICE_TYPE_CLIENT . "
                                                            AND tbl.rop_day BETWEEN " . $daterange_int['from'] . " AND " . $daterange_int['to'] . " ORDER BY price_promotion ASC LIMIT 1")->getOne();
                if (!empty($row["rop_price"])) {
                    if ($result["value"] <= 0 or $row["rop_price"] < $result["value"]) {
                        $result["value"]        = $row["rop_price"];
                        $result["is_breakfast"] = !empty($rooms[$row["rop_room_id"]]);
                    }
                }
                if (!empty($row["price_promotion"])) {
                    if ($promotion_price["value"] <= 0 or $row["price_promotion"] < $promotion_price["value"]) {
                        $promotion_price["value"]        = $row["price_promotion"];
                        $promotion_price["is_breakfast"] = !empty($rooms[$row["rop_room_id"]]);
                    }
                }
            } else {
                // Lấy tổng giá
                $row = $this->DB->query("SELECT SUM(tbl.rop_price) AS price, SUM(IF(tbl2.rop_price IS NOT NULL AND tbl2.rop_price > 0,tbl2.rop_price,tbl.rop_price)) AS price_promotion
                                                        FROM {$v} AS tbl
                                                        LEFT JOIN {$v} as tbl2 ON (tbl.rop_room_id = tbl2.rop_room_id AND tbl.rop_day = tbl2.rop_day AND tbl2.rop_type = " . CON_PRICE_TYPE_PROMOTION . ")
                                                        WHERE tbl.rop_hotel_id = $hotel_id 
                                                            AND tbl.rop_room_id IN({$room_id})
                                                            AND tbl.rop_type = " . CON_PRICE_TYPE_CLIENT . "
                                                            AND tbl.rop_day BETWEEN " . $daterange_int['from'] . " AND " . $daterange_int['to'])->getOne();
                $result["value"] += $row["price"];
                $promotion_price["value"] += $row["price_promotion"];
            }
        }

        // Nếu giá khuyến mại được áp dụng thì lưu giá bán và trường hiển thị giá gốc và trả về giá khuyến mại
        if ($promotion_price['value'] > 0 and $promotion_price['value'] < $result['value']) {
            $this->historical_cost      = $result['value'];
            $this->is_breakfast_price   = $promotion_price['is_breakfast'];
            return $promotion_price['value'];
        }

        // dump($result);
        // Tạm thời lấy ưu tiên gía ăn sáng theo tick phòng nên cmt đoạn này lại
        // $this->is_breakfast_price   = false;
        // $breakfast_price            = 0;
        // foreach ($tables as $v) {
        //     if(!$this->existTableRoomPrice($v)) continue;
        //     if($min) {
        //         // Lấy giá ăn sáng/thường
        //         $sub_sql_breakfast_price = "(SELECT MIN(rop_price) FROM {$v}  WHERE rop_room_id = tbl.rop_room_id AND rop_type = ". CON_PRICE_TYPE_EAT ." AND rop_day = tbl.rop_day)";
        //         $row = $this->DB->query("SELECT 
        //                     {$sub_sql_breakfast_price} AS breakfast_price,
        //                     CASE WHEN 
        //                         rop_type <> ". CON_PRICE_TYPE_EAT ." AND {$sub_sql_breakfast_price} > 0 
        //                     THEN  
        //                         {$sub_sql_breakfast_price}
        //                     ELSE
        //                         rop_price
        //                     END
        //                     AS price_sale
        //                 FROM {$v} AS tbl
        //                 WHERE rop_hotel_id = $hotel_id
        //                     AND rop_room_id IN({$room_id})
        //                     AND rop_type IN(". CON_PRICE_TYPE_CLIENT .",". CON_PRICE_TYPE_EAT .")
        //                     AND rop_day BETWEEN ". $daterange_int['from'] ." AND ". $daterange_int['to'] ." 
        //                     ORDER BY price_sale ASC 
        //                     LIMIT 1")->getOne();

        //         if (!empty($row["price_sale"])) {
        //             if ($result <= 0) {
        //                 $result = $row["price_sale"];
        //                 $this->is_breakfast_price = !empty($row["breakfast_price"]);
        //             } else if($row["price_sale"] < $result) {
        //                 $result = $row["price_sale"];
        //                 $this->is_breakfast_price = !empty($row["breakfast_price"]);
        //             }
        //         }
        //     } else {
        //         $row = $this->DB->query("SELECT SUM(case when rop_type = ". CON_PRICE_TYPE_CLIENT ." then rop_price else 0 end) as price,
        //                             SUM(case when rop_type = ". CON_PRICE_TYPE_EAT ." then rop_price else 0 end) as breakfast_price
        //                             FROM {$v} 
        //                             WHERE rop_hotel_id = $hotel_id 
        //                                 AND rop_room_id IN({$room_id})
        //                                 AND rop_type IN(". CON_PRICE_TYPE_CLIENT .",". CON_PRICE_TYPE_EAT .")
        //                                 AND rop_day BETWEEN ". $daterange_int['from'] ." AND ". $daterange_int['to'])->getOne();
        //         if (empty($row)) continue;

        //         $result             += $row['price'];
        //         $breakfast_price    += $row['breakfast_price'];
        //     }
        // }

        // // Nếu tồn tại giá ăn sáng thì ưu tiên hiển thị
        // if ($breakfast_price > 0) {
        //     $result                     = $breakfast_price;
        //     $this->is_breakfast_price   = true;
        // }
        // END

        $this->is_breakfast_price   = $result['is_breakfast'];
        return $result['value'];
    }

    /**
     * HotelModel::getTableRoomPrice()
     * Lấy danh sách các bảng chứa giá trong khoảng thời gian tìm kiếm
     * @param integer $hotel_id
     * @param mixed $daterange
     * @return
     */
    function getTableRoomPrice($hotel_id = 0, $daterange = null)
    {
        if (!empty($daterange)) {
            $daterange_int  = generate_time_from_date_range($daterange, false);
        } else {
            global $cfg_time_checkin, $cfg_time_checkout;
            $daterange_int  = [
                "from"  =>  $cfg_time_checkin,
                "to"    =>  $cfg_time_checkout
            ];
        }
        // Trừ đi một ngày để tính theo số đêm
        if ($daterange_int['to'] != $daterange_int['from']) {
            $daterange_int['from']  -=  3600;
            $daterange_int['to']    -=  3600;
        } else {
            $daterange_int['from']  -=  3600;
            $daterange_int['to']    +=  3600;
        }

        // Lấy ra tất cả table chứa data của ks
        $tmp = [];
        $partition  = ($hotel_id % CFG_PARTITION_TABLE) + 1;
        for ($i = 1; $i <= ceil($daterange_int['to'] == $daterange_int['from'] ? 1 : ($daterange_int['to'] - $daterange_int['from']) / 86400); $i++) {
            if ($hotel_id < 1) {
                for ($partition = 1; $partition <= CFG_PARTITION_TABLE; $partition++) {
                    $tmp[] = "room_price_" . date('ym', $daterange_int['from'] + $i * 86400) . $partition;
                }
            } else {
                $tmp[] = "room_price_" . date('ym', $daterange_int['from'] + $i * 86400) . $partition;
            }
        }
        $tmp = array_unique($tmp); // Loại bỏ các table trùng tên

        // Loại bỏ các table k tồn tại
        $tables     = [];
        foreach ($tmp as $v) {
            if ($this->existTableRoomPrice($v)) $tables[] = $v;
        }
        return compact('tables', 'daterange_int');
    }

    function getFieldMonthPrice()
    {
        global $cfg_time_checkin;
        return 'hot_price_m' . (int)date("m", $cfg_time_checkin);
    }

    // Lấy địa điểm trung tâm của nhiều ks theo thành phố
    public function getDestinationCenter($hotels = [])
    {
        $res = [];
        if (empty($hotels)) return $res;

        foreach ($hotels as $row) {
            $rows = $this->DB->query("SELECT des_lat, des_lon, des_name, des_city, des_id, des_name_short, ST_Distance_Sphere( point ({$row['hot_lon']}, {$row['hot_lat']}), 
                                                point(des_lon, des_lat)) / 1000 
                                                AS `distance_in_km`
                                            FROM destination WHERE des_hot = 1 AND des_active = 1 HAVING distance_in_km < 20
                                            ORDER BY distance_in_km LIMIT 3")
                ->toArray();
            $res[$row['hot_id']] = $rows;
        }
        return $res;
    }

    /**
     * HotelModel::getComboByHotels()
     * Lay DS combo theo array hotel
     * @param mixed $hotels [row]
     * @return [row]
     */
    function getComboByHotels($hotels = [])
    {
        $result =   [];
        $ids    =   [];
        if (!empty($hotels)) {
            foreach ($hotels as $row) {
                $ids[]  =   $row['hot_id'];
            }

            $data   =   $this->DB->query("SELECT tou_id, tou_name, tou_short_name, tou_group, toho_hotel_id
                                            FROM tour_hotel
                                            INNER JOIN tour ON toho_tour_id = tou_id
                                            WHERE tou_active = 1 AND toho_hotel_id IN(" . implode(',', $ids) . ")
                                            ORDER BY tou_last_update DESC")
                ->toArray();
            foreach ($data as $row) {
                if (!isset($result[$row['toho_hotel_id']])) $result[$row['toho_hotel_id']]  =   $row;   //Check isset để chỉ lấy 1 combo/1KS
            }
        }

        return $result;
    }

    /**
     * HotelModel::getComboOfHotel()
     * Lay cac combo cua 1 hotel
     * @param mixed $hotel_id
     * @return [row]
     */
    function getComboOfHotel($hotel_id)
    {
        $data   =   $this->DB->query("SELECT tou_id, tou_name, tou_short_name, tou_group, tou_image, tou_policy, tou_include, tou_exclude
                                        FROM tour_hotel
                                        INNER JOIN tour ON toho_tour_id = tou_id
                                        WHERE tou_active = 1 AND toho_hotel_id = $hotel_id
                                        ORDER BY tou_last_update DESC")
            ->toArray();
        return $data;
    }

    // Lấy ks trong bán kính km theo lat,lon
    /**
     * HotelModel::getHotelByLatLon()
     * 
     * @param mixed $city_id
     * @param integer $hotel_id
     * @param mixed $lat
     * @param mixed $lon
     * @param integer $km
     * @param integer $size
     * @return
     */
    public function getHotelByLatLon($city_id = 0, $hotel_id = 0, $lat = 0, $lon = 0, $km = 25, $size = 4)
    {
        if (CON_PRICE_CACHE_MONTH_ENABLE) {
            return $this->getHotelByLatLonV2($city_id, $hotel_id, $lat, $lon, $km, $size);
        }

        global $field_hotel;
        //Câu SQL lọc dữ liệu ks
        $field_hotel .= ", hot_top + hot_hot + IF(hot_price > 0, 1, 0) AS hotel_top, hot_price";

        $sql_more   =   "";
        if ($city_id > 0) {
            $sql_more   .=  " AND hot_city = " . $city_id;
        }

        // - hot_price dùng để đẩy các ks giá < 0 về cuối trong mọi truờng hợp sort
        $rows = $this->DB->query("SELECT $field_hotel , 
                        ST_Distance_Sphere( point ('{$lon}', '{$lat}'), 
                        point(hot_lon, hot_lat)) / 1000 
                        AS `distance_in_km` 
                    FROM hotel
                    LEFT JOIN (
                            SELECT rop_hotel_id, hot_price
                            FROM (" . $this->renderSqlPrice() . ") AS tbl 
                            GROUP BY tbl.rop_hotel_id
                        ) AS tbl_price ON hot_id = tbl_price.rop_hotel_id
                    WHERE hot_active = 1 AND hot_id <> {$hotel_id}
                    HAVING `distance_in_km` <= {$km}
                    ORDER BY distance_in_km ASC, hotel_top DESC, - hot_price DESC, hot_hot DESC, hot_count_booking DESC, hot_count_view DESC
                    LIMIT {$size}")
            ->toArray();

        // Nếu k đủ số ks ở gần thì lấy theo thành phố
        if (count($rows) < $size) {
            $result = $this->DB->query("SELECT $field_hotel
                    FROM hotel
                    LEFT JOIN (
                            SELECT rop_hotel_id, hot_price
                            FROM (" . $this->renderSqlPrice() . ") AS tbl 
                            GROUP BY tbl.rop_hotel_id
                        ) AS tbl_price ON hot_id = tbl_price.rop_hotel_id
                    WHERE hot_active = 1 AND hot_id <> {$hotel_id} $sql_more
                    ORDER BY hotel_top DESC, - hot_price DESC, hot_hot DESC, hot_count_booking DESC, hot_count_view DESC
                    LIMIT " . ($size - count($rows)))
                ->toArray();
            $rows = array_merge($rows, $result);
        }

        return $rows;
    }

    // Lấy ks trong bán kính km theo lat,lon. Sử dụng cơ chế tìm kiếm giá được cache hàng tháng
    /**
     * HotelModel::getHotelByLatLonV2()
     * 
     * @param mixed $city_id
     * @param integer $hotel_id
     * @param mixed $lat
     * @param mixed $lon
     * @param integer $km
     * @param integer $size
     * @return
     */
    public function getHotelByLatLonV2($city_id = 0, $hotel_id = 0, $lat = 0, $lon = 0, $km = 25, $size = 4)
    {
        global $field_hotel;
        //Câu SQL lọc dữ liệu ks
        $field_price  =  $this->getFieldMonthPrice();
        $field_hotel .= ", hot_top + hot_hot + IF({$field_price} > 0, 1, 0) AS hotel_top, {$field_price} as hot_price";

        $sql_more   =   "";
        if ($city_id > 0) {
            $sql_more   .=  " AND hot_city = " . $city_id;
        }

        // - hot_price dùng để đẩy các ks giá < 0 về cuối trong mọi truờng hợp sort
        $rows = $this->DB->query("SELECT $field_hotel , 
                        ST_Distance_Sphere( point ('{$lon}', '{$lat}'), 
                        point(hot_lon, hot_lat)) / 1000 
                        AS `distance_in_km` 
                    FROM hotel
                    WHERE hot_active = 1 AND hot_id <> {$hotel_id}
                    HAVING `distance_in_km` <= {$km}
                    ORDER BY distance_in_km ASC, hotel_top DESC, - hot_price DESC, hot_hot DESC, hot_count_booking DESC, hot_count_view DESC
                    LIMIT {$size}")
            ->toArray();

        // Nếu k đủ số ks ở gần thì lấy theo thành phố
        if (count($rows) < $size) {
            $result = $this->DB->query("SELECT $field_hotel
                    FROM hotel
                    WHERE hot_active = 1 AND hot_id <> {$hotel_id} $sql_more
                    ORDER BY hotel_top DESC, - hot_price DESC, hot_hot DESC, hot_count_booking DESC, hot_count_view DESC
                    LIMIT " . ($size - count($rows)))
                ->toArray();
            $rows = array_merge($rows, $result);
        }

        return $rows;
    }

    /**
     * HotelModel::getListHotelRelate()
     * Lấy list các KS liên quan theo câu query truyền vào 
     * @param mixed $sql_relate: Ko co AND o dau cau
     * @param integer $limit
     * @return array list_hotel
     */
    function getListHotelRelate($sql_relate, $limit = 8)
    {
        if (CON_PRICE_CACHE_MONTH_ENABLE) {
            return $this->getListHotelRelateV2($sql_relate, $limit);
        }

        global $field_hotel;

        //Câu SQL lọc dữ liệu ks
        $field_hotel .= ", hot_top + hot_hot + IF(hot_price > 0, 1, 0) AS hotel_top, hot_price";

        $sql_query  =   "SELECT $field_hotel
                        FROM hotel
                        LEFT JOIN (
                            SELECT rop_hotel_id, hot_price
                            FROM (" . $this->renderSqlPrice() . ") AS tbl 
                            GROUP BY tbl.rop_hotel_id
                        ) AS tbl_price ON hot_id = tbl_price.rop_hotel_id
                        WHERE $sql_relate
                        ORDER BY hotel_top DESC, - hot_price DESC, hot_hot DESC, hot_count_booking DESC, hot_count_view DESC
                        LIMIT $limit";

        // - hot_price dùng để đẩy các ks giá < 0 về cuối trong mọi truờng hợp sort

        $list_hotel =   $this->DB->query($sql_query)->toArray();

        return $list_hotel;
    }

    /**
     * HotelModel::getListHotelRelateV2()
     * Lấy list các KS liên quan theo câu query truyền vào. Sử dụng cơ chế tìm kiếm giá được cache hàng tháng
     * @param mixed $sql_relate: Ko co AND o dau cau
     * @param integer $limit
     * @return array list_hotel
     */
    function getListHotelRelateV2($sql_relate, $limit = 8)
    {
        global $field_hotel;
        $field_price  =  $this->getFieldMonthPrice();

        //Câu SQL lọc dữ liệu ks
        $field_hotel .= ", hot_top + hot_hot + IF({$field_price} > 0, 1, 0) AS hotel_top, {$field_price} AS hot_price";

        $sql_query  =   "SELECT $field_hotel
                        FROM hotel
                        WHERE $sql_relate
                        ORDER BY hotel_top DESC, - hot_price DESC, hot_hot DESC, hot_count_booking DESC, hot_count_view DESC
                        LIMIT $limit";

        // - hot_price dùng để đẩy các ks giá < 0 về cuối trong mọi truờng hợp sort

        $list_hotel =   $this->DB->query($sql_query)->toArray();

        return $list_hotel;
    }

    /**
     * HotelModel::renderSqlPrice()
     * 
     * @return
     */
    function renderSqlPrice()
    {
        $tmp                = $this->getTableRoomPrice();
        $tables_prices      = $tmp["tables"];
        $checkin_out_int    = $tmp["daterange_int"];

        //Generate ra câu UNION
        $tmp_union = [];
        foreach ($tables_prices as $i => $tbl) {
            // Lấy giá ăn sáng/thường
            $tmp_union[] = "(SELECT 
                                rop_hotel_id,
                                rop_price AS hot_price
                                FROM {$tbl} AS tbl
                                WHERE rop_day BETWEEN {$checkin_out_int['from']} 
                                    AND {$checkin_out_int['to']} 
                                    AND rop_type = " . CON_PRICE_TYPE_CLIENT . "
                            )";
        }
        $tmp_union   = implode(" UNION ", $tmp_union);
        // Sql gộp table price
        return "{$tmp_union} ORDER BY hot_price ASC"; // ASC để lấy giá nhỏ nhất của phòng 
    }

    /**
     * HotelModel::getLocation()
     * Lay vi tri cua KS (O gan san bay, bien, trung tam...)
     * @param mixed $hotel_info
     * @param bool $return_string: default false
     * @return string or []
     */
    function getLocation($hotel_info, $return_string = true)
    {

        $col_value  =   intval($hotel_info['hot_col_' . AttributeModel::COLUMN_LOCATION]);
        $result =   [];
        if ($col_value > 0) {
            global  $AttributeModel;
            $attr_value =   $AttributeModel->getValueOfAttribute(AttributeModel::ATTR_HOTEL_LOCATION);
            foreach ($attr_value as $row) {
                if ($col_value & (int)$row['atv_value_hexa']) $result[] =   $row['atv_name'];
            }
        }

        if ($return_string) {
            if (!empty($result)) {
                return implode(', ', $result);
            } else {
                return '';
            }
        }

        //Return
        return $result;
    }

    /**
     * HotelModel::getFreeAirport()
     * 
     * @param mixed $hotel_info
     * @return [string, type: 0 la KS, 1 la Vietgoing]
     */
    function getFreeAirport($hotel_info)
    {

        //Check xem nếu KS đó có đưa đón sân bay thì ưu tiên lấy trước
        $col_value  =   intval($hotel_info['hot_col_' . AttributeModel::COLUMN_SERVICE]);
        if ($col_value > 0 && $col_value & 16384) {   //16384 là giá trị của attribute_value Miễn phí đưa đón sân bay
            return  [
                'string'    =>  'Miễn phí đưa đón sân bay',
                'type'      =>  0   //Free theo chính sách của KS
            ];
        }

        //Nếu ko có chính sách của KS thì sẽ áp dụng chính sách riêng của Vietgoing nếu đặt combo Vé + Phòng
        if ($hotel_info['hot_free_airport'] == 1) {
            return  [
                'string'    =>  'Miễn phí đón sân bay',
                'type'      =>  1   //Free theo chính sách của Vietgoing
            ];
        }

        return '';
    }

    /**
     * HotelModel::showHTMLFreeAirport()
     * 
     * @param mixed $data
     * @return
     */
    function showHTMLFreeAirport($data)
    {
        if (!empty($data)) {
            $data['string'] =   '<p class="room_attr_hot service_free"><span><i class="fal fa-plane"></i></span>' . $data['string'];
            //Nếu là chính sách free airport của VG thì show thêm link để giải thích là phải đặt cùng combo
            if ($data['type'] == 1) {
                $data['string'] .=  '<i class="far fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="Miễn phí khi đặt cùng vé máy bay"></i>';
            }
            $data['string'] .=  '</p>';

            return $data['string'];
        }
        return '';
    }

    /**
     * HotelModel::getFreeChildren()
     * 
     * @param integer $adult
     * @return
     */
    function getFreeChildren($adult = 0)
    {

        //Tạm set như này vì ko có công thức nào chung
        return '<p class="room_attr_hot"><span><i class="fal fa-child"></i></span>Miễn phí trẻ em dưới 6 tuổi</p>';

        if ($adult <= 4) {
            return 1;
        } else if ($adult <= 10) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * HotelModel::showFreeBreakFast()
     * 
     * @return
     */
    function showFreeBreakFast($free_breakfast)
    {
        if ($free_breakfast) {
            return '<p class="room_attr_hot service_free"><span><i class="fal fa-utensils"></i></span>Miễn phí bữa sáng</p>';
        }
        return '';
    }

    /**
     * HotelModel::showAdultNumber()
     * 
     * @param mixed $adult
     * @return
     */
    function showAdultNumber($adult)
    {
        return '<p class="room_attr_hot"><span><i class="fal fa-male"></i><i class="fal fa-female"></i></span>' . $adult . ' người lớn</p>';
    }

    /**
     * HotelModel::showSquare()
     * 
     * @param mixed $square
     * @return
     */
    function showSquare($square)
    {
        if ($square > 10) {
            return '<p class="room_attr_hot"><span><i class="fal fa-arrows"></i></span>' . $square . 'm<sup>2</sup></p>';
        }
        return '';
    }

    /**
     * HotelModel::showRoomView()
     * 
     * @param mixed $room_info
     * @return
     */
    function showRoomView($room_info, $text = false)
    {
        global  $AttributeModel;

        //Lấy các value của View
        $values =   $AttributeModel->getValueOfAttribute(17);   //17 là ID của Attribute View
        $col_value  =  isset($room_info['roo_col_3']) ? (int)$room_info['roo_col_3'] : 0;   //Col_3 là lưu giá trị của View
        if ($col_value > 0) {
            $html   =   '';
            foreach ($values as $v) {
                if ($v['atv_name'] == 'Không view') continue;   //Nếu Ko view thì ko show ra
                if ($col_value & (int)$v['atv_value_hexa']) {
                    $html   .=  ', ' . $v['atv_name'];
                }
            }
            if (!empty($html)) {
                $html   =   substr($html, 2);
                $html   =   'View ' . str_replace('View ', '', $html);
                if ($text) return $html;

                $html   =   '<p class="room_attr_hot"><span><i class="fal fa-telescope"></i></span>' . $html . '</p>';
                return $html;
            }
        }
        return '';
    }

    /**
     * HotelModel::showRoomBed()
     * 
     * @param mixed $room_info
     * @return
     */
    function showRoomBed($room_info, $icon = true)
    {
        $room_beds = $this->DB->query("SELECT robe_quantity, bed_name FROM room_bed 
                                        INNER JOIN bed ON robe_bed_id = bed_id
                                        WHERE robe_room_id = {$room_info['roo_id']} AND bed_active = 1 AND robe_hotel_id = {$room_info['roo_hotel_id']}")->toArray();

        $beds = [];
        foreach ($room_beds as $bed) {
            $beds[] = $bed['robe_quantity'] . ' ' . $bed['bed_name'];
        }
        if (!empty($beds)) {
            if (!$icon) {
                return  implode(', ', $beds);
            }
            return '<p class="room_attr_hot"><i class="fal fa-bed-alt"></i>' . implode(', ', $beds) . '</p>';
        }
        return '';
    }

    /**
     * HotelModel::genHotelFullName()
     * Gan hotel type vao ten cua KS
     * @param mixed $hotel[hot_name, hot_type, hot_district, hot_city]
     * @return string hotel name
     */
    function genHotelFullName($hotel)
    {
        global  $cfg_hotel_type, $cfg_city, $PlaceModel;
        $name   =   $hotel['hot_name'];

        //Nếu trong tên KS chưa có tên Tỉnh/TP thì gắn thêm Quận/Huyện (Nếu là HOT) hoặc Tỉnh/TP
        if (mb_strpos($name, $cfg_city[$hotel['hot_city']]) === false) {
            //Lấy tên Quận/Huyện
            $district   =   $this->DB->query("SELECT dis_hot, dis_name, dis_name_show
                                                FROM district
                                                WHERE dis_id = {$hotel['hot_district']}")
                ->getOne();

            if ($district['dis_hot'] == 1) {
                $district_name  =   $PlaceModel->getDistrictName($district);
                if (mb_strpos($name, $district_name) === false) {
                    $name   .=  ' ' . $district_name;
                }
            } else {
                $name   .=  ' ' . $cfg_city[$hotel['hot_city']];
            }
        }

        return $name;
    }
}
