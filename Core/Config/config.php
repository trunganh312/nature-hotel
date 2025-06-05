<?php
//Các biến đường dẫn theme

use src\Facades\DB;
use src\Models\BookingHotel;

$cfg_url_fontawesome    =   'https://fontawesome.com/v5/search';

//Một số biến dùng chung cho toàn bộ cả website cả CMS
$DB     =   DB::getInstance();
$Image  =   new Image;
$Model  =   new Model;

/** Các hình thức thanh toán chung **/
$cfg_payment_method =   [
                        PAYMENT_CASH    =>  'Tiền mặt',
                        PAYMENT_BANK    =>  'Chuyển khoản',
                        PAYMENT_VISA    =>  'Visa',
                        PAYMENT_MOTO    =>  'Moto',
                        ];

// Các hình thức thanh toán TA
$cfg_payment_method_ta =   [
        PAYMENT_CASH    =>  'Tiền mặt',
        PAYMENT_BANK    =>  'Chuyển khoản',
        PAYMENT_PARTNER =>  'Đối tác thu hộ',
];

// Các hình thức thanh toán HMS
$cfg_payment_method_hms =   [
    PAYMENT_CASH    =>  'Tiền mặt',
    PAYMENT_VISA    =>  'Visa',
];

// Các loại thanh toán
$cfg_payment_type =   [
    PAYMENT_TYPE_CUSTOMER   =>  'Khách thanh toán',
    PAYMENT_TYPE_PARTNER    =>  'Đối tác hoàn tiền',
];

//Các tiêu chí đánh giá tour
$cfg_review_criteria_tour   =   [
                            1   =>  'Lịch trình',
                            2   =>  'Hướng dẫn viên',
                            3   =>  'Phương tiện',
                            4   =>  'Lưu trú',
                            5   =>  'Ăn uống'
                            ];
                            
                            
//Các tiêu chí đánh giá ks
$cfg_review_criteria_hotel    =   [
                            1   =>  'Sạch sẽ',
                            2   =>  'Tiện nghi',
                            3   =>  'Đồ ăn',
                            4   =>  'Vị trí',
                            5   =>  'Phục vụ',
                            6   =>  'Thời gian phản hồi',
                            7   =>  'Thái độ hỗ trợ',
                            8   =>  'Độ chuyên nghiệp',
                            9   =>  'Uy tín',
                            ];
//Các tiêu chí đánh giá TA
$cfg_review_criteria_ta    =   [
                            1   =>  'Thái độ hợp tác',
                            2   =>  'Thời gian thanh toán',
                            3   =>  'Uy tín',
                            4   =>  'Thời gian phản hồi',
                            ];

/** Mảng chứa các table booking để dùng cho các tính năng thống kê tổng hợp **/
$cfg_table_booking  =   [
    'booking_hotel'     =>  'bkho_',
];

/** Mảng chứa tất cả các bảng booking và request để xử lý một số nghiệp vụ chung **/
$cfg_table_request_all  =   [
    'customer_request'  =>  'cure_',
    'booking_hotel'     =>  'bkho_',
];

$cfg_request_status =   [
    STT_NEW         =>  'Request mới',
    STT_PROCESS     =>  'Đang xử lý',
    STT_SUCCESS     =>  'Đã tạo đơn',
    STT_FAIL        =>  'Đã hủy'
];

$cfg_booking_status =   [
    STT_NEW         =>  'Đơn mới',
    STT_PROCESS     =>  'Đang xử lý',
    STT_HOLD        =>  'Tạm giữ',
    STT_SUCCESS     =>  'Thành công',
    STT_COMPLETE    =>  'Hoàn thành',
    STT_CANCEL      =>  'Đơn hủy',
    STT_CONFIRM     =>  'Đơn xác nhận',
    STT_FAIL        =>  'Đơn thất bại',
    STT_NO_SHOW     =>  'Không đến',
    STT_STAY        =>  'Đang lưu trú',
    STT_OUT_ROOM    =>  'Đã trả phòng'
];


