<?
include('../../../Core/Config/require_web.php');

/**
 * Lưu ý quan trọng:
 * Khi thay đổi/thêm các param search thì phải kiểm tra kỹ thứ tự để tránh bị thay đổi URL Canonical 
 */

//Thông tin page
$default_title  =   'Khách sạn ';
$place_name     =   ''; //Tên địa danh chính
$place_name_full    =   ''; //Tên địa danh bao gồm cả Tỉnh/TP
$place_name_h1  =   ''; //Tên của place để gắn vào h1
$add_meta_desc  =   ''; //Đoạn text thêm vào description nếu filter theo type hotel

//Các đoạn text sẽ được thêm vào các thẻ meta
$add_meta_before = $add_meta_after = '';

//Set module để active màu menu, ko muốn dùng ở .htaccess
$page_module    =   'hotel';
$page_near_by   =   false;

//Câu SQL lấy dữ liệu
$sql_filter     =   "hot_active = 1";
$sql_having     =   "";
$sql_join       =   "";
$sql_order_by   =   "has_price DESC";
$page_current   =   get_current_page();
$page_size      =   15;
$arr_hotel_id   =   [];
$field_distance =   ""; //Thêm trường tính khoảng cách trong trường hợp lấy các KS ở gần

//Câu SQL để select các dữ liệu distinct, chỉ where theo Tỉnh/TP là đối tượng lớn nhất, ko được dùng sql_filter vì sẽ làm mất đi các value
$sql_distinct   =   "hot_active = 1";

$tmp                = $HotelModel->getTableRoomPrice();
$tables_prices      = $tmp["tables"];
$checkin_out_int    = $tmp["daterange_int"];
unset($tmp);

//Lấy các thông tin filter
$search_type    =   getValue('group', GET_STRING, GET_GET, '');
$search_id      =   getValue('id');
$sort_type      =   getValue('sort', GET_STRING, GET_GET, '');
$has_filter     =   false;  //Biến để đánh dấu show nút clear bộ lọc

//Lấy city ID để dùng query ra quận huyện
$city_id    =   0;
$page_type  =   'city'; //Đánh dấu là page của Tỉnh/TP hay quận huyện để show địa chỉ phù hợp

//Mảng chứa các item của breadcrum và các item được dùng để generate ra URL canonical
$arr_breadcrum =    $arr_canonical  =   [];

//Mảng chứa các câu hỏi liên quan đến đối tượng chính của page
$question_relate    =   [];
$sql_question       =   "";

//Câu gợi ý đặt giá rẻ hơn
$text_suggest_bonus =   $cfg_website['con_text_bonus_hotel'];

/** Ảnh đại diện của page để show khi share link trên các social **/
$page_image     =   $cfg_default_image;
$mapview_type   =   $search_type;
$mapview_id     =   $search_id;
$location       =   [];

