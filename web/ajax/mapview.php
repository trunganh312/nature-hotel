<?php 
include('../Core/Config/require_web.php');

$mode_data  =   getValue('mode', GET_STRING, GET_GET, '', 1);
$raw_query  =   getValue('query', GET_ARRAY, GET_GET, []);
$record_id  =   getValue('id', GET_INT, GET_GET, 0);
$mapBounds  =   getValue('mapBounds', GET_ARRAY, GET_GET, 0);
$sql_where  = $sql_having = $sql_field = $poup_title = '';
$sql_order_by = [];
$res = [
    "data_map"=> [],
    "map_icon"=> "/theme/images/ico_mapker_hotel-1.png",
    "zoom"=> 0
];

switch ($mode_data) {
    case 'city':
        $row = $DB->query("SELECT cit_id, cit_name, cit_lat_center, cit_lng_center, cit_map_zoom_level, cit_hot FROM city WHERE cit_id = {$record_id} LIMIT 1")->getOne();
        if(empty($row)) break;
        $poup_title = $row['cit_name'];
        $sql_where = 'hot_city = '. $row['cit_id'];

        if (empty($row['cit_lat_center']) || empty($row['cit_lng_center'])) {
            $location_info = get_latlng_mapbox($row["cit_name"]);
        } else {
            $location_info = [
                "lat"=> $row["cit_lat_center"],
                "lng"=> $row["cit_lng_center"],
            ];
        }
        $res["zoom"]    =   $row["cit_map_zoom_level"] > 0 ? $row["cit_map_zoom_level"] : ($row['cit_hot'] > 0 ? 13 : 13);
        break;
    case 'district':
        $row = $DB->query("SELECT dis_id, dis_name, dis_lat_center, dis_lng_center, dis_map_zoom_level, dis_hot FROM district WHERE dis_id = {$record_id} LIMIT 1")->getOne();
        if(empty($row)) break;
        $poup_title = $row['dis_name'];
        $sql_where = 'hot_district = '. $row['dis_id'];

        if (empty($row['dis_lat_center']) || empty($row['dis_lng_center'])) {
            $location_info = get_latlng_mapbox($row["dis_name"]);
        } else {
            $location_info = [
                "lat"=> $row["dis_lat_center"],
                "lng"=> $row["dis_lng_center"],
            ];
        }
        $res["zoom"]    =   $row["dis_map_zoom_level"] > 0 ? $row["dis_map_zoom_level"] : ($row['dis_hot'] > 0 ? 14 : 12);
        break;

    case 'detail':
        $row = $DB->query("SELECT hot_lat, hot_lon, hot_name, hot_type FROM hotel WHERE hot_id = {$record_id} LIMIT 1")->getOne();
        if(empty($row)) break;
        $poup_title = $row['hot_name'];
        $sql_where = 1;
        $sql_field = ", ST_Distance_Sphere( point ('{$row["hot_lon"]}', '{$row["hot_lat"]}'), 
                        point(hot_lon, hot_lat)) / 1000 
                        as `distance_in_km` ";
        $sql_order_by[] = 'distance_in_km ASC';
        $location_info = [
            "lat"=> $row["hot_lat"],
            "lng"=> $row["hot_lon"],
        ];
        break;

    case 'destination':
        $row = $DB->query("SELECT des_id, des_image, des_address_full, des_lat, des_lon, des_name FROM destination WHERE des_id = {$record_id} LIMIT 1")->getOne();
        if(empty($row)) break;
        $poup_title = $row['des_name'];
        $sql_where = 1;
        $sql_field = ", ST_Distance_Sphere( point ('{$row["des_lon"]}', '{$row["des_lat"]}'), 
                        point(hot_lon, hot_lat)) / 1000 
                        as `distance_in_km` ";
        $sql_order_by[] = 'distance_in_km ASC';
        $location_info = [
            "lat"=> $row["des_lat"],
            "lng"=> $row["des_lon"],
        ];
        $row['image']      =   $Router->srcDestination($row['des_image']);
        $row["url"]        = $Router->detailDestination($row);
        $res['data_map'][] = [
            "id"            => $row["des_id"],
            "name"          => $row["des_name"],
            "img"           => $row['image'],
            "lat"           => $row["des_lat"],
            "lng"           => $row["des_lon"],
            "address_full"  => $row["des_address_full"],
            "icon"          => "/theme/images/icon/location-pin.png",
            "price"         => '',
            "content_html"  => "<div class=\"item-service-map\">
                <div class=\"thumb\">
                    <a href=\"{$row["url"]}\" target=\"_blank\">
                        <img width=\"280\" height=\"180\" src=\"{$row["image"]}\" class=\"img-responsive wp-post-image\" alt=\"{$row["des_name"]}\" loading=\"lazy\" srcset=\"{$row["image"]} 280w\" sizes=\"(max-width: 280px) 100vw, 280px\" />
                    </a>
                </div>
                <div class=\"content\">
                    <h4 class=\"service-title\"><a href=\"{$row["url"]}\" target=\"_blank\">{$row["des_name"]}</a></h4>
                </div>
            </div>"
        ];
        break;
        
    case 'search':
        $location_search = getValue('location', GET_ARRAY, GET_GET, []);
        if(count($location_search) != 2) break;
        $poup_title = 'Tìm kiếm';
        $sql_where = 1;
        $sql_field = ", ST_Distance_Sphere( point ('{$location_search[0]}', '{$location_search[1]}'), 
                        point(hot_lon, hot_lat)) / 1000 
                        as `distance_in_km` ";
        $sql_order_by[] = 'distance_in_km ASC';
        $location_info = [
            "lat"=> $location_search[1],
            "lng"=> $location_search[0],
        ];

    case 'around':
        $record_id          =   getValue('id', GET_STRING); // Với type around thì id gửi lên là một str
        $location_search    =   getValue('location', GET_ARRAY, GET_GET, []);
        $record_id          =   explode("_", $record_id);
        if(count($location_search) != 2 || count($record_id) != 2) break;
        $poup_title = 'Khách sạn tốt nhất ở gần đây';

        $city_id        =   $record_id[0];
        $district_id    =   $record_id[1];
        $distance_limit =   10;
        $sql_where      =   1;

        if ($district_id > 0) {
            $row = $Model->getRecordInfo('district', 'dis_id', $district_id, 'dis_hot, dis_map_zoom_level');
            if(!empty($row)) {
                $sql_where = 'hot_district = '. $district_id;
                //Nếu là quận/huyện HOT thì lấy distance gần thôi, ko HOT thì lấy rộng
                if (array_get($row, 'dis_hot', 0) == 1) {
                    $distance_limit =   4;
                    $res["zoom"]    =   $row["dis_map_zoom_level"] > 0 ? $row["dis_map_zoom_level"] : 15; 
                } else {
                    $distance_limit =   8;
                    $res["zoom"]    =   $row["dis_map_zoom_level"] > 0 ? $row["dis_map_zoom_level"] : 12;
                }
            }
        } else if ($city_id > 0) {
            $row = $Model->getRecordInfo('city', 'cit_id', $city_id, 'cit_hot, cit_map_zoom_level');
            if(!empty($row)) {
                $sql_where = 'hot_city = '. $city_id;
                if (array_get($row, 'cit_hot', 0) == 1) {
                    $distance_limit =   5;
                    $res["zoom"]    =   $row["cit_map_zoom_level"] > 0 ? $row["cit_map_zoom_level"] : 14;
                } else {
                    $distance_limit =   10;
                    $res["zoom"]    =   $row["cit_map_zoom_level"] > 0 ? $row["cit_map_zoom_level"] : 10;
                }
            }
        }

        $sql_field = ", ST_Distance_Sphere( point ('{$location_search[0]}', '{$location_search[1]}'), 
                        point(hot_lon, hot_lat)) / 1000 
                        as `distance_in_km` ";
        $sql_having = 'HAVING distance_in_km <= '. $distance_limit;
        $sql_order_by[] = 'distance_in_km ASC';
        $location_info = [
            "lat"=> $location_search[1],
            "lng"=> $location_search[0],
        ];
}