/** Các kênh đặt booking **/
$cfg_data_source_hms    =   [
    SOURCE_TA       =>  'TA',
    SOURCE_OTA      =>  'OTAs',
    SOURCE_WALKIN   =>  'Walk-in',
    SOURCE_CALL     =>  'Hotline',
    SOURCE_SOCIAL   =>  'Social',
    SOURCE_WEB      =>  'Website',
    SOURCE_CORPORATE=>  'Corporate',
    SOURCE_OTHER    =>  'Kênh khác',
];

// Định nghĩa các nền tảng Social
$cfg_social_platforms = [
    SOURCE_TIKTOK       =>  'Tiktok',
    SOURCE_FACEBOOK     =>  'Facebook',
    SOURCE_INSTAGRAM    =>  'Instagram',
    SOURCE_ZALO         =>  'Zalo',
    SOURCE_YOUTUBE      =>  'Youtube',
];

$cfg_data_source_ta =   [
    SOURCE_CALL     =>  'Gọi điện',
    SOURCE_ZALO     =>  'Zalo',
    SOURCE_FACEBOOK =>  'Facebook',
    SOURCE_TIKTOK   =>  'Tiktok',
    SOURCE_CTV      =>  'Cộng tác viên',
    SOURCE_CSKH      =>  'CSKH cũ',
    SOURCE_INTRO    =>  'Khách giới thiệu',
    SOURCE_WEB      =>  'Website',
    SOURCE_CORPORATE=>  'Corporate',
    SOURCE_OTHER    =>  'Kênh khác',
];

$cfg_source_ota =   [
    AGODA           =>  'Agoda',
    BOOKING_COM     =>  'BK-com',
    TRAVELOKA       =>  'Traveloka',
    EXPEDIA          => 'Expedia',
    TRIPADVISOR     =>  'Tripadvisor',
    AIRBNB          =>  'Airbnb',
    CTRIP           =>  'Ctrip',
    TRIP_COM        =>  'Trip-com',
    HOTEL_COM       =>  'Hotels-com',
    GO2JOY          =>  'Go2joy',
    HOTELBEDS       =>  'Hotelbeds',
    MYTOUR          =>  'Mytour',
    IVIVU           =>  'Ivivu',
];


/** Các lý do hủy booking **/
$cfg_fail_type  =   [
    FAIL_OUTROOM        =>  'Hết phòng',
    FAIL_CANCEL         =>  'Khách hết nhu cầu',
    FAIL_PAYMENT        =>  'Không thanh toán',
    FAIL_EXPENSIVE      =>  'Chê giá cao',
    FAIL_COMPETITOR     =>  'Đặt bên khác',
    FAIL_DUPLICATE      =>  'Trùng request',
    FAIL_CONNECT        =>  'Khách không nghe máy',
    FAIL_WRONG_PHONE    =>  'Sai số, không liên lạc được',
    FAIL_OTHER_SERVICE  =>  'Hỏi dịch vụ khác',
    FAIL_OTHER_REASON   =>  'Lý do khác'
];

//Lấy sẵn DS city vì dùng ở nhiều nơi
$cfg_city   =   $Model->getListData('cities', 'cit_id, cit_name', '', 'cit_name');

$cfg_type_attribute     =   [
    ATTRIBUTE_SELECT    =>  'Chọn một giá trị',
    ATTRIBUTE_MULTI     =>  'Chọn nhiều giá trị',
    //ATTRIBUTE_TEXT      =>  'Nhập text tự do'
];

/** Sex **/
$cfg_sex    =   [
    SEX_MALE    =>  'Nam',
    SEX_FEMALE  =>  'Nữ'
];

/** Các nhóm đối tượng chính của website **/
$cfg_group      =   [
    GROUP_HOTEL     =>  'Khách sạn',
    GROUP_TOUR      =>  'Tour',
    GROUP_TICKET    =>  'Vé',
    GROUP_ROOM      =>  'Phòng'
];
$cfg_group_alias    =   [
    GROUP_HOTEL     =>  'hotel',
    GROUP_TOUR      =>  'tour',
    GROUP_TICKET    =>  'ticket',
    GROUP_ROOM      =>  'room'
];

