<?
include('../../../Core/Config/require_web.php');
$BookingModel   =   new BookingModel;
$VoucherModel   =   new VoucherModel;
$UserModel      =   new UserModel;

$error  =   [];
$action =   getValue('action', GET_STRING, GET_POST, '');

//Set module để active màu menu, ko muốn dùng ở .htaccess
$page_module    =   'hotel';

//Breadcrum bar
$arr_breadcrum  =   [
                    ['text' => 'Đặt phòng']
                    ];

//Lấy data các phòng được chọn đặt
$rooms  =   getValue('room', GET_ARRAY, GET_GET, []);
$rooms_original  =   $rooms;   //Lưu lại dữ liệu đầu tiên để check log
$notfound   =   false;

//Có một số URL cũ vẫn được lưu ở máy khách nên cần convert sang param mới cho chuẩn (URL cũ: rooms[]=123&qtys[123]=1)
if (empty($rooms)) {
    $arr_r  =   getValue('rooms', GET_ARRAY, GET_GET, []);
    $arr_q  =   getValue('qtys', GET_ARRAY, GET_GET, []);

    foreach ($arr_r as $r) {
        if (isset($arr_q[$r])) {
            $rooms[$r]  =   ($arr_q[$r] > 0 ? $arr_q[$r] : 1);
        }
    }
    save_log('checkout_404.cfn', 'Old URL. Line: ' . __LINE__ . '. Action: ' . $action . '. Param converted: ' . json_encode($rooms) . '. User-Agent: ' . (!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . '. Session: ' . (!empty($_SESSION) ? json_encode($_SESSION) : '') . '. Cookie: ' . (!empty($_COOKIE) ? json_encode($_COOKIE) : ''));
}
// Lưu vào session
$_SESSION['url_cancel'] = $_SERVER['REQUEST_URI'];
if (empty($rooms)) {
    
    //Xử lý trường hợp URL bị encode do kiểu người này copy gửi cho người kia, sau đó mở trong 1 ứng dụng nào đó bị convert URL
    //Kiểu dạng: vietgoing.com/checkout/hotel/?checkin=27%252F11%252F2023&checkout=28%252F11%252F2023&rooms%255B0%255D=6898&qtys%255B6898%255D=2&adult=1&child=0&baby=0&utm_web=134
    $uri    =   $_SERVER['REQUEST_URI'];
    $uri    =   str_replace('&amp;', '&', $uri);
    

    //Decode 2 lần
    $uri    =   urldecode($uri);
    $uri    =   urldecode($uri);
    //Bẻ ký tự ? để convert URL thành các param rồi gán thành $_GET
    $exp    =   explode('?', $uri);
    if (!empty($exp[1])) {
        $uri    =   parse_str($exp[1], $params);
        if (!empty($params)) {
            $_GET   =   array_merge($_GET, $params);
        }
    }
    
    //Sau khi convert xong URL thì get lại
    $rooms  =   getValue('room', GET_ARRAY, GET_GET, []);
    
    //Nếu vẫn ko được thì save log là 404
    if (empty($rooms)) {
        
        $content    =   'Empty param room. Line: ' . __LINE__ . '. Action: ' . $action;
        if (!empty($_POST)) {
            $content    .=  '. $_POST: ' . json_encode($_POST);
        }
        if (!empty($_GET)) {
            $content    .=  '. $_GET: ' . json_encode($_GET);
        }
        $content    .=   '. User-Agent: ' . (!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . '. Session: ' . (!empty($_SESSION) ? json_encode($_SESSION) : '') . '. Cookie: ' . (!empty($_COOKIE) ? json_encode($_COOKIE) : '');
        
        $notfound   =   true;
        save_log('checkout_404.cfn', $content);
        return;
        
    } else {
        //Lưu log để check việc convert URL có tạo thành booking hay ko
        save_log('checkout_404.cfn', 'Convert URL checkout success!');
    }
}

//Clear Data Room để tránh Fake URL
$room_param =   [];
$min_adult_number = 0;
$max_adult_number = 0;
$max_baby_number = 0;
$max_children_number = 0;
foreach ($rooms as $id => $qty) {
    $id     =   (int)$id;
    $qty    =   (int)$qty;
    if ($id > 0) {
        $room_param[$id]    =   $qty >= 0 ? $qty : 1;
    }
}

if (empty($room_param)) {
    $notfound   =   true;
    save_log('checkout_404.cfn', 'Empty data booking hotel. Line: ' . __LINE__ . '. Param URL: ' . json_encode($rooms));
    return;
}

$room_book  =   $DB->query("SELECT *
                            FROM room
                            WHERE roo_id IN(". implode(',', array_keys($room_param)) .") AND roo_active = 1")
                            ->toArray();
if (empty($room_book)) {
    $notfound   =   true;
    save_log('checkout_404.cfn', 'Empty room database. Line: ' . __LINE__ . '. Rooms: ' . json_encode($room_param) . '. Action: ' . $action);
    return;
}

//Lấy Hotel ID
$hotel_id   =   $room_book[0]["roo_hotel_id"];

$hotel_info =   $DB->query("SELECT * FROM hotel WHERE hot_id = $hotel_id AND hot_active = 1")->getOne();
if (empty($hotel_info)) {
    $notfound   =   true;
    save_log('checkout_404.cfn', 'Hotel not found. Line: ' . __LINE__ . '. Rooms: ' . json_encode($room_book) . '. Action: ' . $action);
    return;
}

$hotel_info['full_name']    =   $hotel_info["hot_name"];
$hotel_info['url']          =   $Router->detailHotel($hotel_info);
$hotel_info['image']        =   $Router->srcHotel($hotel_info["hot_id"], $hotel_info['hot_picture'], SIZE_SMALL);
$promotion_apply            =   $PromotionModel->info();

//Gán loại hình vào tên KS
if ($hotel_info['hot_type'] != 6) {   //Ko gán Tổ hợp vào, chẳng hạn như tổ hợp của Vin thì ko cần
    if (strpos($hotel_info['hot_name'], $cfg_hotel_type[$hotel_info['hot_type']]) === false) {
        $hotel_info['full_name']    =   $cfg_hotel_type[$hotel_info['hot_type']] . ' ' . $hotel_info['full_name'];
    }
}
// Biến check KS OTA
$isOTA = $hotel_info['hot_ota'] > 0 && $hotel_info['hot_id_mapping'] > 0;

//Lấy các thông tin đặt
$date_checkin   =   getValue('checkin', GET_STRING, GET_POST, $cfg_date_checkin);
$date_checkout  =   getValue('checkout', GET_STRING, GET_POST, $cfg_date_checkout);
$adult_number   =   getValue('adult', GET_INT, GET_POST, getValue('adult', GET_INT, GET_GET, getValue('adult', GET_INT, GET_COOKIE, 2)));
$child_number   =   getValue('child', GET_INT, GET_POST, getValue('child', GET_INT, GET_GET, getValue('child', GET_INT, GET_COOKIE, 0)));
$baby_number    =   getValue('baby', GET_INT, GET_POST, getValue('baby', GET_INT, GET_GET, getValue('baby', GET_INT, GET_COOKIE, 0)));
//Mặc định luôn có 1 người lớn
if ($adult_number < 1) {
    $adult_number   =   1;
}

//Validate tính hợp lệ của time
if (str_totime($date_checkin) < strtotime(date('m/d/Y')) || str_totime($date_checkout) <= str_totime($date_checkin)) {
    $date_checkin   =   date('d/m/Y');
    $date_checkout  =   date('d/m/Y', CURRENT_TIME + 86400);
}

//Convert lại ngày tháng sang kiểu int để có thể sử dụng được global ở hàm getRoomPrice
$cfg_time_checkin   =   str_totime($date_checkin);
$cfg_time_checkout  =   str_totime($date_checkout);
$cfg_total_night    =   round(($cfg_time_checkout - $cfg_time_checkin) / 86400);



//Lưu các thông tin Họ tên, ĐT... vào Session để nếu submit lỗi thì giữ lại để khách đỡ phải nhập lại lần nữa
$_SESSION['booking_info']   =   [];

//Lấy các thông tin liên hệ
$name   =   getValue('name', GET_STRING, GET_POST, !empty($_SESSION['booking_info']['name']) ? $_SESSION['booking_info']['name'] : ($User->logged ? $User->info['use_name'] : ''));
$email  =   $User->logged ? $User->email : getValue('email', GET_STRING, GET_POST, !empty($_SESSION['booking_info']['email']) ? $_SESSION['booking_info']['email'] : '');
$phone  =   getValue('phone', GET_STRING, GET_POST, !empty($_SESSION['booking_info']['phone']) ? $_SESSION['booking_info']['phone'] : ($User->logged ? $User->info['use_phone'] : ''));
$city   =   getValue('city', GET_INT, GET_POST, !empty($_SESSION['booking_info']['city']) ? $_SESSION['booking_info']['city'] : ($User->logged ? $User->info['use_city'] : ''));
$note   =   getValue('note', GET_STRING, GET_POST, !empty($_SESSION['booking_info']['note']) ? $_SESSION['booking_info']['note'] : '');
$voucher_code   =   getValue('voucher', GET_STRING, GET_POST, !empty($_SESSION['booking_info']['voucher']) ? $_SESSION['booking_info']['voucher'] : '');
$invoice_info   =   getValue('invoice_info', GET_STRING, GET_POST, !empty($_SESSION['booking_info']['invoice_info']) ? $_SESSION['booking_info']['invoice_info'] : '');
$invoice        =   getValue('invoice', GET_INT, GET_POST, !empty($_SESSION['booking_info']['invoice']) ? $_SESSION['booking_info']['invoice'] : 0);
$email  =   fix_email($email);

// Lấy giá cho tất cả các phòng
$total_money    = 0;
$data_room  =   [
    'arr_id'    =>  [],
    'prices'    =>  [],
    'qtys'      =>  []
];

//Lấy giá của phòng đắt nhất trong các phòng được chọn đặt để tính áng chừng ra giá công bố, show thông tin số tiền tiết kiệm
$price_max  =   0;

//Lấy thông tin bữa sáng
$include_breakfast  =   false;

//Thông tin miễn phí sân bay
$free_airport   =   $HotelModel->getFreeAirport($hotel_info);

//Code cũ đặt tên biến là $rooms nên khai báo lại ở đây để đỡ phải sửa nhiều
$rooms  =   $room_book;
$error  =   [];
foreach ($rooms as $k => $row) {
    
    //Loại các phòng ko thuộc KS đi (Tránh trường hợp Fake dữ liệu)
    if($row["roo_hotel_id"] !== $hotel_info["hot_id"]) {
        unset($rooms[$k]);
        continue;
    }

    // Kiểm tra số phòng trống trước khi tính toán
    $requested_qty = $room_param[$row["roo_id"]];

    // Nếu KS OTA thì tính lại phòng trống mỗi khi quét lại
    if($isOTA) {
        $room_available = $HotelModel->getTotalRoomAvailable($hotel_info["hot_id"], null, $row["roo_id"]);
        if($room_available < $requested_qty) {
            if($room_available == 0) {
                $error[] = 'Phòng ' . $row['roo_name'] . ' đã hết. Vui lòng chọn phòng khác.';
            }else {
                $error[] = 'Phòng ' . $row['roo_name'] . ' chỉ còn ' . $room_available . ' phòng. Vui lòng chọn phòng khác.';
            }
        }
    }
    $rooms[$k]["qty"] = $requested_qty;

    $rooms[$k]["price"] =   $HotelModel->getRoomPrice($hotel_info["hot_id"], false, $row["roo_id"]);
    // Nếu là KS OTA thì check ăn sáng theo trường này
    // Còn KS TA vẫn logic cũ
    $is_breakfast_price = $isOTA ? ($hotel_info['hot_include_breakfast'] == 1 ? true : false) : $HotelModel->is_breakfast_price;
    if ($is_breakfast_price)   $include_breakfast   =   true;   //Chỉ cần 1 phòng có ăn sáng thì tính là tất cả đều có ăn sáng 

    $rooms[$k]["price_total"]   =   $rooms[$k]["price"] * $rooms[$k]["qty"];
    $total_money    +=  $rooms[$k]["price_total"];
    $rooms[$k]['image'] =   $Router->srcRoom($row["roo_id"], $row['roo_picture'], SIZE_SMALL);

    $rooms[$k]["price_total_vnd"]   =   $rooms[$k]["price_total"] > 0 ? ': ' . show_money($rooms[$k]["price_total"]) : '';
    
    //Gán vào mảng room để truyền vào hàm tạo booking
    $data_room['arr_id'][]  =   $row["roo_id"];
    $data_room['prices'][]  =   $rooms[$k]["price"];
    $data_room['qtys'][]    =   $rooms[$k]["qty"];
    $max_baby_number+= ($rooms[$k]["qty"] * $row['roo_baby']);
    $max_children_number+= ($rooms[$k]["qty"] * $row['roo_children']);
    $max_adult_number+= ($rooms[$k]["qty"] * $row['roo_person']);
    $min_adult_number+= $rooms[$k]["qty"];
    //Gán lại vào giá cao nhất
    if ($rooms[$k]["price"] > $price_max)   $price_max  =   $rooms[$k]["price"];
}

// Nếu là KS OTA thì số người lớn min phải bằng số phòng
if($isOTA) {
    // Tổng số người lớn phải >= min và phải <= max
    if($adult_number < $min_adult_number) {
        $adult_number = $min_adult_number;
    }else if($adult_number > $max_adult_number) {
        $adult_number = $max_adult_number;
    }
    // Tổng số trẻ em phải <= max
    if($child_number > $max_children_number) {
        $child_number = $max_children_number;
    }
    // Tổng số em bé phải <= max
    if($baby_number > $max_baby_number) {
        $baby_number = $max_baby_number;
    }
}else {
    $min_adult_number = 1;
    $max_baby_number = 100;
    $max_children_number = 100;
    $max_adult_number = 100;
}

//Lưu cookie để fill vào các form search
// Chuyển xuống dưới
setcookie('checkin', $date_checkin, $time_expired, '/');
setcookie('checkout', $date_checkout, $time_expired, '/');
setcookie('adult', $adult_number, $time_expired, '/');
setcookie('child', $child_number, $time_expired, '/');
setcookie('baby', $baby_number, $time_expired, '/');

if(empty($rooms)) {
    $notfound   =   true;
    save_log('checkout_404.cfn', 'Empty room. Line: ' . __LINE__ . '. Room book: ' . json_encode($room_book) . '. Action: ' . $action);
    return;
}

// Kiểm tra xem có áp dụng khuyến mại nào k
$promotion_discount =   $PromotionModel->calculateMoney($total_money)['discounting'];

//Tính tiền nong
$money_discount         =   0;
$money_pay              =   $total_money - $promotion_discount;
$is_voucher_referral    =   false;

// Áp dung mã giảm giá
if (!empty($voucher_code)) {
    
    // Kiểm tra xem có phải mã giới thiệu k trước
    $voucher_discount   =   $UserModel->getVoucherReferral($email, $voucher_code, $total_money);
    
    // Nếu không phải mã giới thiệu thì kiểm tra xem có phải mã giảm giá k
    if (empty($voucher_discount)) {
        $voucher_discount   =   $VoucherModel->getMoneyDiscountByCode($voucher_code, $total_money);
    } else {
        $is_voucher_referral    =   true;
    }
    $money_discount +=  $voucher_discount;

    $money_pay  -=  $money_discount;

    // lưu lại session để tính giá nếu đổi ngày
    $_SESSION['booking_info']['voucher']    =   $voucher_code;
    
}

//Nếu là đổi ngày/submit voucher thì sẽ request Ajax nên return data luôn để dùng JS fill vào form
if ($action == 'ajax') {
    if ($notfound) {
        echo json_encode([]);
    } else {
        echo json_encode([
            "total_money"       =>  show_money($total_money), 
            "money_pay"         =>  show_money($money_pay),
            "money_discount"    =>  show_money($money_discount),
            "rooms"             =>  $rooms,
            "count_night"       =>  round(($cfg_time_checkout - $cfg_time_checkin) / (60 * 60 * 24)),
            "error"             =>  empty($voucher_discount) ? $VoucherModel->error_code : '',
            "is_voucher_referral"=> $is_voucher_referral,
            "promotion_discount"=> $promotion_discount,
            'min_adult_number'  => $min_adult_number,
            'max_adult_number'  => $max_adult_number,
            'max_baby_number'   => $max_baby_number,
            'max_children_number'=> $max_children_number,
            'adult_number'      => $adult_number,
            'child_number'      => $child_number,
            'baby_number'       => $baby_number,
            'isOTA'             => $isOTA,
        ]);
    }
    exit;
}

//Tính số tiền tiết kiệm được so với giá công bố để show ở form booking
$money_saving   =   0;
if ($price_max < 1000000) {
    $money_saving   =   round_number($money_pay * 0.35);    //Áng chừng mức giá công bố cao hơn
} else if ($price_max < 2000000) {
    $money_saving   =   round_number($money_pay * 0.45);    //Áng chừng mức giá công bố cao hơn
} else if ($price_max < 3500000) {
    $money_saving   =   round_number($money_pay * 0.60);    //Áng chừng mức giá công bố cao hơn
} else if ($price_max < 6000000) {
    $money_saving   =   round_number($money_pay * 0.80);    //Áng chừng mức giá công bố cao hơn
} else {
    $money_saving   =   round_number($money_pay * 0.95);    //Áng chừng mức giá công bố cao hơn
}

//Tiền tặng voucher đặt lần sau
if ($money_pay > 0) {
    $money_bonus    =   get_money_bonus($money_pay);
} else {
    if ($hotel_info['hot_star'] >= 4) {
        $money_bonus    =   100000; //Với các KS từ 4 sao trở lên thì tối thiểu tặng 100k
    } else {
        $money_bonus    =   50000;  //Còn lại thì tặng tối thiểu 50k
    }
}

//Xử lý tạo đơn sau khi submit form
if ($action == 'booking') {
    
    //Nếu KH ko có email thì mặc định lấy Email của mình
    if (empty($email))  $email  =   VG_EMAIL;

    //Data tạo booking
    $data_create    =   [
        'hotel_id'      =>  $hotel_info["hot_id"],
        'data_room'     =>  $data_room,
        'user_id'       =>  $User->logged ? $User->id : 0,
        'name'          =>  $name,
        'email'         =>  $email,
        'phone'         =>  $phone,
        'adult'         =>  $adult_number,
        'children'      =>  $child_number,
        'baby'          =>  $baby_number,
        'daterange'     =>  $date_checkin . ' - ' . $date_checkout,
        'note'          =>  $note,
        'voucher_code'  =>  $voucher_code,
        'promotion_discount'  =>  $promotion_discount,
        'promotion_id'  =>  $PromotionModel->info()['id'],
        'city'          =>  $city
    ];

    if(!empty($error)) {
        return;
    }

    //Tạo booking
    $insert =   $BookingModel->createBookingHotel($data_create);
    
    if ($insert['booking_id'] > 0) {
        
        //Set session để sang trang thanks lấy thông tin
        $_SESSION['booking_completed']  =   $insert['booking_id'];
        
        //Set session để ko show form support ra
        $_SESSION['vg_sent_request']    =   1;
        
        // Hủy thông tin đặt phòng nếu có
        unset($_SESSION['booking_info']);

        redirect_url(DOMAIN_WEB . '/thanks/hotel/');
        
    } else {
        
        //Lưu Session thông tin để nếu quay lại chọn thời gian khác thì khi đặt lại ko cần phải nhập lại thông tin
        $_SESSION['booking_info']   +=  [
                                        'name'  =>  $name,
                                        'phone' =>  $phone,
                                        'email' =>  $email,
                                        'note'  =>  $note,
                                        'city'  =>  $city,
                                        'invoice_info' => $invoice_info,
                                        'invoice' => $invoice
                                        ];
        
        $error  =   $insert['message'];
        
        save_log('error_booking.cfn', "Data: " . json_encode($data_create) . "\nError: " . json_encode($error));

        $str_notify =   "Có khách đang đặt Booking bị sai thông tin:\n";
        $str_notify .=  "ĐT: " . $phone . "\n";
        $str_notify .=  "Họ tên: " . $name . "\n";
        $str_notify .=  "Email: " . $email . "\n";
        $str_notify .=  "Note: " . $note . "\n";
        foreach ($error as $e) {
            $str_notify .=  "Error: " . $e . "\n";
        }
        $str_notify .=  "URL: " . @$_SERVER['SERVER_NAME'] . urldecode(@$_SERVER["REQUEST_URI"]);
        
        pushNotifyTelegram($str_notify, $icon="%F0%9F%94%94", $chat_id='-4516202868');
        
    }
    
}

//Câu gợi ý đặt giá rẻ hơn
//Lấy thông tin tỉnh TP/Quận huyện
$city_info  =   $DB->query("SELECT cit_text_bonus FROM city WHERE cit_id = " . $hotel_info['hot_city'])->getOne();
$district_info  =   $DB->query("SELECT dis_text_bonus
                                FROM district
                                WHERE dis_id = " . $hotel_info['hot_district'])
                                ->getOne();
$text_suggest_bonus =   isset($cfg_website) ? $cfg_website['con_text_bonus_hotel'] : '';
if (!empty($district_info['dis_text_bonus'])) {
    $text_suggest_bonus =   $district_info['dis_text_bonus'];
} else if (!empty($city_info['cit_text_bonus'])) {
    $text_suggest_bonus =   $city_info['cit_text_bonus'];
}

$title  =   'Đặt phòng ' . $hotel_info['full_name'];

$Layout->setTitle($title)
        ->setDescription($title . ' mới nhất ' . date('Y') . ' giá rẻ tại Vietgoing')
        ->setIndex(false)
        ->setJS(['page.checkout']);
?>