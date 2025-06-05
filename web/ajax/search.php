<?
include('../Core/Config/require_web.php');

$keyword    =   getValue('q', GET_STRING, GET_GET, '', 1);
$module     =   getValue('module', GET_STRING, GET_GET, 'hotel');
$response   =   [
    [
    'type'  =>  'location',
    'name'  =>  'Tìm khách sạn ở gần đây',
    'url'   =>  $cfg_path_hotel_near,
    'mode'  =>  'location'
    ]
];

//Các prefix thêm vào result tìm kiếm
$prefix_hotel   =   'Khách sạn ở ';
$prefix_tour    =   'Tour du lịch ';
$prefix_combo   =   'Combo du lịch ';

/** Nếu là module KS hoặc tour mà chưa nhập keyword thì show các City/District hot **/
if (empty($keyword)) {
    
    $cities =   $DB->query("SELECT cit_id, cit_name
                            FROM city
                            WHERE cit_show_menu = 1
                            ORDER BY cit_order, cit_name")
                            ->toArray();
    foreach ($cities as $row) {
        //Nếu city đó có Quận/Huyện Hot thì sẽ lấy ra Quận/Huyện để show chứ ko show tên Tỉnh/TP. VD như Lào Cai thì show Sapa chứ show Lào Cai ko ai quan tâm.
        $districts  =   $DB->query("SELECT dis_id, dis_name, dis_name_show
                                    FROM district
                                    WHERE dis_city = " . $row['cit_id'] . " AND dis_hot = 1
                                    ORDER BY dis_order")
                                    ->toArray();
        if (!empty($districts)) {
            foreach ($districts as $d) {
                switch ($module) {
                    case 'tour':
                        //Tour
                        $response[] =   [
                            'type'  =>  'district',
                            'name'  =>  $prefix_tour . $d['dis_name_show'],
                            'url'   =>  $Router->listTourDistrict($d),
                            'mode'  =>  'tour'
                        ];
                        //Combo
                        /*
                        $response[] =   [
                            'type'  =>  'district',
                            'name'  =>  $prefix_combo . $d['dis_name_show'],
                            'url'   =>  $Router->listComboDistrict($d)
                        ];
                        */
                        break;
                    default:
                        //Hotel
                        $response[] =   [
                            'type'  =>  'district',
                            'name'  =>  $prefix_hotel . $d['dis_name_show'],
                            'url'   =>  $Router->listHotelDistrict($d),
                            'mode'  =>  'hotel'
                        ];
                        break;
                }
            }
        } else {
            switch ($module) {
                case 'tour':
                    //Tour
                    $response[] =   [
                        'type'  =>  'city',
                        'name'  =>  $prefix_tour . $row['cit_name'],
                        'url'   =>  $Router->listTourCity($row),
                        'mode'  =>  'tour'
                    ];
                    //Combo
                    /*
                    $response[] =   [
                        'type'  =>  'city',
                        'name'  =>  $prefix_combo . $row['cit_name'],
                        'url'   =>  $Router->listComboCity($row)
                    ];
                    */
                    break;
                default:
                    //Hotel
                    $response[] =   [
                        'type'  =>  'city',
                        'name'  =>  $prefix_hotel . $row['cit_name'],
                        'url'   =>  $Router->listHotelCity($row),
                        'mode'  =>  'hotel'
                    ];
                    break;
            }
        }
        
    }
    echo json_encode($response);
    exit;
}

//Generate ra các câu SQL tính điểm
$search_data    =   $Search->generateSQLSearch($keyword, [
    'cit_search_data',
    'dis_search_data',
    'hot_data_search',
    'tou_search_data',
    'tic_search_data',
    'des_search_data',
    'art_search_data'
]);
//dump($search_data);
//Keyword đã clean
$keyword    =   $search_data['keyword'];    //Keyword đã clean dùng để search
                                
/** Nếu là search vé thì chỉ search tên, ko có phân loại theo city/district nên cho lên trên đầu tiên, search có kết quả là return luôn **/
if ($module == 'ticket') {
    $data   =   $DB->query("SELECT tic_id, tic_name, " . $search_data['tic_search_data']['diem'] . "
                            FROM ticket
                            WHERE tic_active = 1 " . $search_data['tic_search_data']['where'] . "
                            ORDER BY diem DESC, tic_top DESC, tic_hot DESC
                            LIMIT 10")
                            ->toArray();
    foreach ($data as $row) {
        $response[] =   [
            'url'   =>  $Router->detailTicket($row),
            'name'  =>  $row['tic_name'],
            'type'  =>  'ticket',
            'mode'  =>  'ticket'
            //'diem'  =>  $row['diem']
        ];
    }
    echo json_encode($response);
    exit;
}

/** Tìm theo Tỉnh/TP **/
$data   =   $DB->query("SELECT cit_id, cit_name, " . $search_data['cit_search_data']['diem'] . "
                        FROM city
                        WHERE cit_active = 1 " . $search_data['cit_search_data']['where'] . "
                        ORDER BY diem DESC
                        LIMIT 5")
                        ->toArray();
$i  =   0;
foreach ($data as $row) {
    $i++;
    
    //Tiêu đề của nhóm Điểm đến
    if ($i == 1) {
        $response[] =   [
                        'id'    =>  0,
                        'name'  =>  'Tỉnh/TP'
                        ];
    }
    
    //Tùy search gì mà cho link của module đó
    switch ($module) {
        case 'tour':
            //Tour
            $response[] =   [
                'type'  =>  'city',
                'name'  =>  $prefix_tour . $row['cit_name'],
                'url'   =>  $Router->listTourCity($row),
                'mode'  =>  'tour'
                //'diem'  =>  $row['diem']
            ];
            //Combo
            $response[] =   [
                'type'  =>  'city',
                'name'  =>  $prefix_combo . $row['cit_name'],
                'url'   =>  $Router->listComboCity($row),
                'mode'  =>  'combo'
                //'diem'  =>  $row['diem']
            ];
            break;
        default:
            //Hotel
            $response[] =   [
                'type'  =>  'city',
                'name'  =>  $prefix_hotel . $row['cit_name'],
                'url'   =>  $Router->listHotelCity($row),
                'mode'  =>  'hotel'
                //'diem'  =>  $row['diem']
            ];
            break;
    }
    
}

/** Tìm các Quận huyện **/
$data   =   $DB->query("SELECT dis_id, dis_hot, dis_name, dis_name_show, " . $search_data['dis_search_data']['diem'] . "
                        FROM district
                        WHERE dis_active = 1 " . $search_data['dis_search_data']['where'] . "
                        ORDER BY diem DESC
                        LIMIT 5")
                        ->toArray();
$i  =   0;
foreach ($data as $row) {
    $i++;
    
    //Tiêu đều của nhóm tour khi hiển thị ở kết quả search
    if ($i == 1) {
        $response[] =   [
                        'id'    =>  0,
                        'name'  =>  'Điểm đến'
                        ];
    }
    $dis_name   =   $PlaceModel->getDistrictName($row);
    //Tùy search gì mà cho link của module đó
    switch ($module) {
        case 'tour':
            //Tour
            $response[] =   [
                'type'  =>  'district',
                'name'  =>  $prefix_tour . $dis_name,
                'url'   =>  $Router->listTourDistrict($row),
                'mode'  =>  'tour'
                //'diem'  =>  $row['diem']
            ];
            //Combo
            $response[] =   [
                'type'  =>  'district',
                'name'  =>  $prefix_combo . $dis_name,
                'url'   =>  $Router->listComboDistrict($row),
                'mode'  =>  'combo'
                //'diem'  =>  $row['diem']
            ];
            break;
        default:
            //Hotel
            $response[] =   [
                'type'  =>  'district',
                'name'  =>  $prefix_hotel . $dis_name,
                'url'   =>  $Router->listHotelDistrict($row),
                'mode'  =>  'hotel'
                //'diem'  =>  $row['diem']
            ];
            break;
    }
    
}

//Lấy các địa danh, dùng cho search cả khi search tour và search ra tên các địa danh nên lấy ở đây và dùng ở cuối file để ko phải query lại
$data_destination   =   $DB->query("SELECT des_id, des_name, " . $search_data['des_search_data']['diem'] . "
                            FROM destination
                            WHERE des_active = 1 " . $search_data['des_search_data']['where'] . "
                            ORDER BY diem DESC, des_count_view DESC
                            LIMIT 10")
                            ->toArray();

//Tìm đối tượng chính theo module
if($module == 'tour') {
    
    //Nếu search tour thì mới có list tour ở địa danh
    foreach ($data_destination as $row) {
        $i++;
        //Nếu ở bên trên mà ko có Tỉnh/TP quận huyện nào phù hợp thì xuống đây cho tiêu đề vào
        if ($i == 1) {
            $response[] =   [
                            'id'    =>  0,
                            'name'  =>  'Điểm đến'
                            ];
        }
        $response[] =   [
            'url'   =>  $Router->listTourDestination($row),
            'name'  =>  $prefix_tour . $row['des_name'],
            'type'  =>  'destination',
            'mode'  =>  'tour'
            //'diem'  =>  $row['diem']
        ];
    }
    
    //Tìm các tour
    $data   =   $DB->query("SELECT tou_id, tou_name, tou_group, " . $search_data['tou_search_data']['diem'] . "
                            FROM tour
                            WHERE tou_active = 1 " . $search_data['tou_search_data']['where'] . "
                            ORDER BY diem DESC, tou_top DESC, tou_hot DESC
                            LIMIT 10")
                            ->toArray();
    $i  =   0;
    foreach ($data as $row) {
        $i++;
        
        //Tiêu đều của nhóm tour khi hiển thị ở kết quả search
        if ($i == 1) {
            $response[] =   [
                            'id'    =>  0,
                            'name'  =>  'Tour'
                            ];
        }
        
        $response[] =   [
            'url'   =>  $Router->detailTour($row),
            'name'  =>  $row['tou_name'],
            'type'  =>  'tour',
            'mode'  =>  'tour'
            //'diem'  =>  $row['diem']
        ];
    }
} else {
    //Tìm các ks
    $data   =   $DB->query("SELECT hot_id, hot_name, " . $search_data['hot_data_search']['diem'] . "
                            FROM hotel
                            WHERE hot_active = 1 " . $search_data['hot_data_search']['where'] . "
                            ORDER BY diem DESC, hot_top DESC, hot_hot DESC
                            LIMIT 10")
                            ->toArray();
    $i  =   0;
    foreach ($data as $row) {
        $i++;
        
        //Tiêu đều của nhóm ks khi hiển thị ở kết quả search
        if ($i == 1) {
            $response[] =   [
                            'id'    =>  0,
                            'name'  =>  'Khách sạn'
                            ];
        }
        
        $response[] =   [
            'url'   =>  $Router->detailHotel($row),
            'name'  =>  $row['hot_name'],
            'type'  =>  'hotel',
            'mode'  =>  'hotel'
            //'diem'  =>  $row['diem']
        ];
    }
}

//Cuối cùng là tìm địa danh
$i  =   0;
foreach ($data_destination as $row) {
    $i++;
    
    //Tiêu đều của nhóm tour khi hiển thị ở kết quả search
    if ($i == 1) {
        $response[] =   [
                        'id'    =>  0,
                        'name'  =>  'Địa danh du lịch'
                        ];
    }
    
    $response[] =   [
        'url'   =>  $Router->detailDestination($row),
        'name'  =>  $row['des_name'],
        'type'  =>  'destination',
        'mode'  =>  'destination'
        //'diem'  =>  $row['diem']
    ];
}
//dump($response);
echo json_encode($response);
exit;
?>