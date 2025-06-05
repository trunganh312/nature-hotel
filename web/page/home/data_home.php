<?
include('Core/Config/require_web.php');

$Layout->setTitle($cfg_website['con_meta_title'])
        ->setDescription($cfg_website['con_meta_description'])
        ->setKeywords($cfg_website['con_meta_keyword'])
        ->setImages(['src' => $cfg_default_image, 'alt' => $cfg_website['con_meta_title']])
        ->setCanonical(DOMAIN_WEB)
        ->setJS(['page.home']);

/** Lấy các banner **/
$list_banner    =   $DB->query("SELECT ban_id, ban_title, ban_description, ban_image, ban_label, ban_text_link, ban_link
                                FROM banner
                                WHERE ban_active = 1 AND ban_position = " . BANNER_HOME_TOP . "
                                ORDER BY ban_order
                                LIMIT 3")
                                ->toArray();

/** Lấy các địa điểm du lịch HOT **/
$list_place_hot =   [];
//Tùy giai đoạn mà sắp xếp các khu cao điểm lên trên
$city_hot   =   [
    18, //Kiên Giang (Phú Quốc)
    56, //Khánh Hòa (Nha Trang)
    48, //Đà Nẵng
    49, //Quảng Nam (Hội An)
    52, //Bình Định (Quy Nhơn)
    43, //Vũng Tàu (Vũng Tàu, Côn Đảo)
    57, //Lâm Đồng (Đà Lạt)
    10, //Lào Cai (Sapa)
    22, //Quảng Ninh (Hạ Long)
    26, //Vĩnh Phúc (Tam Đảo)
    31, //Hải Phòng (Cát Bà, Đồ Sơn)
    54, //Phú Yên
    60, //Bình Thuận (Phan Thiết Mũi Né)
    38  //Thanh Hóa (Sầm Sơn)
];

$list_city  =   $DB->query("SELECT cit_id, cit_name, cit_image
                            FROM cities WHERE cit_hot = 1
                            ORDER BY FIELD(cit_id, " . implode(',', $city_hot) . ") LIMIT 10")->toArray();

$i  =   0;
foreach ($list_city as $c) {
    //Check xem City này có District Hot ko thì sẽ ưu tiên lấy theo District, chẳng hạn ở Khánh Hòa thì sẽ lấy Nha Trang
    $district   =   $DB->query("SELECT dis_id, dis_name, dis_name_show, dis_image
                                FROM district
                                WHERE dis_city = " . $c['cit_id'] . " AND dis_hot = 1
                                ORDER BY dis_order
                                LIMIT 2")
                                ->toArray();
    if (!empty($district)) {
        foreach ($district as $d) {
            $name   =   !empty($d['dis_name_show']) ? $d['dis_name_show'] : $d['dis_name'];
            
            $list_place_hot[]   =   [
                'image' =>  $Router->srcPlace($d['dis_image'], SIZE_SMALL),
                'title' =>  $name,
                'url'   =>  $Router->listHotelDistrict($d, param_box_web(5)),
                'id'    =>  'd' . $d['dis_id']
            ];
            
            $i++;
        }
    } else {
        $list_place_hot[]   =   [
            'image' =>  $Router->srcPlace($c['cit_image'], SIZE_SMALL),
            'title' =>  $c['cit_name'],
            'url'   =>  $Router->listHotelCity($c, param_box_web(5)),
            'id'    =>  'c' . $c['cit_id']
        ];
        
        $i++;
    }
    
    if ($i == 10) break; //Chỉ lấy 10 cái để đủ show thôi
}
//dd($list_place_hot);

/** List hot relate **/
$list_collection    =   [];

$list_tour_hot  =   [];

$list_combo_hot  =   [];

$list_hotel_hot  =   [];

// Lấy địa điểm trung tâm
$list_destination_center = [];

?>