/** Các nhóm module tính năng của hệ thống **/
$cfg_module_group_folder   =   [
    MODULE_SENNET   =>  'crm',
    MODULE_HOTEL    =>  'hms',
    MODULE_TOUR     =>  'tms',
    MODULE_AGENCY   =>  'ta',
    MODULE_HRM      =>  'hrm',
];
$cfg_module_group_name  =   [
    MODULE_SENNET   =>  'SENNET',
    MODULE_HOTEL    =>  'Lưu trú',
    MODULE_TOUR     =>  'Lữ hành',
    MODULE_AGENCY   =>  'Đại lý',
    MODULE_HRM      =>  'Nhân sự',
    MODULE_EMS      =>   'EMS'
];

/**
 * Các module có các tính năng sử dụng chung cho tất cả các cty: Quản lý nhân sự, Quản lý KH...
 */
$cfg_module_common  =   [MODULE_HRM];

/** Các nhóm công ty sử dụng hệ thống. Các folder module tính năng được chia theo các nhóm cty này **/
$cfg_company_group  =   [
    MODULE_HOTEL   =>  'Lưu trú',
    MODULE_TOUR    =>  'Lữ hành',
    MODULE_AGENCY  =>  'Đại lý'
];

/** Các loại Phòng/Ban */
$cfg_department_type    =   [
    DEPARTMENT_SALE =>  'Kinh doanh',
    DEPARTMENT_MKT  =>  'Marketing',
    DEPARTMENT_HR   =>  'Nhân sự',
    DEPARTMENT_ACCOUNTANT   =>  'Kế toán'
];

/** Các kiểu giảm giá **/
$cfg_discount_type  =   [
    DISCOUNT_TYPE_PERCENT   =>  '%',
    DISCOUNT_TYPE_MONEY     =>  'VNĐ'
];

/** Các kiểu loại giá show cho TA và không phải TA **/
$cfg_type_show_price  =   [
    STT_CONFIRM     =>  'Dành cho TA có hợp tác',
    STT_CANCEL      =>  'Dành cho TA chưa hợp tác'
];

/** --- Các kiểu trường dữ liệu lưu log --- **/                                
$cfg_field_type =   [
    FIELD_TEXT      =>  'Text',
    FIELD_DATABASE  =>  'Database',
    FIELD_CONSTANT  =>  'Constant',
    FIELD_TIME      =>  'Thời gian',
    FIELD_BASE64    =>  'Base64',
];

$cfg_resize_image   =   [
    SIZE_LARGE  =>  ['maxwidth' => 1200, 'maxheight' => 900],
    SIZE_MEDIUM =>  ['maxwidth' => 800, 'maxheight' => 600],
    SIZE_SMALL  =>  ['maxwidth' => 400, 'maxheight' => 300]
];

/** Các loại hình lưu trú **/
$cfg_hotel_type =   [
    HOTEL_TYPE_HOTEL    =>  'Khách sạn',
    HOTEL_TYPE_RESORT   =>  'Resort',
    HOTEL_TYPE_HOMESTAY =>  'Homestay',
    HOTEL_TYPE_YACHT    =>  'Du thuyền',
    HOTEL_TYPE_VILLA    =>  'Căn hộ',
    HOTEL_TYPE_COMPLEX  =>  'Tổ hợp',
];

/** Các tiêu chí xếp loại phòng **/
$cfg_room_criteria  =   [
    ROOM_CRITERIA_CATEGORY  =>  'Hạng phòng',
    ROOM_CRITERIA_BED       =>  'Loại giường'
];

/** --- Số sao của KS --- **/
$cfg_hotel_star =   [
    0   =>  '0 sao',
    1   =>  '1 sao',
    2   =>  '2 sao',
    3   =>  '3 sao',
    4   =>  '4 sao',
    5   =>  '5 sao'
];

/** Các kiểu chi tiền **/
$cfg_type_spend =   [
    SPEND_MKT       =>  'Marketing',
    SPEND_PARTNER   =>  'Đối tác',
    SPEND_SALARY    =>  'Tiền lương',
    SPEND_OFFICE    =>  'Văn phòng',
    SPEND_HOTLINE   =>  'Cước hotline',
    SPEND_OTHER     =>  'Chi khác'
];