/** Tùy từng type search để lấy ra câu sql và title tương ứng **/
switch ($search_type) {
    case 'city':
        $search_info    =   $DB->query("SELECT cit_id, cit_name, cit_text_bonus, cit_name AS name, cit_image, cit_content AS description, cit_active AS item_active, cit_attribute_value_empty AS attribute_empty
                                        FROM city
                                        WHERE cit_id = $search_id")
                                        ->getOne();
        if (empty($search_info)) {
            include('../../../soft_404.php');
            exit;
        }
        $city_id            =   $search_id;
        $sql_filter         .=  " AND hot_city = $city_id";
        $sql_distinct       .=  " AND hot_city = $city_id";
        $place_name         =   $search_info['cit_name'];
        $place_name_full    =   $search_info['cit_name'];
        $place_name_h1      =   $place_name_full;
        $place_url          =   $Router->listHotelCity($search_info);
        if (!empty($search_info['cit_image']))  $page_image =   $Router->srcPlace($search_info['cit_image']);

        //Lấy câu gợi ý theo Tỉnh/TP, đồng thời lấy ra theo Quận/Huyện để nếu có câu riêng của từng Quận/Huyện thì lấy theo Quận/Huyện
        if (!empty($search_info['cit_text_bonus']))   $text_suggest_bonus =   $search_info['cit_text_bonus'];
        $array_text_suggest =   [];
        $districts  =   $DB->query("SELECT dis_id, dis_text_bonus
                                    FROM district
                                    WHERE dis_city = $city_id AND dis_text_bonus != ''")
                                    ->toArray();
        foreach ($districts as $d) {
            $array_text_suggest[$d['dis_id']]   =   $d['dis_text_bonus'];
        }
    
        //Breadcrum bar
        $arr_breadcrum  =   [
                            ['link' => $place_url, 'text' => $search_info['cit_name']]
                            ];
        // sql tìm tour xung quanh
        $sql_where_tour =   "des_city = $city_id";
        $sql_question   =   "city = $city_id";
        $url_tour_list  =   $Router->listTourCity($search_info);
        $url_destination_list   =   $Router->listDestinationCity($search_info);
    break;
    
    case 'district':
        $search_info    =   $DB->query("SELECT dis_id, dis_name, dis_name_show, dis_text_bonus, dis_hot, dis_image, cit_id, cit_name, cit_image, cit_active, cit_text_bonus, dis_content AS description, dis_active AS item_active, dis_attribute_value_empty AS attribute_empty
                                        FROM district
                                        INNER JOIN city ON dis_city = cit_id
                                        WHERE dis_id = $search_id")
                                        ->getOne();
        if (empty($search_info)) {
            include('../../../soft_404.php');
            exit;
        }
        $district_name  =   $PlaceModel->getDistrictName($search_info, true);
        $search_info['name']    =   $district_name;
        $city_id        =   $search_info['cit_id'];

        //Nếu Deactive cả Tỉnh/TP thì cũng ko show các Quận/Huyện ra
        if ($search_info['cit_active'] != 1)    $search_info['item_active'] =   0;
        
        $sql_filter         .=  " AND hot_district = " . $search_info['dis_id'];
        $sql_distinct       .=  " AND hot_city = $city_id";
        $place_name         =   $district_name;
        $place_name_full    =   ($district_name == $search_info['cit_name'] ? $search_info['dis_name'] : $district_name . ', ' . $search_info['cit_name']);
        $place_name_h1      =   ($search_info['dis_hot'] == 1 ? $district_name : $place_name_full);
        $place_url          =   $Router->listHotelDistrict($search_info);
        if (!empty($search_info['dis_text_bonus'])) {
            $text_suggest_bonus =   $search_info['dis_text_bonus'];
        } else if (!empty($search_info['cit_text_bonus'])) {
            $text_suggest_bonus =   $search_info['cit_text_bonus'];
        }
        
        if (!empty($search_info['dis_image'])) {
            $page_image =   $Router->srcPlace($search_info['dis_image']);
        } else {
            if (!empty($search_info['cit_image']))  $page_image =   $Router->srcPlace($search_info['cit_image']);
        }
        
        //Breadcrum bar
        $arr_breadcrum  =   [
                            ['link' => $Router->listHotelCity($search_info), 'text' => $search_info['cit_name']],
                            ['link' => $place_url, 'text' => ($district_name == $search_info['cit_name'] ? $search_info['dis_name'] : $district_name)]
                            ];
        
        //Lấy danh sách Quận để show địa chỉ
        $list_wards =   $Model->getListData('ward', 'war_id, war_name', 'war_district = ' . $search_id);
        $page_type  =   'district';
        
        // sql tìm tour xung quanh
        $sql_where_tour =   "des_district = {$search_info['dis_id']}";
        $sql_question   =   "district = {$search_info['dis_id']}";
        $url_tour_list = $Router->listTourDistrict($search_info);
        $url_destination_list   =   $Router->listDestinationDistrict($search_info);
    break;
    
    default:
        /** Lấy các KS ở gần theo vị trí của user trên bản đồ **/
        $current_location   =   getValue('vg_location', GET_STRING, GET_SESSION, '');
        $has_location   =   false;
        if (!empty($current_location)) {
            $location   =   json_decode($current_location, true);
            if (!empty($location['lat']) && !empty($location['lng']) && !empty($location['address'])) {
                
                //Đánh dấu là đã có tọa độ rồi để ko chuyển đến trang lấy tọa độ nữa
                $has_location   =   true;
                $page_near_by   =   true;
                
                //Bóc tách address để lấy city, district
                //Address dạng: 319/505, Tam Trinh, Quận Hoàng Mai, Thành Phố Hà Nội, Hoàng Văn Thụ, Hoàng Mai, Hà Nội, Vietnam
                $exp    =   explode(',', $location['address']);
                $arr    =   array_reverse($exp);
                $city_id    =   0;
                $district_id    =   0;
                $distance_limit =   10;  //Tùy theo đang ở TP Hot hay ko mà lấy giới hạn khoảng cách khác nhau. VD ở ĐN thì 1km thôi, nhưng ở BG thì 20km.
                
                //Tách address ra rồi duyệt từ Vietnam ngược lại, cái nào khớp thì lấy ra thành Tỉnh/TP
                foreach ($arr as $e) {
                    $e  =   trim($e);
                    if ($city_id <= 0) {
                        $name   =   str_replace(['Tỉnh', 'tỉnh', 'Thành Phố', 'thành phố', 'Thành phố'], '', $e);
                        $name   =   replaceMQ($name);
                        $row    =   $DB->query("SELECT cit_id
                                                FROM city
                                                WHERE cit_name = '$name'")
                                                ->getOne();
                        if (!empty($row)) {
                            $city_id    =   $row['cit_id'];
                        }
                    }
                    if ($district_id <= 0) {
                        $name   =   str_replace(['Quận', 'Huyện', 'Thị Xã', 'quận', 'huyện', 'thị xã', 'Thị xã'], '', $e);
                        $name   =   replaceMQ($name);
                        $row    =   $DB->query("SELECT dis_id
                                                FROM district
                                                WHERE dis_name = '$name'")
                                                ->getOne();
                        if (!empty($row)) {
                            $district_id    =   $row['dis_id'];
                        }
                    } else {
                        //Lấy được ID rồi thì break để ko lặp nữa
                        break;
                    }
                }
                
                //Nếu ko lấy được tên Tỉnh/TP thì cho mặc định là HN luôn
                if ($city_id <= 0) {
                    $city_id    =   1;
                    //Lưu log lại để check xem địa chỉ ấy như nào mà lại ko lấy được City
                    save_log('location_404.cfn', $current_location);
                }

                // Thiết lập chế độ xem bản đồ là loại "around"
                $mapview_type   =   'around';
                $mapview_id     =   $city_id ."_". $district_id;
                
                //Nếu lấy được Quận/Huyện thì set cho đối tượng chính là Quận/Huyện
                if (!empty($district_id)) {
                    $search_info    =   $DB->query("SELECT dis_id, dis_name, dis_name_show, dis_hot, dis_image, cit_id, cit_name, cit_image,
                                                        dis_content AS description, dis_active AS item_active
                                                    FROM district
                                                    INNER JOIN city ON dis_city = cit_id
                                                    WHERE dis_id = $district_id")
                                                    ->getOne();
                                                    
                    $district_name      =   $PlaceModel->getDistrictName($search_info, true);
                    $city_id            =   $search_info['cit_id'];
                    $place_name         =   $district_name;
                    $place_name_full    =   ($district_name == $search_info['cit_name'] ? $search_info['dis_name'] : $district_name . ', ' . $search_info['cit_name']);
                    
                    //Nếu là quận/huyện HOT thì lấy distance gần thôi, ko HOT thì lấy rộng
                    if ($search_info['dis_hot'] == 1) {
                        $distance_limit =   4;
                    } else {
                        $distance_limit =   8;
                    }
                } else {
                    $search_info    =   $DB->query("SELECT cit_id, cit_name, cit_hot, cit_name AS name, cit_image,
                                                        cit_content AS description, cit_active AS item_active
                                                    FROM city
                                                    WHERE cit_id = $city_id")
                                                    ->getOne();
                    
                    $place_name         =   $search_info['cit_name'];
                    $place_name_full    =   $search_info['cit_name'];
                    
                    if ($search_info['cit_hot'] == 1) {
                        $distance_limit =   5;
                    } else {
                        $distance_limit =   10;
                    }
                }
                
                //Câu SQL having để lấy ra các KS ở trong khoảng cách cần lấy
                $sql_having .=  "
                HAVING `distance_in_km` <= " . $distance_limit; //Lấy các KS trong bán kính 10km
                
                //Thêm field distance để sắp xếp theo các KS ở gần trước
                $field_distance =   ", ST_Distance_Sphere( point ('" . $location['lng'] . "', '" . $location['lat'] . "'), 
                        point(hot_lon, hot_lat)) / 1000 
                        AS `distance_in_km`";
                        
                $sql_order_by   .=  ", distance_in_km ASC";
                
                //Một số thông tin liên quan
                $search_info['name']    =   'gần đây';
                $place_name_h1          =   'gần đây';
                $place_url  =   $cfg_path_hotel_near;
                    
                if (!empty($search_info['dis_image'])) {
                    $page_image =   $Router->srcPlace($search_info['dis_image']);
                } else {
                    if (!empty($search_info['cit_image']))  $page_image =   $Router->srcPlace($search_info['cit_image']);
                }
            
                //Breadcrum bar
                $arr_breadcrum  =   [
                                    ['link' => $cfg_path_hotel_near, 'text' => 'Khách sạn ở gần đây']
                                    ];
                // sql tìm tour xung quanh
                $sql_where_tour =   "des_city = $city_id";
                $sql_question   =   "city = $city_id";
                $url_tour_list  =   $Router->listTourCity($search_info);
                $url_destination_list   =   $Router->listDestinationCity($search_info);
            }
        }
        if (!$has_location) {
            include('../../../location.php');
            exit;
        }
}

/** Gán URL Canonical ban đầu bằng URL gốc của place **/
$url_canonical      =   $place_url;
$search_action_form =   $place_url;
$search_keyword     =   $place_name;
if ($place_url == $cfg_path_hotel_near) $search_keyword =   'Khách sạn ở gần đây';
//Redirect về link đúng trong trường hợp tên của đối tượng bị sửa => URL bị sửa
redirect_correct_url($url_canonical);

/** Search theo giá **/
$price_range    =   getValue('price_range', GET_STRING, GET_GET, '');
if ($price_range != '') {
    $exp_price  =   explode(';', $price_range);
    if (isset($exp_price[0]) && isset($exp_price[1])) {
        $from   =   (int)$exp_price[0];
        $to     =   (int)$exp_price[1];
        
        //Nếu giá nhập hợp lệ thì mới filter
        if ($to >= $from && $to <= MAX_PRICE_TOUR) {
            $sql_filter .=  " AND hot_price BETWEEN $from AND $to";
            $has_filter =   true;
            
            //Gán vào mảng param canonical, lưu ý giữ đúng thứ tự code từ trên xuống dưới
            //13/09/22: Tạm bỏ đi các param phụ
            //$arr_canonical['price_range']   =   $price_range;
            
        }
    }
}

//Lọc theo loại lưu trú
$filter_type    =   getValue('type', GET_STRING, GET_GET, '', 1);
$type_id        =   0;  //Lấy ID để so sánh cho chính xác
if (!empty($filter_type)) {
    
    //Lấy ID của type
    $row    =   $DB->query("SELECT hty_id, hty_name FROM hotel_type WHERE hty_name = '" . $filter_type . "'")->getOne();
    
    if (!empty($row)) {
        $sql_filter     .=  " AND hot_type = " . $row['hty_id'];
        $default_title  =   $row['hty_name'] . ' ';
        $type_id        =   $row['hty_id'];
        
        //Gán vào mảng param canonical, lưu ý giữ đúng thứ tự code từ trên xuống dưới
        $arr_canonical['type'] =   $row['hty_name'];
        $has_filter =   true;
        
        //Nếu ko phải là lọc theo khách sạn thì mới thêm Loại hình lưu trú vào meta description
        if (mb_strtolower($row['hty_name'], 'UTF-8') != 'khách sạn')    $add_meta_desc  .=  ', ' . $row['hty_name'];
    }
}

//Lọc theo sao
$filter_star    =   getValue('star');
if ($filter_star > 0 && $filter_star <= 6) {
    $sql_filter .=  " AND hot_star = " . $filter_star;
    
    //Gán vào mảng param canonical, lưu ý giữ đúng thứ tự code từ trên xuống dưới
    $arr_canonical['star'] =   $filter_star;
    $has_filter =   true;
    
    $default_title  .=  $filter_star . ' sao';
    $add_meta_desc  .=  ' ' . $filter_star . ' sao';
}

/** Bộ lọc Attribute **/
$list_attribute =   $AttributeModel->getAttributeOfGroup(GROUP_HOTEL, " AND atn_show_filter = 1 AND atv_hot = 1");
//dd($list_attribute);

//Xử lý bộ lọc Attribute bằng Class AttributeModel và trả về các data cần thiết: query, meta, selected, canonical...
$filter_attribute   =   $AttributeModel->generateSQLFilter(GROUP_HOTEL);
//dump($filter_attribute);
$sql_filter         .=  $filter_attribute['query'];
$add_meta_before    .=  $filter_attribute['meta'];
$attribute_selected =   $filter_attribute['selected'];
$arr_canonical      =   array_merge($arr_canonical, $filter_attribute['canonical']);
if (!empty($attribute_selected)) {
    $has_filter =   true;
}

/** Sắp xếp **/
$sort_title =   'Sắp xếp';
switch ($sort_type) {
    case 'price-asc':
        $sql_order_by   .=  ", hot_price";
        $sort_title     =   'Giá tăng dần';
    break;
        
    case 'price-desc':
        $sql_order_by   .=  ", hot_price DESC";
        $sort_title     =   'Giá giảm dần';
    break;
    
    case 'review':
        $sql_order_by   .=  ", hot_review_score DESC";    //Sắp xếp theo đánh giá thì chỉ có xếp từ cao đến thấp
        $sort_title     =   'Đánh giá cao';
    break;
    
    case 'favorite':
        $sql_order_by   .=  ", hot_count_booking DESC";   //Sắp xếp theo đặt nhiều thì chỉ có xếp từ cao đến thấp
        $sort_title     =   'Được đặt nhiều';
    break;
}

if (!empty($sort_type)) {
    //Gán vào mảng param canonical, lưu ý giữ đúng thứ tự code từ trên xuống dưới
    $arr_canonical['sort']  =   $sort_type;
    $has_filter =   true;
}

/**
 * Logic sắp xếp:
 * Luôn ưu tiên các KS có giá cho lên trên
 * Mặc định là Has_Price => Top => Price => Booked => View
 */
$sql_order_by   .=  ", hot_top DESC";
//Nếu ko phải sắp xếp theo giá thì mặc định cho KS giá nhỏ lên trên
if ($sort_type != 'price-asc' && $sort_type != 'price-desc') {
    //$sql_order_by   .=  ", hot_price ASC";
}
if ($sort_type != 'favorite') {
    $sql_order_by   .=  ", hot_count_booking DESC";
}
$sql_order_by   .=  ", hot_count_view DESC";

// Sql xử lý gộp tất cả các table chứa giá vào làm một

$tmp_union = [];
foreach($tables_prices as $i => $tbl) {
    // Lấy giá ăn sáng/thường
    $tmp_union[] = "(SELECT 
                        rop_hotel_id,
                        rop_price AS hot_price
                        FROM {$tbl} AS tbl
                        INNER JOIN room ON rop_room_id = roo_id
                        WHERE rop_day BETWEEN {$checkin_out_int['from']} 
                            AND {$checkin_out_int['to']} AND roo_active = 1
                            AND rop_type = ". CON_PRICE_TYPE_CLIENT ." AND rop_price > 0
                    )";
}
$tmp_union   = implode(" UNION ", $tmp_union);

// Sql gộp table price
$sql_price = "{$tmp_union} ORDER BY hot_price ASC"; // ASC để lấy giá nhỏ nhất của phòng 

//Câu SQL lọc dữ liệu ks
//$field_hotel .= ", hot_top + hot_hot + IF(hot_price > 0, 1, 0) AS hotel_top, hot_price";
$field_hotel    .=  ", hot_price, IF(hot_price > 0, 1, 0) AS has_price, hot_top, hot_count_booking, hot_count_view";

$sql_query  =   "SELECT $field_hotel $field_distance
                FROM hotel
                LEFT JOIN (
                    SELECT rop_hotel_id, hot_price
                    FROM ({$sql_price}) AS tbl 
                    GROUP BY tbl.rop_hotel_id
                ) AS tbl_price ON hot_id = tbl_price.rop_hotel_id 
                $sql_join
                WHERE $sql_filter $sql_having";

$total_record   =   $DB->count(str_replace($field_hotel, "COUNT(DISTINCT hot_id) AS total", $sql_query));

//$sql_query      .=  " ORDER BY ". ($sort_type == 'DESC' ? " $sql_order_by, - hot_price DESC" : "hotel_top DESC, - hot_price DESC, $sql_order_by"); // - hot_price dùng để đẩy các ks giá <1 về cuối trong mọi truờng hợp sort
//$sql_query      .=  " ORDER BY ". ($sort_type == 'DESC' ? " $sql_order_by" : "has_price DESC, hot_top DESC, hot_hot DESC, hot_type ASC, $sql_order_by"); // - hot_price dùng để đẩy các ks giá <1 về cuối trong mọi truờng hợp sort

$sql_query  .=  " ORDER BY " . $sql_order_by;

$list_hotel =   $DB->query("$sql_query LIMIT " . (($page_current - 1) * $page_size) . ",$page_size")->toArray();

//dump($sql_query);
//dump($list_hotel);
/*
save_log('test.cfn', 'Ghi log thành công file data_list.php');
save_log('query_debug.cfn', $sql_query);
save_log('data_hotel.cfn', json_encode($list_hotel));
*/

/**
 * Có một số bộ lọc gây ra nhiều URL ko có dữ liệu (Kiểu KS ở Sapa lại có bộ lọc Type hotel là Du thuyền), nên sẽ lấy distinct các value của
 * các attribute mà có dữ liệu để show lọc, cái nào ko có thì ko show link 
 **/
$arr_distinct   =   [
    'star'      =>  [],
    'type'      =>  [],
    'district'  =>  []
];
//Loại các Attribute mà ko có dữ liệu đi để ko bị Google quét quá nhiều link thừa ko có dữ liệu

if (!empty($search_info['attribute_empty'])) {
    $arr_attribute_value    =   json_decode($search_info['attribute_empty'], true);
    if (!empty($arr_attribute_value)) {
        foreach ($list_attribute as $atn_id => $atn) {
            foreach ($atn['data'] as $atv_id => $atv) {
                if (!in_array($atv_id, $arr_attribute_value))    unset($list_attribute[$atn_id]['data'][$atv_id]);
            }
        }
    }
}

//dd($list_attribute);

//Star
$get    =   $DB->query("SELECT DISTINCT(hot_star) FROM hotel WHERE $sql_distinct")->toArray();
foreach ($get as $r) {
    $arr_distinct['star'][] =   $r['hot_star'];
}

//Distinct hot_type
$get    =   $DB->query("SELECT DISTINCT(hot_type) FROM hotel WHERE $sql_distinct")->toArray();
foreach ($get as $r) {
    $arr_distinct['type'][] =   $r['hot_type'];
}
//Distinct hot_district
$get    =   $DB->query("SELECT DISTINCT(hot_district) FROM hotel WHERE $sql_distinct")->toArray();
foreach ($get as $r) {
    $arr_distinct['district'][] =   $r['hot_district'];
}

//Lấy các Quận/Huyện để filter
$arr_district   =   $Model->getListData('district', 'dis_id, dis_name', 'dis_city = ' . $city_id, 'dis_hot DESC, dis_name', 'row');

$list_districts =   [];
$district_id    =   [];
$arr_district_filter    =   [];
foreach ($arr_district as $row) {
    
    //Loại các value ko có trong arr_distinct
    if (!in_array($row['dis_id'], $arr_distinct['district'])) continue;
    
    $arr_district_filter[]  =   [
                                'id'    =>  $row['dis_id'],
                                'link'  =>  $Router->listHotelDistrict($row, param_box_web(25)),
                                'text'  =>  $row['dis_name']
                                ];
    $district_id[]  =   $row['dis_id'];
    $list_districts[$row['dis_id']] =   $row['dis_name'];   //Gán vào mảng để show địa chỉ
        
}

//Nếu chưa có list_wards (Tức là đang xem trang listing city) thì lấy ra list ward để cho nhẹ hơn
if (empty($list_wards) && !empty($district_id)) {
    $list_wards =   $Model->getListData('ward', 'war_id, war_name', 'war_district IN (' . implode(',', $district_id) . ')');
}

// Lấy địa điểm trung tâm
$list_destination_center = $HotelModel->getDestinationCenter($list_hotel);

/** Lấy ra các combo **/
$list_combo_hotel   =   $HotelModel->getComboByHotels($list_hotel);

//Add canonical cho phân trang
//Gán vào mảng param canonical, lưu ý giữ đúng thứ tự code từ trên xuống dưới
if ($page_current > 1) {
    $arr_canonical['page'] =   $page_current;
}

//Generate ra URL canonical
if (!empty($arr_canonical)) {
    $url_canonical  .=  '?' . http_build_query($arr_canonical);
    $url_canonical  =   rawurldecode($url_canonical);
}

/** List collection liên quan **/
$list_collection    =   $Model->getCollectionRelate($city_id, GROUP_HOTEL);


/** Các tour liên quan **/
$list_tour  =   $DB->query("SELECT $field_tour
                            FROM tour
                            INNER JOIN tour_destination ON tou_id = tode_tour_id
                            INNER JOIN destination ON tode_destination_id = des_id
                            WHERE {$sql_where_tour} AND tou_active = 1
                            GROUP BY tou_id
                            ORDER BY tou_top DESC, tou_hot DESC, tou_last_update DESC
                            LIMIT 8")
                            ->toArray();

/** Bài viết liên quan địa điểm **/
$list_article  =   $DB->query("SELECT art_id, art_title
                                FROM destination_article
                                INNER JOIN destination ON dear_destination_id = des_id
                                INNER JOIN article ON dear_article_id = art_id
                                WHERE {$sql_where_tour} AND art_active = 1
                                GROUP BY art_id
                                ORDER BY art_last_update DESC
                                LIMIT 10")
                                ->toArray();

/** Danh sách địa điểm nổi bật **/
$list_destination  =   $DB->query("SELECT *
                                    FROM destination
                                    WHERE {$sql_where_tour}  AND des_active = 1
                                    ORDER BY des_is_center DESC, des_hot DESC, des_last_update DESC
                                    LIMIT 8")
                                    ->toArray();

/** Lấy các KS HOT, Destination HOT để gán vào các câu hỏi liên quan **/
//KS
$qa_data    =   $DB->query("SELECT hot_id, hot_name, hot_type
                            FROM hotel
                            WHERE hot_{$sql_question} AND hot_famous = 1 AND hot_active = 1
                            ORDER BY RAND()")
                            ->toArray();

if (!empty($qa_data)) {
    $arr_hotel = $arr_resort = $arr_cruises =   [];
    foreach ($qa_data as $row) {
        $item_famous    =   '<a href="' . $Router->detailHotel($row, param_box_web(157)) . '">' . $row['hot_name'] . '</a>';
        if ($row['hot_type'] == 1) {    //KS
            if (count($arr_hotel) < 10) $arr_hotel[]    =   $item_famous;   //Limit 10 cái để nhìn cho đỡ bị dài
        } else if ($row['hot_type'] == 2 || $row['hot_type'] == 6) { //Resort, Tổ hợp nghỉ dưỡng
            if (count($arr_resort) < 10) $arr_resort[]   =   $item_famous;  //Limit 10 cái để nhìn cho đỡ bị dài
        } else if ($row['hot_type'] == 5) { //Du thuyền
            if (count($arr_cruises) < 10) $arr_cruises[]  =   $item_famous; //Limit 10 cái để nhìn cho đỡ bị dài
        }
        //Ko SEO cho homestay
    }
    
    //Show từng câu hỏi của từng loại
    if (!empty($arr_hotel)) {
        $question_relate[]  =   [
            'title'     =>  'Khách sạn tốt nhất được ưa thích đặt nhiều ở ' . $place_name . '?',
            'content'   =>  'Các khách sạn ở ' . $place_name . ' được đánh giá tốt nhất, khách hàng ưa thích và đặt nhiều: ' . implode(', ', $arr_hotel) . '.'
        ];
    }
    if (!empty($arr_resort)) {
        $question_relate[]  =   [
            'title'     =>  'Resort nghỉ dưỡng tốt nhất và được đặt nhiều ở ' . $place_name . '?',
            'content'   =>  'Các resort nghỉ dưỡng tốt nhất, được ưa thích và lựa chọn đặt nhiều ở ' . $place_name . ': ' . implode(', ', $arr_resort) . '.'
        ];
    }
    if (!empty($arr_cruises)) {
        $question_relate[]  =   [
            'title'     =>  'Du thuyền tốt nhất được lựa chọn đặt nhiều ở ' . $place_name . '?',
            'content'   =>  'Các du thuyền tốt nhất, được ưa thích và đặt nhiều ở ' . $place_name . ': ' . implode(', ', $arr_cruises) . '.'
        ];
    }
}

//Lấy các KS ở Trung tâm, gần biển, gần sân bay
$attr_location  =   $AttributeModel->getAttributeOfGroup(GROUP_HOTEL, " AND atn_id = " . AttributeModel::ATTR_HOTEL_LOCATION);
$column =   $attr_location[AttributeModel::ATTR_HOTEL_LOCATION]['info']['column'];   //14 là ID của attribute location

foreach ($attr_location[AttributeModel::ATTR_HOTEL_LOCATION]['data'] as $id => $atn) {
    $qa_data    =   $DB->query("SELECT hot_id, hot_name, hot_type
                                FROM hotel
                                WHERE hot_{$sql_question} AND hot_active = 1 AND hot_col_{$column} >= " . $atn['value'] . " AND hot_col_{$column} & " . $atn['value'] . "
                                ORDER BY RAND()
                                LIMIT 10")
                                ->toArray();
    if (!empty($qa_data)) {
        $arr_hotel  =   [];
        foreach ($qa_data as $row) {
            $arr_hotel[]    =   '<a href="' . $Router->detailHotel($row, param_box_web(157)) . '">' . $row['hot_name'] . '</a>';
        }
        $name   =   mb_strtolower($atn['name']);
        $question_relate[]  =   [
            'title'     =>  'Khách sạn ở ' . $place_name . ' ' . $name . '?',
            'content'   =>  'Các khách sạn ' . $place_name . ' ở ' . $name . (count($arr_hotel) >= 10 ? ' nổi bật' : '') . ' là: ' . implode(', ', $arr_hotel) . '.'
        ];
    }
}

//Destination
$qa_data    =   $DB->query("SELECT des_id, des_name
                            FROM destination
                            WHERE des_{$sql_question} AND des_active = 1 AND (des_is_center = 1 OR des_hot = 1)
                            ORDER BY RAND()
                            LIMIT 10")
                            ->toArray();
if (!empty($qa_data)) {
    $list   =   [];
    foreach ($qa_data as $row) {
        $list[] =   '<a href="' . $Router->detailDestination($row, param_box_web(158)) . '" class="open-modal-destination" target="_tblank" data-id="' . $row['des_id'] . '" data-box="158">' . $row['des_name'] . '</a>';
    }
    $question_relate[]  =   [
        'title'     =>  'Địa điểm du lịch đẹp nhất ở ' . $place_name . '?',
        'content'   =>  'Các địa điểm du lịch nổi tiếng và đẹp nhất ở ' . $place_name . ': ' . implode(', ', $list) . '.'
    ];
}

//Các câu hỏi riêng
$qa_data    =   $DB->query("SELECT coq_title, coq_content
                            FROM common_questions
                            WHERE coq_type = ". GROUP_HOTEL ." AND coq_{$sql_question} AND coq_active = 1
                            ORDER BY coq_district, coq_order
                            LIMIT 10")
                            ->toArray();
foreach ($qa_data as $row) {
    $question_relate[]  =   [
        'title'     =>  $row['coq_title'],
        'content'   =>  $row['coq_content']
    ];
}

//dd($question_relate);

/** Lấy giá thấp nhất của ngày hiện tại để ghép vào meta title **/
$price_min  =   200000000;
$price_max  =   0;
foreach ($list_hotel as $row) {
    if ($row['hot_price'] > 0 && $row['hot_price'] < $price_min)    $price_min  =   $row['hot_price'];
    if ($row['hot_price'] > 0 && $row['hot_price'] > $price_max)    $price_max  =   $row['hot_price'];
}

if ($price_min != 200000000) {
    //Với mỗi hạng KS thì cho mức giá min fake khác nhau cho nó phù hợp, VD KS 4-5 sao thì giá ko thể vài trăm K được
    $price_limit    =   $filter_star < 4 ? 500000 : 1000000;
    
    //Cho giá thấp hơn mức limit để nhìn cho hấp dẫn
    while ($price_min > $price_limit) {
        $price_min  =   $price_min * 0.8;
    }
    $price_min  =   round_number($price_min);
}

/** Các title **/
$add_meta   =   mb_strtolower($add_meta_before, 'UTF-8') . $add_meta_after;
$default_title  =   trim($default_title);
$page_h1    =   $default_title . ' tốt nhất tại ' . $place_name_h1;
$meta_title =   $default_title . ' ' . $place_name_h1 . ($price_min != 200000000 ? ' giá chỉ từ ' . format_number($price_min, 0, ',') . '₫' : ' tốt nhất ' . date('Y')) . $add_meta;

//Nếu title ngắn quá thì nối thêm tí vào
/* 07/03/2023 - Bỏ đi
$title_length   =   mb_strlen($meta_title, 'UTF-8');
if ($title_length <= 45) {
    $meta_title .=  ' | ' . (mb_strlen($meta_title, 'UTF-8') <= 30 ? 'Vietgoing ưu đãi đến 70%' : 'Ưu đãi đến 70%');
}
*/

//$meta_description   =   'Đặt phòng ' . $meta_title . ' nhiều ưu đãi, còn phòng trống xác nhận ngay, được chăm sóc và hỗ trợ dịch vụ 24/7, cam kết hoàn tiền nếu hết phòng.';
$meta_description   =   'Khách sạn' . mb_strtolower($add_meta_desc, 'UTF-8') . ' tốt nhất tại ' . $place_name_h1 . $add_meta . ' có view đẹp, gần trung tâm, nhiều hình ảnh và đánh giá thực tế, được tặng mã giảm giá và hỗ trợ 24/7 bởi đội ngũ tư vấn chuyên nghiệp, cam kết hài lòng.';
/*
echo    '(' . mb_strlen($meta_title) . '): ' . ($meta_title) . '<br>';
echo    '(' . mb_strlen($meta_description) . '): ' . ($meta_description) . '<br>';
*/

if ($page_current > 1) {
    $meta_title         .=  ' | Trang ' . $page_current;
    $meta_description   .=  ' Trang ' . $page_current . '.';
}


$Layout->setTitle($meta_title . ' - Vietgoing')
        ->setDescription($meta_description)
        ->setKeywords('đặt phòng, khách sạn, ' . $meta_title . ', giá rẻ, giá mới nhất ' . date('Y') . ', còn phòng trống, xác nhận ngay, nhiều ưu đãi, cam kết hoàn tiền')
        ->setImages(['src' => $page_image, 'alt' => $page_h1])
        ->setCanonical($url_canonical)
        ->setJS(['page.list']);

//dump($meta_title);
//dump($meta_description);
//dump($url_canonical);

/** Schema **/
$schema_more    =   '';
if (!empty($list_hotel)) {
    $arr_schema =   [];
    $i  =   0;
    foreach ($list_hotel as $row) {
        $arr_schema[]   =   '{
                        "@type": "ListItem",
                        "position": ' . (++$i)  .',
                        "name": "' . replace_single_quotes($row['hot_name']) . '",
                        "url": "' . $Router->detailHotel($row) . '"
                        }';
    }
    
    if (!empty($arr_schema)) {
        $schema_more    =   ',
  "itemListElement": [' . implode(',', $arr_schema) . ']';
    }
}

$schema_html    =   '
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "ItemList",
  "name": "' . replace_single_quotes($meta_title) . '",
  "description": "' . replace_single_quotes($meta_description) . '",
  "url": "' . $url_canonical . '",
  "image": "' . $page_image . '"' . $schema_more . '
}
</script>';
?>