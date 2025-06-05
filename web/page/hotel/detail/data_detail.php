<?
include('../../../Core/Config/require_web.php');
include('../../../Core/Classes/TableOfContent.php');

$hotel_id    =   getValue('id');
$hotel_info  =   $DB->query("SELECT *
                            FROM hotel
                            WHERE hot_id = $hotel_id")
                            ->getOne();
if (empty($hotel_info)) {
    save_log('error_data.cfn', 'Hotel ID: ' . $hotel_id);
    include('../../../soft_404.php');
    exit;
}
$url_canonical  =   $Router->detailHotel($hotel_info);
//Redirect về link đúng trong trường hợp tên của đối tượng bị sửa => URL bị sửa
redirect_correct_url($url_canonical);

//Set module để active màu menu, ko muốn dùng ở .htaccess
$page_module    =   'hotel';
$object_name    =   $hotel_info['hot_name'];

$hotel_info["price_min"]        = $HotelModel->getRoomPrice($hotel_id);
$hotel_info["historical_cost"]  = $HotelModel->historical_cost;

//Lấy thông tin tỉnh TP để show breakcrum
$city_info  =   $DB->query("SELECT cit_id, cit_name, cit_text_bonus FROM city WHERE cit_id = " . $hotel_info['hot_city'])->getOne();
$district_info  =   $DB->query("SELECT dis_id, dis_name, dis_name_show, dis_text_bonus, dis_hot
                                FROM district
                                WHERE dis_id = " . $hotel_info['hot_district'])
                                ->getOne();
$hotel_info =   array_merge($hotel_info, $district_info);

//Câu gợi ý đặt giá rẻ hơn
$text_suggest_bonus =   $cfg_website['con_text_bonus_hotel'];
if (!empty($district_info['dis_text_bonus'])) {
    $text_suggest_bonus =   $district_info['dis_text_bonus'];
} else if (!empty($city_info['cit_text_bonus'])) {
    $text_suggest_bonus =   $city_info['cit_text_bonus'];
}

/** Lấy các thông tin liên quan đến khách sạn **/
$page_image =   $Router->srcHotel($hotel_id, $hotel_info['hot_picture'], SIZE_LARGE);
//Nếu KS nào mà được nhóm các ảnh theo từng nhóm rồi thì lấy theo nhóm, ko thì lấy theo cách cũ
if ($hotel_info['hot_picture_group'] == 1) {
    $list_image =   $HotelModel->getHotelImageGroup($hotel_id);
} else {
    $list_image =   $HotelModel->getHotelImage($hotel_id);
}

$url_list_city      =   $Router->listHotelCity($city_info);
$city_name          =   $city_info['cit_name'];
$url_list_district  =   $Router->listHotelDistrict($hotel_info);
$promotion_apply    =   $PromotionModel->info();

$hotel_info['dis_name'] =   $PlaceModel->getDistrictName($hotel_info);
$district_name      =   $PlaceModel->getDistrictName($district_info, true);

//Breadcrum bar
$arr_breadcrum  =   [
                    ['link' => $url_list_city, 'text' => $city_info['cit_name']],
                    ['link' => $url_list_district, 'text' => $district_name],
                    ['link' => $url_canonical, 'text' => $hotel_info['hot_name']]
                    ];
                    
/** Lấy text và URL để fill sẵn vào form search **/
if ($district_info['dis_hot'] == 1) {
    $search_action_form =   $url_list_district;
    $search_keyword     =   $district_name;
} else {
    $search_action_form =   $url_list_city;
    $search_keyword     =   $city_name;
}

/** Lấy DS các Quận/Huyện để lọc **/
$arr_district   =   $Model->getListData('district', 'dis_id, dis_name', 'dis_city = ' . $hotel_info["hot_city"], 'dis_order DESC, dis_name', 'row');

$list_district  =   [];
foreach ($arr_district as $row) {
    $list_district[]    =   [
                            'link'  =>  $Router->listHotelDistrict($row, param_box_web(23)),
                            'text'  =>  $row['dis_name']
                            ];
}
$link_show_footer   =   $list_district; //Show ở footer
$title_link_footer  =   'Tìm khách sạn theo Quận/Huyện';

$hotel_attribute    =   $AttributeModel->getAttributeOfId($hotel_id, GROUP_HOTEL);

$rooms  =   [];
// Nếu là ks OTA chỉ show những phòng được map ra thôi
$isOTA = $hotel_info['hot_id_mapping'] > 0 && $hotel_info['hot_ota'] >= 1;
$room_sql = "roo_hotel_id = $hotel_id AND roo_active = 1";
if($isOTA) {
    $room_sql .= " AND roo_id_mapping > 0";
}
$rows   =   $DB->query("SELECT * FROM room WHERE $room_sql")->toArray();
if (empty($rows) && $hotel_info['hot_active'] == 1) {
    save_log('empty_hotel_room.cfn', 'Hotel ID: ' . $hotel_id);
}

// Sắp xếp giá từ thấp đến cao 
foreach ($rows as $k => &$room) {
    $room["price_min"]          = $HotelModel->getRoomPrice($hotel_id, true, $room["roo_id"]);
    $room["qty"]                = $HotelModel->getTotalRoomAvailable($hotel_id, null, $room["roo_id"]);
    // Nếu là KS OTA thì đang lấy loại giá OTA trên SENNET
    // Nên loại giá có cấu hình ăn sáng hay k là tùy KS
    // Mình sẽ cập nhật vào trường hot_include_breakfast
    $room["is_breakfast_price"] = $isOTA ? ($hotel_info['hot_include_breakfast'] == 1 ? true : false) : $HotelModel->is_breakfast_price;
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

// Lấy ks xung quanh bán kính 25km
$list_hotel_distance    =   $HotelModel->getHotelByLatLon($hotel_info['hot_city'], $hotel_id, $hotel_info['hot_lat'], $hotel_info['hot_lon'], 25, 8);

// Lấy địa điểm xung quanh bán kính 25km
$list_destination_distance  =   $PlaceModel->getDestinationByLatLon($hotel_info['hot_city'], 0, $hotel_info['hot_lat'], $hotel_info['hot_lon']);

// Lấy địa điểm trung tâm
$list_destination_center = $HotelModel->getDestinationCenter(array_merge($list_hotel_distance, [[
    'hot_id'    =>  $hotel_info['hot_id'],
    'hot_lat'   =>  $hotel_info['hot_lat'],
    'hot_lon'   =>  $hotel_info['hot_lon'],
    'hot_city'  =>  $hotel_info['hot_city'],
]]));

//Lấy vị trí của KS (Gần trung tâm, sân bay...)
$hotel_location =   mb_strtolower($HotelModel->getLocation($hotel_info));

/** List collection liên quan **/
$list_collection    =   $Model->getCollectionRelate($hotel_info['hot_city'], GROUP_HOTEL);

//Lấy thông tin có đưa/đón sân bay hay ko
$hotel_free_airport =   $HotelModel->getFreeAirport($hotel_info);

/** Lấy các tour liên quan **/
$list_tour_relate   =   $DB->query("SELECT DISTINCT(tode_tour_id), $field_tour
                                    FROM tour
                                    INNER JOIN tour_destination ON (tou_id = tode_tour_id)
                                    INNER JOIN destination ON tode_destination_id = des_id
                                    WHERE tou_active = 1 AND des_city = " . $hotel_info['hot_city'] . "
                                    ORDER BY tou_top DESC, tou_hot DESC, tou_last_update DESC
                                    LIMIT 4")
                                    ->toArray();
//Câu title của mục Tour liên quan:
$url_tour_city  =   $Router->listTourCity($city_info);
$tour_relate_title  =   '<a href="' . $url_tour_city . param_box_web(20, true) . '" target="_blank" title="Các tour du lịch ở ' . $city_info['cit_name'] . '">' . $city_info['cit_name'] . '</a>';

//Nếu là Quận/Huyện Hot và ko trùng tên với City (Kiểu Thành phố Bắc Giang, Tỉnh BG) thì show thêm tên Quận/Huyện nữa
if ($district_info['dis_hot'] == 1 && $district_name != $city_info['cit_name']) {
    $tour_relate_title  =   '<a href="' . $Router->listTourDistrict($hotel_info, param_box_web(20)) . '" target="_blank" title="Tour du lịch ở ' . $district_name . '">' . $district_name . '</a>, ' . $tour_relate_title;
}
$tour_relate_title  .=  ' <span><a href="' . $url_tour_city . param_box_web(20, true) . '" target="_blank" title="Xem tất cả các tour du lịch ở ' . $city_info['cit_name'] . '">Xem tất cả</a></span>';

/** Lấy các combo **/
$list_combo =   $HotelModel->getComboOfHotel($hotel_id);

/** Thẻ h1 **/
$page_h1    =   $HotelModel->genHotelFullName($hotel_info);

/** Generate thẻ meta title và description để tốt cho SEO **/
$meta_title =   $page_h1;

//Gán meta description = title
$meta_description   =   $meta_title;

if ($hotel_info['hot_star'] >= 3) {
    $meta_description   .=  ' ' . $hotel_info['hot_star'] . ' sao';
}

//Cho thêm Có vị trí... vào meta description
if (!empty($hotel_location)) {
    $meta_description   .=  ' có vị trí ở ' . $hotel_location;
} else {
    $meta_description   .=  ' có vị trí thuận tiện';
}

//Cho thêm Cách... địa danh vào meta description
if (!empty($list_destination_center[$hotel_id])) {
    //Chỉ lấy cái đầu tiên để meta description ko bị dài quá
    $destinaion_nearest =   !empty($list_destination_center[$hotel_id][0]['des_name_short']) ? $list_destination_center[$hotel_id][0]['des_name_short'] : $list_destination_center[$hotel_id][0]['des_name'];
    $meta_description   .=  ', cách ' . $destinaion_nearest . ' ' . showDistanceText($list_destination_center[$hotel_id][0]['distance_in_km']);
    //Nếu ko có thông tin về vị trí và miễn phí đưa đón sân bay thì ghép thêm câu này vào
    //if (empty($hotel_location) && empty($hotel_free_airport)) $meta_description   .=  ', nhiều tiện ích miễn phí';
}

//Cho thêm Miễn phí đưa đón sân bay nếu có
if (!empty($hotel_free_airport)) {
    $meta_description   .=  ', ' . mb_strtolower($hotel_free_airport['string']);
}

if (mb_strlen($meta_description) < 60) {
    $meta_description   .=  ', chất lượng dịch vụ tốt';
}
$meta_description   .=  ', phòng view đẹp, nhiều hình ảnh và đánh giá mới nhất. Vietgoing có giá tốt';
$meta_description   .=  ', tặng mã khuyến mại.';

//Nếu KS có giá phòng thì cho giá vào title để nhìn cho hấp dẫn
$price_min  =   0;
if (!empty($rooms[0]['price_min'])) {
    
    //Lấy giá thấp nhất của phòng đầu tiên
    $price_min  =   $rooms[0]['price_min'];
    
    //Cho giá thấp hơn chút để nhìn cho hấp dẫn
    $price_min  =   round_number($price_min * 0.8);
}

if ($price_min > 0) {
    $meta_title .=  ' giá chỉ ' . format_number($price_min, 0, ',') . '₫';
    //if (mb_strlen($meta_description) < 150) $meta_description   .=  ', Vietgoing có quỹ phòng giá rẻ nhiều ưu đãi';
} else {
    $meta_title .=  ' giá mới nhất';
    //if (mb_strlen($meta_description) < 150) $meta_description   .=  ', cam kết giá tốt, hoàn tiền nếu không hài lòng';
}
$meta_title .=  ', giảm đến 30%';

/*
echo    '(' . mb_strlen($meta_title) . '): ' . ($meta_title) . '<br>';
echo    '(' . mb_strlen($meta_description) . '): ' . ($meta_description) . '<br>';
*/

//Check xem trong tên của KS mà chưa có tên của loại KS thì gán vào ở thẻ h1 và meta description
//Phải xuống đoạn này mới gán để ko bị cho Loại KS vào meta title sẽ bị dài quá ko cần thiết
if ($hotel_info['hot_type'] != 6 && $hotel_info['hot_type'] != 0) {   //Ko gán Tổ hợp vào, chẳng hạn như tổ hợp của Vin thì ko cần
    if (strpos($hotel_info['hot_name'], $cfg_hotel_type[$hotel_info['hot_type']]) === false) {
        $page_h1    =   $cfg_hotel_type[$hotel_info['hot_type']] . ' ' . $page_h1;
        //$meta_description   =   $cfg_hotel_type[$hotel_info['hot_type']] . ' ' . $meta_description;
    }
}

//Lấy tên gọi của loại hình để show ra cho phù hợp
$hotel_type =   'Khách sạn';
if ($hotel_info['hot_type'] == 3 || $hotel_info['hot_type'] == 5 || $hotel_info['hot_type'] == 2) {
    $hotel_type =   $cfg_hotel_type[$hotel_info['hot_type']];
}

/** Schema **/
$schema_more    =   '';
//Giá
$price_min  =   200000000;
$price_max  =   0;
foreach ($rooms as $row) {
    if ($row['price_min'] > 0 && $row['price_min'] < $price_min)    $price_min  =   $row['price_min'];
    if ($row['price_min'] > 0 && $row['price_min'] > $price_max)    $price_max  =   $row['price_min'];
}
if ($price_max > $price_min && $price_min > 0) {
    $schema_more    .=  ',
    "priceRange": "' . format_number($price_min * 0.8, 0, '.') . ' - ' . format_number($price_max, 0, '.') . '"';
}

//Điểm đánh giá
if ($hotel_info['hot_review_score'] > 0) {
    $schema_more    .=  ',
    "aggregateRating": {
         "@type": "AggregateRating",
         "ratingValue": "' . $hotel_info['hot_review_score'] . '",
         "bestRating": "10",
         "ratingCount": "' . $hotel_info['hot_review_count'] . '"
     }
    ';
}

$schema_html    =   '
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Hotel",
    "name": "' . replace_single_quotes($page_h1) . '",
    "description": "' . replace_single_quotes($meta_description) . '",
    "url": "' . $url_canonical . '",
    "image": "' . $page_image . '",
    "telephone": "0931666900",
    "address": "' . replace_single_quotes($hotel_info['hot_address_full']) . '",
    "starRating": ' . $hotel_info['hot_star'] . $schema_more . '
}
</script>';

/**
 * Xử lý mục lục
 */
$table_of_content = new TableOfContent;
$table_of_content->append("#box_hotel_gallery", "Ảnh khách sạn");
$table_of_content->append("#box_list_room", "Danh sách phòng");
$table_of_content->append("#box_hotel_policy", "Chính sách");
$table_of_content->append("#box_hotel_attribute", "Tiện ích");
if (!empty($list_destination_center[$hotel_id]) || !empty($hotel_location)) {
    $table_of_content->append("#box_hotel_place", "Vị trí");
}
$table_of_content->append("#box_hotel_description", "Giới thiệu");
$table_of_content->append("#box_review_detail", "Đánh giá");

$Layout->setTitle($meta_title)
        ->setDescription($meta_description)
        ->setKeywords($page_h1 . ', đặt phòng, giá tốt, giá mới nhất ' . date('Y') . ', ưu đãi, còn phòng trống, ' . BRAND_NAME . ', đảm bảo hoàn tiền')
        ->setCanonical($url_canonical)
        ->setImages([
                    'src'   =>  $page_image,
                    'alt'   =>  $page_h1
                ])
        ->setJS(['page.detail']);
if ($hotel_info['price_min'] > 0) {
    $Layout->setAmount($hotel_info['price_min']);
}
?>