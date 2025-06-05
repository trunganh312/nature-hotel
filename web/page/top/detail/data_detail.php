<?
include('../../../Core/Config/require_web.php');
include('../../../Core/Classes/TableOfContent.php');

/**
 * Trang chi tiết của Bộ sưu tập
 * Là trang chi tiết của Collection nhưng thực chất sẽ là trang danh sách các item (khách sạn, tour, destination...)
 */

$collection_id  =   getValue('id');
$collection_info    =   $DB->query("SELECT collection.*, cit_id, cit_name
                                    FROM collection
                                    INNER JOIN city ON col_city = cit_id
                                    WHERE col_id = $collection_id")->getOne();
if (empty($collection_info)) {
    save_log('error_data.cfn', 'Collection ID: ' . $collection_id);
    include('../../../soft_404.php');
    exit;
}

//URL canonical
$url_canonical  =   $Router->detailCollection($collection_info);
//Redirect về link đúng trong trường hợp tên của đối tượng bị sửa => URL bị sửa
redirect_correct_url($url_canonical);

//Lấy ra ID các item của collection
$arr_item_id    =   [];
$str_item_id    =   '';
$items  =   $DB->query("SELECT coda_item_id FROM collection_data WHERE coda_collection_id = $collection_id ORDER BY coda_order")->toArray();
foreach ($items as $row) {
    $arr_item_id[]  =   $row['coda_item_id'];
}
$str_item_id    =   implode(',', $arr_item_id);

//Lấy thông tin city
$city_info  =   $Model->getRecordInfo('city', 'cit_id', $collection_info['col_city'], 'cit_id, cit_name, cit_image, cit_area');

/** Ảnh banner **/
$page_image =   $cfg_default_image;

if (!empty($city_info['cit_image']))  $page_image =   $Router->srcPlace($city_info['cit_image']);

$page_h1    =   $collection_info['col_name'];

//Nếu là các Collection Hotel mà sửa từ ngày 01/09 trở về sau thì nối thêm số năm vào title
if ($collection_info['col_group'] == GROUP_HOTEL && $collection_info['col_last_update'] > strtotime('09/01/2023 14:00:00')) {
    $page_h1    .=  ' ' . date('Y');
}

/** Lấy tên của place của bộ sưu tập để fill vào form search **/
//Gán lại thông tin city thành row để tiện generate link Xem thêm ở cuối trang
$search_keyword =   $city_info['cit_name'];
$place_name =   $city_info['cit_name'];

//Nếu có chọn Quận/Huyện của collection và là quận huyện HOT thì lấy tên theo Quận/Huyện
if ($collection_info['col_district'] > 0) {
    $district_info  =   $DB->query("SELECT dis_id, dis_name, dis_name_show, dis_hot FROM district WHERE dis_hot = 1 AND dis_id = " . $collection_info['col_district'])->getOne();
    if (!empty($district_info)) {
        $place_name =   $PlaceModel->getDistrictName($district_info);
    }
}

switch ($collection_info['col_group']) {
    case GROUP_HOTEL:
        //Set module để active màu menu, ko muốn dùng ở .htaccess
        $page_module    =   'hotel';
        
        //Lấy Data
        $tmp                =   $HotelModel->getTableRoomPrice();
        $tables_prices      =   $tmp["tables"];
        $checkin_out_int    =   $tmp["daterange_int"];
        unset($tmp);
        
        // Sql xử lý gộp tất cả các table chứa giá vào làm một
        $tmp_union = [];                   
        foreach($tables_prices as $i => $tbl) {
            // Lấy giá ăn sáng/thường      
            $tmp_union[] = "(SELECT        
                                rop_hotel_id,
                                rop_price AS hot_price
                                FROM {$tbl} AS tbl
                                WHERE rop_day BETWEEN {$checkin_out_int['from']} 
                                    AND {$checkin_out_int['to']} 
                                    AND rop_type = ". CON_PRICE_TYPE_CLIENT ."
                            )";            
        }                                  
        $tmp_union  =   implode(" UNION ", $tmp_union);
                                           
        // Sql gộp table price             
        $sql_price = "{$tmp_union} ORDER BY hot_price ASC"; 
                                           
        //Câu SQL lọc dữ liệu ks           
        $sql_query  =   "SELECT $field_hotel, hot_price, IF(hot_price > 0, 1, 0) AS has_price, hot_top, hot_count_booking, hot_count_view, hot_description
                        FROM hotel         
                        LEFT JOIN (        
                            SELECT rop_hotel_id, hot_price
                            FROM ({$sql_price}) AS tbl 
                            GROUP BY tbl.rop_hotel_id
                        ) AS tbl_price ON hot_id = tbl_price.rop_hotel_id          
                        WHERE hot_id IN($str_item_id) AND hot_active = 1
                        ORDER BY FIELD(hot_id, $str_item_id)";
        $list_hotel =   $DB->query($sql_query)->toArray();
        $search_action_form =   $Router->listHotelCity($city_info);
        
        //Breadcrum bar theo từng module
        $arr_breadcrum  =   [
            ['link' => $search_action_form, 'text' => $city_info['cit_name']]
        ];
        if (!empty($district_info)) $arr_breadcrum[]    =   ['link' => $Router->listHotelDistrict($district_info), 'text' => $place_name];
        
        //Mục lục
        $TableOfContent =   new TableOfContent;
        $i  =   0;
        foreach ($list_hotel as $row) {
            $i++;
            $TableOfContent->append('#top_item_' . $i, $row['hot_name']);
        }
        
    break;
    
    case GROUP_TOUR:
        $page_module    =   'tour';
        $list_tour  =   $DB->query("SELECT $field_tour
                                    FROM tour
                                    INNER JOIN collection_data ON (coda_item_id = tou_id)
                                    WHERE coda_collection_id = $collection_id
                                    ORDER bY coda_order")
                                    ->toArray();
        
        /** Lấy ra các loại tour để ko phải query trong vòng for **/
        $list_tour_type =   [];
        $types  =   $DB->query("SELECT atv_value_hexa, atv_name FROM attribute_value WHERE atv_attribute_id = " . ATTR_TOUR_TYPE)->toArray(); //ID của attribute Loại tour là 5
        foreach ($types as $t) {
            $list_tour_type[$t['atv_value_hexa']]   =   $t['atv_name'];
        }
        
        $search_action_form =   $Router->listTourCity($city_info);
        
        //Breadcrum bar theo từng module
        $arr_breadcrum  =   [
            ['link' => $Router->listTourCategory(['cat_id' => CATE_TOUR_VN, 'cat_name' => 'Tour trong nước']), 'text' => 'Tour trong nước'],
            ['link' => $search_action_form, 'text' => $city_info['cit_name']]
        ];
        if (!empty($district_info)) $arr_breadcrum[]    =   ['link' => $Router->listTourDistrict($district_info), 'text' => $place_name];
    break;
    
    case GROUP_DESTINATION:
        $page_module    =   'destination';
        $list_destination   =   $DB->query("SELECT des_id, des_name, des_category, des_district, des_image, des_time_open, des_time_close
                                            FROM destination
                                            INNER JOIN collection_data ON coda_item_id = des_id
                                            WHERE coda_collection_id = $collection_id
                                            ORDER BY coda_order")
                                            ->toArray();
        
        $search_action_form =   $Router->listHotelCity($city_info);
        //Breadcrum bar theo từng module
        $arr_breadcrum  =   [
            ['link' => $Router->listDestinationCity($city_info), 'text' => $city_info['cit_name']]
        ];
        
        if (!empty($district_info)) $arr_breadcrum[]    =   ['link' => $Router->listDestinationDistrict($district_info), 'text' => $place_name];
        //Lấy ra tên các quận huyện, tách riêng query để tránh inner join 3 bảng
        $arr_district_id    =   [];
        foreach ($list_destination as $row) {
            if (!in_array($row['des_district'], $arr_district_id))  $arr_district_id[]  =   $row['des_district'];
        }
        $list_district_name =   $Model->getListData('district', 'dis_id, dis_name', 'dis_id IN(' . implode(',', $arr_district_id) . ')');
        
    break;
    
}

/** Lấy URL của trang list Hotel để show gợi ý **/
$url_hotel_city =   $Router->listHotelCity($city_info);

/** Lấy các KS khác để show gợi ý ở bên trái **/
$list_relate    =   $HotelModel->getListHotelRelate("hot_active = 1 AND hot_city = " . $city_info['cit_id'] . ($collection_info['col_group'] == GROUP_HOTEL ? " AND hot_id NOT IN($str_item_id)" : ""), $collection_info['col_brand'] > 0 ? 5 : 8);

// Lấy địa điểm trung tâm
$list_destination_center = $HotelModel->getDestinationCenter($collection_info['col_group'] == GROUP_HOTEL ? array_merge($list_hotel, $list_relate) : $list_relate);

//Lấy ra các collection khác
$list_collection    =   $DB->query("SELECT col_id, col_name, IF(col_brand > 0, 1, 0) AS hotel_brand
                                    FROM collection
                                    WHERE col_active = 1 AND col_id <> " . $collection_id . " AND col_group = " . $collection_info['col_group'] . "
                                        AND (col_city = " . $collection_info['col_city'] . "
                                            OR (col_brand > 0 AND col_brand = " . $collection_info['col_brand'] . "))
                                    ORDER BY hotel_brand DESC, col_hot DESC, col_last_update DESC
                                    LIMIT 10")
                                    ->toArray();
                                    
//Nếu chưa đủ 10 mà ko phải là các BST kiểu của 1 chuỗi KS thì lấy thêm các BST khác cùng ở khu vực cho đủ 10
$count  =   count($list_collection);
if ($count < 10) {
    
    $ids    =   [$collection_id];
    foreach ($list_collection as $row) {
        $ids[]  =   $row['col_id'];
    }
    
    $list_collection  =   array_merge($list_collection, $DB->query("SELECT col_id, col_name
                                        FROM collection
                                        INNER JOIN city ON col_city = cit_id
                                        WHERE col_active = 1 AND col_group = " . $collection_info['col_group'] . " AND cit_area = " . $city_info['cit_area'] . "
                                            AND col_id NOT IN (" . implode(',', $ids) . ")
                                        ORDER BY col_hot DESC
                                        LIMIT " . (10 - $count))
                                        ->toArray()
                                        );
}


$Layout->setTitle($page_h1)
        ->setDescription($collection_info['col_meta_desc'])
        ->setKeywords($collection_info['col_meta_desc'])
        ->setCanonical($url_canonical)
        ->setImages(['src' => $page_image, 'alt' => $page_h1])
        ->setJS(['page.article']);

?>