<?
$cfg_path_image     =   DOMAIN_WEB . '/theme/images/';
$cfg_path_svg       =   DOMAIN_WEB . '/theme/svg/';

$cfg_path_banner        =   DOMAIN_STATIC . '/banner/';
$cfg_path_destination   =   DOMAIN_STATIC . '/destination/';
$cfg_path_tour          =   DOMAIN_STATIC . '/tour/';
$cfg_path_profile       =   DOMAIN_WEB . '/profile/';
$cfg_path_combo         =   DOMAIN_WEB . '/combo/';
$cfg_path_deal          =   DOMAIN_WEB . '/deal/';
$cfg_path_search        =   DOMAIN_WEB . '/search/';
$cfg_path_ticket        =   DOMAIN_WEB . '/ticket/';
$cfg_path_hotel_near    =   DOMAIN_WEB . '/hotel/';
$cfg_path_combo_vin     =   $cfg_path_combo . '?danh-muc=Combo Vinpearl';
$cfg_path_checkout_tour     =   DOMAIN_WEB . '/checkout/tour/';
$cfg_path_checkout_hotel    =   DOMAIN_WEB . '/checkout/hotel/';
$cfg_path_404       =   DOMAIN_WEB . '/404.php';
$cfg_default_image  =   $cfg_path_image . 'logo/logo-color-square.png';
$search_action_form =   '/search/';
$search_keyword     =   '';

$User   =   new User;
$Layout =   new Layout;
$PlaceModel     =   new PlaceModel;
$AttributeModel =   new AttributeModel;
$Search         =   new Search;

//Mảng chứa All city dùng thường xuyên nên lấy luôn
$cfg_city       =   $PlaceModel->getAllCity();
$cfg_city_area  =   $PlaceModel->getCityArea();
$cfg_hotel_type =   $cfg_hotel_type;

//Các trường dữ liệu cần lấy để show tour trong các list
$field_tour     =   "tou_id, tou_name, tou_image, tou_review_score, tou_review_count, tou_city_from, tou_city_to, tou_day, tou_night, tou_price, tou_price_original, tou_hot, tou_top, tou_col_3, tou_col_4, tou_schedule_date, tou_group, tou_col_1, tou_col_3, tou_last_update";
//Các trường dữ liệu cần lấy để show hotel trong các list
$field_hotel    =   "hot_id, hot_city, hot_district, hot_ward, hot_address_street, hot_review_score, hot_review_count, hot_star, hot_address_full, hot_picture, hot_name, hot_type, hot_hot, hot_lat, hot_lon, hot_col_1, hot_col_8, hot_free_airport";

/** Checkin checkout **/
$cfg_date_checkin   =   getValue('checkin', GET_STRING, GET_GET, getValue('checkin', GET_STRING, GET_COOKIE, date('d/m/Y')));
$cfg_date_checkout  =   getValue('checkout', GET_STRING, GET_GET, getValue('checkout', GET_STRING, GET_COOKIE, date('d/m/Y', CURRENT_TIME + 86400)));

$cfg_time_checkin   =   str_totime($cfg_date_checkin);
$cfg_time_checkout  =   str_totime($cfg_date_checkout);

//Validate tính hợp lệ của time
if ($cfg_time_checkin < strtotime(date('m/d/Y'))) {
    $cfg_date_checkin   =   date('d/m/Y');
    $cfg_time_checkin   =   str_totime($cfg_date_checkin);
}
if ($cfg_time_checkout <= $cfg_time_checkin) {
    $cfg_time_checkout  =   $cfg_time_checkin + 86400;
    $cfg_date_checkout  =   date('d/m/Y', $cfg_time_checkout);
}

//Cho tối đa 15 ngày
// Tạm thời comment cho đỡ lỗi chọn 20 ngày nhưng show 15 ngày
// if ($cfg_time_checkout - $cfg_time_checkin > 15 * 86400) {
//     $cfg_time_checkout  =   $cfg_time_checkin + 15 * 86400;
//     $cfg_date_checkout  =   date('d/m/Y', $cfg_time_checkout);
// }

$cfg_total_night    =   round(($cfg_time_checkout - $cfg_time_checkin) / 86400);
$time_expired = CURRENT_TIME + 7 * 86400;
setcookie('checkin', $cfg_date_checkin, $time_expired, '/', '');
setcookie('checkout', $cfg_date_checkout, $time_expired, '/', '');

/** Các tiêu chí đánh giá **/
$cfg_review_label   =   [
                        5   =>  'Tuyệt vời',
                        4   =>  'Rất hài lòng',
                        3   =>  'Bình thường',
                        2   =>  'Tạm được',
                        1   =>  'Quá tệ',
                        0   =>  ''
                        ];

?>