/** Các kênh MKT **/
$cfg_mkt_chanel =   [
    MKT_GOOGLE      =>  'Google',
    MKT_FACEBOOK    =>  'Facebook',
    MKT_WEBSITE     =>  'Website',
    MKT_PARTNER     =>  'Partner',
    MKT_YOUTUBE     =>  'Youtube',
    MKT_EMAIL       =>  'Email'
];

/** Các Account Ads **/
$cfg_ads_account    =   [
];

/** Các trạng thái xử lý booking với partner **/
$cfg_status_partner =   [
    PART_NONE       =>  'Chưa đặt dịch vụ',
    PART_SENT       =>  'Đã gửi đối tác',
    PART_CONFIRMED  =>  'Đối tác đã xác nhận'
];

/** Mảng dùng chung cho các label màu **/
$cfg_label_color    =   [
    0   =>  'bg-olive',
    1   =>  'bg-info',
    2   =>  'bg-purple',
    3   =>  'bg-success',
    4   =>  'bg-indigo',
    5   =>  'bg-danger',
];

// Màu trạng thái request
$cfg_request_color =   [
    STT_NEW         =>  'bg-olive',
    STT_PROCESS     =>  'bg-info',
    STT_SUCCESS     =>  'bg-success',
    STT_FAIL        =>  'bg-danger',
];

/** Các loại hình đối tác **/
$cfg_type_partner   =   [
    1   =>  'Lưu trú',
    2   =>  'Lữ hành',
    3   =>  'Vé máy bay',
    4   =>  'Vận chuyển',
    5   =>  'Nhà hàng',
    6   =>  'HDV, Game',
    7   =>  'DV phụ trợ',
    15  =>  'DV khác'
];

/** Các màu dùng cho chart và thống kê **/
$cfg_color  =   [
    0   =>  '#27c24c',  //Xanh logo
    1   =>  '#ffc107',  //Cam logo
    2   =>  '#007ac7',  //Xanh da trời
    3   =>  '#f62394',  //Tím hồng
    4   =>  '#23b7e5',  //Xanh lam nhạt
    5   =>  '#0024ba',  //Xanh da trời đậm
    6   =>  '#8b20bb',  //Tím
    7   =>  '#83ce01',  //Xanh lá cây nhạt
    8   =>  '#fffa00',  //Vàng
    9   =>  '#f791e2',  //Tím nhẹ
    10  =>  '#ff2000',  //Đỏ
    11  =>  '#ffcf00',  //Cam vàng
    12  =>  '#b2ff59',  //Xanh lá cây sáng
    13  =>  '#ffff66',  //Vàng nhạt
    14  =>  '#ffb3ff',  //Tím nhạt
    15  =>  '#ffe6e6',  //Hồng nhạt
    16  =>  '#ccffff',  //Xanh lam nhạt
    17  =>  '#ffffcc',  //Vàng nhạt
    18  =>  '#e6ccff',  //Tím nhạt
    19  =>  '#ccffcc',  //Xanh lá cây nhạt
    20  =>  '#ffffb3',  //Vàng nhạt
    21  =>  '#ffcccc',  //Hồng nhạt
    99  =>  '#4d8dbe'
];

/** Các kiểu lấy VAT **/
$cfg_vat_note   =   [
    1   =>  'Không VAT',
    2   =>  'Có lấy VAT',
    3   =>  'Có VAT, Không lấy'
];

/** Đoạn SQL where để loại ra các bk test và trùng **/
$cfg_sql_exclude_data_same_test =   "fail_type NOT IN(" . FAIL_DUPLICATE . "," . FAIL_TEST . "," . FAIL_WRONG_PHONE . "," . FAIL_OTHER_SERVICE . "," . FAIL_CALL_LATE . ")";

/** Phân loại đối tượng nhân sự **/
$cfg_hr_group   =   [
    HR_COMPANY      =>  'Công ty',
    HR_HOTEL        =>  'Khách sạn',
    HR_DEPARTMENT   =>  'Phòng/Ban',
    HR_INDIVIDUAL   =>  'Cá nhân'
];