if(empty($sql_where)) exit('Dữ liệu này không tồn tại!');

$res["poup_title"]      = $poup_title;
$res["map_lat_center"]  = $location_info["lat"];
$res["map_lng_center"]  = $location_info["lng"];

if (count($mapBounds) != 4) {
    echo json_encode($res);
    exit;
}

$raw_query['stars'] = empty($raw_query['stars']) ? [] : $raw_query['stars'];
if (!empty($raw_query['stars'])) {
    $raw_query['stars'] = getIntegerArrayID(array_values($raw_query['stars']));
    $raw_query['stars'] = implode(',', $raw_query['stars']);
    $sql_where .= " AND hot_star IN({$raw_query['stars']})";
}
$raw_query['types'] = empty($raw_query['types']) ? [] : $raw_query['types'];
if (!empty($raw_query['types'])) {
    $raw_query['types'] = getIntegerArrayID(array_values($raw_query['types']));
    $raw_query['types'] = implode(',', $raw_query['types']);
    $sql_where .= " AND hot_type IN({$raw_query['types']})";
}

$raw_query['sort'] = empty($raw_query['sort']) ? '' : $raw_query['sort'];
switch ($raw_query['sort']) {
    case 'star_asc':
        $sql_order_by[] = 'hot_star ASC';
    break;

    default:
        $sql_order_by[] = 'hot_star DESC';
}
$sql_order_by = implode(',', $sql_order_by);

// replaceMQ
$data_hotel   =   $DB->query("SELECT hot_id, hot_name, hot_picture, hot_star, hot_address_full, hot_hot, hot_lat, hot_lon {$sql_field}
                                FROM hotel
                                WHERE hot_active = 1 AND $sql_where AND
                                    (CASE WHEN {$mapBounds[0]} < {$mapBounds[2]}
                                        THEN hot_lat BETWEEN {$mapBounds[0]} AND {$mapBounds[2]}
                                        ELSE hot_lat BETWEEN {$mapBounds[2]} AND {$mapBounds[0]}
                                    END) 
                                    AND
                                    (CASE WHEN {$mapBounds[1]} < {$mapBounds[3]}
                                        THEN hot_lon BETWEEN {$mapBounds[1]} AND {$mapBounds[3]}
                                        ELSE hot_lon BETWEEN {$mapBounds[3]} AND {$mapBounds[1]}
                                    END)
                                $sql_having ORDER BY $sql_order_by LIMIT 500")
                            ->toArray();



require_once($path_core . 'Model/HotelModel.php');
$HotelModel = new HotelModel;

foreach ($data_hotel as $index => $row) {

    $row["hot_picture"] = $Router->srcHotel($row['hot_id'], $row['hot_picture'], SIZE_MEDIUM);
    $row["url"]         = $Router->detailHotel($row, param_box_web(156));
    
    /*
    $row["hot_address_tmp"] = explode(',', $row["hot_address_full"]);
    $row["hot_address_tmp"] = $row["hot_address_tmp"][count($row["hot_address_tmp"])-2] .', '. $row["hot_address_tmp"][count($row["hot_address_tmp"])-1];
    */
    
    $row['hot_price'] = $HotelModel->getRoomPrice($row["hot_id"]);

    $tmp = [
        "id"=> $row["hot_id"],
        "name"=> $row["hot_name"],
        "price"=> $row['hot_price'] > 0 ? show_money($row['hot_price'], ' ₫') : '',
        "img"=> $row["hot_picture"],
        "star"=> $row["hot_star"],
        "lat"=> $row["hot_lat"],
        "lng"=> $row["hot_lon"],
        "address_full"=> $row["hot_address_full"],
        "icon"=> ($mode_data == 'detail' && $record_id == $row['hot_id'] || $index == 0) ? "/theme/images/icon/marker-hotel.png" : null
    ];

    $row["hot_star"]         = gen_star_3($row['hot_star']);

    $tmp["content_html"] = "<div class=\"item-service-map\">
        <div class=\"thumb\">
            <a href=\"{$row["url"]}\" target=\"_blank\">
                <img width=\"280\" height=\"180\" src=\"{$row["hot_picture"]}\" class=\"img-responsive wp-post-image\" alt=\"{$row["hot_name"]}\" loading=\"lazy\" srcset=\"{$row["hot_picture"]} 280w\" sizes=\"(max-width: 280px) 100vw, 280px\" />
            </a>
            ". ($row['hot_hot'] == 1 ? '<div class="service-tag bestseller">
                                        <div class="feature_class st_featured featured">HOT</div>
                                    </div>' : '') ."
            {$row["hot_star"]}
        </div>
        <div class=\"content\">
            <h4 class=\"service-title\"><a href=\"{$row["url"]}\" target=\"_blank\">{$row["hot_name"]}</a></h4>
            ". ($row['hot_price'] > 0 ? '
            <div class="service-price price_hoz">
                <span class="price">' . show_money($row['hot_price'], ' ₫') . '</span>
            </div>' : '<div class="service-price empty_price">Liên hệ: 0931 666 900</div>') . "
        </div>
    </div>";
    $res["data_map"][] = $tmp;
}

echo json_encode($res);
exit;