/** Các kênh liên hệ KH **/
$cfg_contact_method =   [
    CONTACT_CALL    =>  'Gọi điện',
    CONTACT_ZALO    =>  'Zalo',
    CONTACT_UNABLE  =>  'Không liên hệ được'
];

/** Phân loại đối tượng khách hàng booking **/
$cfg_booking_customer_type   =   [
    BookingHotel::CUSTOMER_INDIVIDUAL   =>  'Khách lẻ',
    BookingHotel::CUSTOMER_GROUP        =>  'Khách đoàn',
];

/** Các nhóm ảnh KS **/
$cfg_image_group_hotel  =   [
    IMAGE_MAIN          =>  'Ảnh đại diện',
    IMAGE_ROOM          =>  'Ảnh phòng',
    IMAGE_EXTERIOR      =>  'Toàn cảnh, ngoại cảnh', 
    IMAGE_POOL          =>  'Bể bơi',
    IMAGE_RECEPTION     =>  'Sảnh chính, lễ tân',
    IMAGE_RESTAURANT    =>  'Nhà hàng, Bar',
    IMAGE_AMENITY       =>  'Tiện ích, dịch vụ',
    IMAGE_COMMON        =>  'Khu vực chung',
    IMAGE_OUTDOOR       =>  'Ngoài trời',
    IMAGE_ACTIVITY      =>  'Hoạt động, trải nghiệm'
];

/** Các trạng thái xử lý booking với partner **/
$cfg_status_partnership =   [
    STT_NEW         =>  'Chờ xác nhận',
    STT_CONFIRM     =>  'Xác nhận hợp tác',
    STT_CANCEL      =>  'Từ chối hợp tác'
];

/** Các trạng thái xử lý thu tiền **/
$cfg_status_bank_transfer =   [
    STT_NEW         =>  'Chưa xác nhận',
    STT_PROCESS     =>  'Xác nhận một phần',
    STT_CONFIRM     =>  'Đã xác nhận đủ',
];

/** Các nhóm phụ thu **/
$cfg_surcharge_group =   [
    SURCHARGE_ALL         =>  'Tất cả',
    SURCHARGE_CI_CO       =>  'Checkin/ Checkout',
];

/** Các loại giá mặc định **/
$cfg_price_type_default =   [
    PRICE_RAC       => 'Giá công bố',
    PRICE_WALK_IN   => 'Giá Walk-in',
    PRICE_TA        => 'Giá đại lý',
    PRICE_OTA       => 'Giá OTA',
    PRICE_CORP      => 'Giá CORP'
];

$cfg_price_type_default_code =   [
    PRICE_RAC       => 'RAC',
    PRICE_WALK_IN   => 'Walk-in',
    PRICE_TA        => 'TA',
    PRICE_OTA       => 'OTA',
    PRICE_CORP      => 'CORP'
];

/** Các loại tài sản */
$cfg_asset_type = [
    ASSET_ROOM => 'Tài sản phòng',
    ASSET_RESTAUNT => 'Tài sản nhà hàng',
    ASSET_PHYSICAL => 'Tài sản cơ sở vật chất',
];
/** Các nhóm tài sản */
$cfg_asset_group =   [
    ASSET_INTANGIBLE    =>  'Dịch vụ',
    ASSET_TANGIBLE  =>  'Sản phẩm',
];

/** Các loại cấu hình mã màu */
$cfg_color_config =   [
    COLOR_ROOM_EMPTY            =>  'Màu phòng trống',
    COLOR_ROOM_CHECKIN_TODAY    =>  'Màu phòng checkin hôm nay',
    COLOR_ROOM_STAY             =>  'Màu phòng đang ở',
    COLOR_ROOM_NO_SHOW          =>  'Màu phòng không đến',
    COLOR_ROOM_CLEAN            =>  'Màu phòng đang dọn',
    COLOR_ROOM_CHECK            =>  'Màu kiểm phòng',
    COLOR_ROOM_BROKEN           =>  'Màu phòng hỏng',
    COLOR_ROOM_INSPECTION       =>  'Màu phòng kiểm tra',
    COLOR_ROOM_OUT              =>  'Màu phòng khách ra ngoài',
];
