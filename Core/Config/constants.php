<?
/** ======= Cac kieu du lieu add vao form ======= **/
define('DATA_STRING', 0);   //Kieu String
define('DATA_INTEGER', 1);  //Kieu Integer
define('DATA_DOUBLE', 2);   //Kieu Double
define('DATA_TIME', 3);    //Kieu Time, lưu time unix

define('DATA_FORM', 0); //Du lieu duoc lay tu form
define('DATA_VARIABLE', 1); //Du lieu duoc lay tu bien
/** ======= End of cac kieu du lieu add vao form ======= **/

/** --- Các kiểu dữ liệu dùng trong class DataTable --- **/
define('TAB_TEXT', 1);    //Kieu Text
define('TAB_NUMBER', 2);    //Kieu số
define('TAB_SELECT', 3);     //Kiểu mảng danh sách
define('TAB_IMAGE', 4); //Kiểu hiển thị ảnh
define('TAB_DATE', 5);  //Kiểu hiển thị ngày tháng
define('TAB_CHECKBOX', 6);  //Kiểu checkbox
define('TAB_MULTI', 7); //Kiểu 1 trường tìm kiếm theo nhiều giá trị (Where IN)

/** Các kiểu get dữ liệu dùng cho hàm getValue **/
define('GET_STRING', 'str');
define('GET_INT', 'int');
define('GET_DOUBLE', 'dbl');
define('GET_ARRAY', 'arr');
define('GET_GET', 'GET');
define('GET_POST', 'POST');
define('GET_SESSION', 'SESSION');
define('GET_COOKIE', 'COOKIE');
define('GET_JSON', 'JSON');

/** Password default **/
define('PWD_DEFAULT', 'Sen2024');
define('REGISTER_SELF', 0); //Khách tự đk tài khoản
define('REGISTER_AUTO', 1); //Tạo tk tự động

/** Các môi trường CRM **/
define('ADMIN_CRM', 1); //Môi trường CRM
define('ADMIN_USER', 2);    //Môi trường dùng cho User

/** Các nhóm module tính năng, các nhóm cty cũng được gán theo nhóm module này **/
define('MODULE_SENNET', 1);    //Sennet
define('MODULE_HOTEL', 2);    //Các module tính năng của nhà quản lý KS
define('MODULE_TOUR', 3);    //Các module tính năng của cty lữ hành
define('MODULE_AGENCY', 4);    //Các module tính năng của đại lý du lịch
define('MODULE_HRM', 5);    //Các module tính năng của quản lý nhân sự (Chung cho Hotel, Tour, Agency)
define('MODULE_EMS', 6);    //Các module tính năng thống kê của công ty HMS

/** --- Group đối tượng của project --- **/
define('GROUP_TOUR', 1);
define('GROUP_HOTEL', 2);
define('GROUP_TICKET', 3);
define('GROUP_ACTIVITY', 4);
define('GROUP_ARTICLE', 5);
define('GROUP_DESTINATION', 6);
define('GROUP_ROOM', 7);
define('GROUP_OTHER', 8);
define('GROUP_TA', 9);


/** -- Các loại hình lưu trú --  **/
define('HOTEL_TYPE_HOTEL', 1);  //Khách sạn
define('HOTEL_TYPE_RESORT', 2); //Resort
define('HOTEL_TYPE_HOMESTAY', 3);   //Homestay
define('HOTEL_TYPE_VILLA', 4);  //Villa đơn lẻ
define('HOTEL_TYPE_YACHT', 5);  //Du thuyền
define('HOTEL_TYPE_COMPLEX', 6);    //Tổ hợp du lịch

/** -- Các tiêu chí xếp loại phòng --  **/
define('ROOM_CRITERIA_CATEGORY', 1);    //Xếp theo Hạng phòng (Standard, Superior, Deluxe...)
define('ROOM_CRITERIA_BED', 2); //Xếp theo loại giường (Single, Twin, Double...)

/** --- Giới tính --- **/
define('SEX_MALE', 1);
define('SEX_FEMALE', 2);

// phụ thu và dịch vụ
define('GROUP_SERVICE', 1);
define('GROUP_SURCHARGE', 2);

/** --- Phụ thu --- **/
// Mặc định phải để bằng 1 vì đây là trường đặc biệt
define('SURCHARGE_EXTRA_BED', 1);

/** --- Kiểu dữ liệu của các attribute --- **/
define('ATTRIBUTE_SELECT', 1);  //Kiểu chọn 1 giá trị
define('ATTRIBUTE_MULTI', 2);   //Kiểu chọn nhiều giá trị
define('ATTRIBUTE_TEXT', 3);    //Kiểu nhập text tự do

/** Một số ID của Attribute đặc biệt **/
define('ATTR_TOUR_TYPE', 5);   //ID Atttribute của loại tour
define('ATTR_COMBO_CATEGORY', 6);   //ID của Attribute Nhóm combo, chỉ show ở bộ lọc trang listing combo, ko show ở trang Tour
define('ATTR_COMBO_VINPEARL', 34);  //ID của Attribute Value Vinpearl
define('ATTR_COMBO_FLC', 35);  //ID của Attribute Value FLC

/** Category của tour trong nước và tour nước ngoài **/
define('CATE_TOUR_VN', 9);
define('CATE_TOUR_FOREIGN', 10);
define('CATE_TOUR_COMBO', 39);

/** --- Số lượng folder lưu ảnh --- **/
define('FOLDER_PARENT', 1000);  //1000 item trong một folder cha. Ko được thay đổi con số này.
define('FOLDER_CHILD', 100);    //Chia folder cha thành 100 folder con. Ko được thay đổi con số này.
define('MAX_UPLOAD_SIZE', 2);   //MBành 100 folder con. Ko được thay đổi con số này.

/** --- Tên các thư mục lưu ảnh theo kích thước --- **/
define('SIZE_LARGE', 'large');
define('SIZE_MEDIUM', 'medium');
define('SIZE_SMALL', 'small');
define('SIZE_BIG', 'big');

/** Các vị trí banner của website **/
define('BANNER_HEADER_SLIDER', 1);  //Banner text slider ở header
define('BANNER_HOME_TOP', 2);   //Banner ở trang chủ ngay dưới box search

/** Lấy current time cho đồng bộ **/
define('CURRENT_TIME', time());

/** Các trạng thái của booking **/
// Nhóm trạng thái bắt đầu 0 -> 4
define('STT_NEW', 0);   //Đơn mới

// Nhóm trạng thái xử lý 5 -> 14
define('STT_PROCESS', 5);   //Đơn đang được xử lý
define('STT_HOLD', 6);   //Giữ phòng
define('STT_CONFIRM', 7);   //Đã xác nhận
define('STT_STAY', 8);   //Khách đang lưu trú
define('STT_NO_SHOW', 9);   //Khách ko đến
define('STT_SEND', 10);   // Gửi booking
define('STT_OUT_ROOM', 11);   // Booking khách đã trả phòng

// Nhóm trạng thái hoàn thành 15 -> 19
define('STT_COMPLETE', 15); //Đơn đã hoàn thành
define('STT_SUCCESS', 16); //Request thành công

// Nhóm trạng thái thất bại 20 -> 25
define('STT_CANCEL', 20);   //Đơn hủy: Các đơn đã được xác nhận nhưng sau đó khách hủy
define('STT_FAIL', 21);   //Đơn thất bại: Các đơn mà tư vấn nhưng khách ko chốt đặt

/** Các lý do hủy đơn **/
define('FAIL_OUTROOM', 1); //Hết phòng
define('FAIL_CANCEL', 2);   //Khách hủy, hết nhu cầu, ko đặt nữa
define('FAIL_PAYMENT', 3);  //Khách ko thanh toán
define('FAIL_EXPENSIVE', 4);    //Chê giá cao
define('FAIL_COMPETITOR', 5);   //Đặt bên khác
define('FAIL_DUPLICATE', 6);    //Trùng đơn/request khác
define('FAIL_CONNECT', 7);    //Gọi khách ko nghe máy
define('FAIL_WRONG_PHONE', 8); //Sai số
define('FAIL_CALL_LATE', 9); //Gọi lại muộn, khách đặt được rồi
define('FAIL_OTHER_SERVICE', 10); //Khách hỏi các dịch vụ khác ko liên quan
define('FAIL_TEST', 11);    //Đặt nhầm/đặt cho vui/Đặt test
define('FAIL_OTHER_REASON', 20);    //Cho hẳn lên 20 để sau có phát sinh thêm nguồn khác

/** Các hình thức thanh toán **/
define('PAYMENT_CASH', 1);
define('PAYMENT_BANK', 2);  //Chuyển khoản
define('PAYMENT_VISA', 4);  //Thẻ Visa
define('PAYMENT_GATE', 3);  //Thanh toán qua cổng thanh toán
define('PAYMENT_PARTNER', 5);  // Đối tác thu hộ
define('PAYMENT_MOTO', 6);  // MOTO
define('PAYMENT_DEBT', 7);  // CÔNG NỢ
define('PAYMENT_OTA', 8);  // OTAS

/** Các loại thanh toán */
define('PAYMENT_TYPE_CUSTOMER', 1); // Khách thanh toán
define('PAYMENT_TYPE_PARTNER', 2);  // Đối tác hoàn tiền

/*Đơn vị tiền*/
define('CURRENCY_VND', 1); //Đồng Việt Nam
define('CURRENCY_USD', 2); //Đồng đô la

/** --- Các biến khác --- **/
define('CFG_PARTITION_TABLE', 0);

/** Các nguồn đặt Booking **/
define('SOURCE_TA', 1);   //TA
define('SOURCE_OTA', 2);   //OTA
define('SOURCE_WALKIN', 3);   //Walkin
define('SOURCE_CALL', 4);   //Gọi điện
define('SOURCE_ZALO', 5);   //Zalo
define('SOURCE_FACEBOOK', 6);    //Fanpage FB
define('SOURCE_TIKTOK', 7); //Tiktok
define('SOURCE_SOCIAL', 11); //Từ các kênh Social khác: Group FB, youtube...
define('SOURCE_RETURN', 8); //Khách cũ
define('SOURCE_INTRO', 9);  //Được khách hàng giới thiệu
define('SOURCE_WEB', 10); //Website
define('SOURCE_CTV', 12);   //Cộng tác viên
define('SOURCE_CSKH', 13);   //Cộng tác viên
define('SOURCE_CALL_CANCEL_REQUEST', 14);   //Gọi lại request hủy
define('SOURCE_CORPORATE', 15);   //Gọi lại request hủy
define('SOURCE_INSTAGRAM', 16);    //Fanpage FB
define('SOURCE_YOUTUBE', 17);    //Fanpage FB
define('SOURCE_OTHER', 99); //Các kênh chưa rõ ràng

/** --- MÃ thông báo API --- **/
define('REQUEST_ERROR', 422); // Trường hợp return các lỗi
define('REQUEST_SUCCESS', 200); // Trường hợp return data thành công 
define('TOKEN_EXPIRE', 401); // Access token hết hiệu lực

/** --- Kiểu giảm giá --- **/
define('DISCOUNT_TYPE_PERCENT', 1);  //Giảm theo %
define('DISCOUNT_TYPE_MONEY', 2);    //Giảm theo số tiền

/** Điểm tối đa của đánh giá **/
define('REVIEW_MAX_SCORE', 10); //Đánh giá theo thagn điểm 10
define('REVIEW_NUMBER_CRITERIA', 9);    //Có 5 tiêu chi đnáh giá

/** Param để tracking từ Ads **/
define('PARAM_ADS', 'utm_vg');  //Param click từ các Ads
define('PARAM_WEB', 'utm_web'); //Param click từ các box trên website

/** Mốc thời gian đầu ngày **/
define('TODAY_BEGIN', strtotime(date('m/d/Y')));

/** Các khoản chi tiền **/
define('SPEND_MKT', 1); //Chi phí MKT
define('SPEND_PARTNER', 2); //Chi phí trả cho các đối tác
define('SPEND_SALARY', 3);  //Tiền lương
define('SPEND_OFFICE', 4);  //Tiền văn phòng, liên quan VP
define('SPEND_HOTLINE', 5); //Cước hotline
define('SPEND_OTHER', 9);

/** Các kênh MKT **/
define('MKT_GOOGLE', 1);
define('MKT_FACEBOOK', 2);
define('MKT_WEBSITE', 3);   //Ads đặt trực tiếp trên Vietgoing.com
define('MKT_PARTNER', 4);   //Ads đặt trên các website khác
define('MKT_YOUTUBE', 5);
define('MKT_EMAIL', 6);

/** Các TK Ads **/
define('ADS_ACC_G_VG_1', 1); //VG-2022, vietgoing.com
define('ADS_ACC_G_NQH_1', 2);   //VG-2023, quanghieu2104
define('ADS_ACC_F_VG_1', 3);    //FB - Account Vietgoing
define('ADS_ACC_W_VG_1', 4);    //Website - Banner
define('ADS_ACC_G_VG_2', 5);    //VG-23 - HighCost, vietgoing.com

/** Các trạng thái xử lý với partner của booking **/
define('PART_NONE', 0);  //Chưa xử lý gì
define('PART_SENT', 1);  //Đã gửi thông tin đặt dịch vụ cho Partner
define('PART_CONFIRMED', 2);    //KS đã gửi xác nhận

/** --- Các kiểu dữ liệu dùng để lưu log --- **/
define('FIELD_TEXT', 1);   //Kiểu trường lưu log dạng text
define('FIELD_DATABASE', 2);    //Kiểu trường lưu log cần lấy ra text theo ID của 1 bảng trong database
define('FIELD_CONSTANT', 3);    //Kiểu trường lưu log dạng lấy theo constant
define('FIELD_TIME', 4);    //Kiểu thời gian
define('FIELD_BASE64', 5);    //Kiểu thời gian

define('LOG_CREATE', 1);    //Log create record
define('LOG_UPDATE', 2);    //Log update
define('LOG_DELETE', 3);
define('LOG_VIEW', 4);  //Log các hành động view

/** Các lý do hủy của request **/
define('FAIL_REQ_NO_NEED', 1);
define('FAIL_REQ_NO_CALL', 2);
define('FAIL_REQ_PRICE', 3);
define('FAIL_REQ_VEHICLE', 4);
define('FAIL_REQ_COVID', 5);
define('FAIL_REQ_COMPETITOR', 6);
define('FAIL_REQ_OTHER', 15);

/** Số ngày sau khi đơn checkout sẽ gọi lại để CSKH **/
define('NUMBER_DAY_CSKH', 120);

/** Số ngày nhắc hết hạn voucher **/
define('NUMBER_DAY_VOUCHER_REMINDER', 10);

/** Kích thước tối thiểu của ảnh đại diện **/
define('MIN_WIDTH_MAIN', 1200);
define('MIN_HEIGHT_MAIN', 900);

/** Trạng thái cuộc gọi **/
define('HOTLINE_MISS', 1); // Gọi nhỡ
define('HOTLINE_MEET', 2); // Thành công

/** Loại cuộc gọi **/
define('HOTLINE_CALL_IN', 1); // Gọi vào
define('HOTLINE_CALL_OUT', 2); // Gọi ra

/** Các phòng ban **/
define('DEPARTMENT_SALE', 1);
define('DEPARTMENT_MKT', 2);
define('DEPARTMENT_HR', 3);
define('DEPARTMENT_ACCOUNTANT', 4);

/** Phân loại đối tượng nhân sự **/
define('HR_INDIVIDUAL', 1);
define('HR_DEPARTMENT', 2);
define('HR_COMPANY', 3);
define('HR_HOTEL', 4);

/** Các kênh liên lạc với KH **/
define('CONTACT_CALL', 1);  //Gọi điện
define('CONTACT_ZALO', 2);  //Zalo
define('CONTACT_UNABLE', 9);  //Ko liên hệ được

define('STATUS_INACTIVE', 0);
define('STATUS_ACTIVE', 1);

/** Các nhóm ảnh KS **/
define('IMAGE_MAIN', 1);  //Ảnh toàn cảnh, cảnh quan
define('IMAGE_ROOM', 2);
define('IMAGE_EXTERIOR', 3);  //Ảnh toàn cảnh, cảnh quan
define('IMAGE_POOL', 4);
define('IMAGE_RECEPTION', 5); //Sảnh chính, lễ tân
define('IMAGE_RESTAURANT', 6);
define('IMAGE_AMENITY', 7); //Các tiện nghi, dịch vụ
define('IMAGE_COMMON', 8);  //Khu vực chung
define('IMAGE_OUTDOOR', 9); //Khu vực ngoài trời
define('IMAGE_ACTIVITY', 10);   //Hoạt động

/** Các nhóm phụ thu */
define('SURCHARGE_ALL', 1);  // Tất cả
define('SURCHARGE_CI_CO', 2);  // Nhóm checkin checkout

/** Các trường default */
define('COMPANY_SENNET', 1);
define('USER_SENNET', 1); 

/** Các kênh OTA */
define('BOOKING_COM', 1);
define('AGODA', 2);
define('TRIPADVISOR', 3);
define('TRAVELOKA', 4);
define('AIRBNB', 5);
define('EXPEDIA', 6);
define('HOTEL_COM', 7);
define('IVIVU', 8);
define('TRIP_COM', 9);
define('LUXSTAY', 10);
define('CTRIP', 11);
define('MYTOUR', 12);
define('HOTELBEDS', 13);
define('GO2JOY', 14);

/** Các loại giá mặc định */
define('PRICE_RAC', 1); // Giá công bố
define('PRICE_TA', 2); // Giá TA
define('PRICE_OTA', 3); // Giá OTA
define('PRICE_WALK_IN', 4); // Giá WALK-IN
define('PRICE_CORP', 5); // Giá Corp

/** Các loại tài sản */
 define('ASSET_ROOM', 1); // Tài sản phòng
 define('ASSET_RESTAUNT', 2); // Tài sản nhà hàng   
 define('ASSET_PHYSICAL', 3); // Tài sản cơ sở vật chất

 /** Các nhóm tài sản */
define('ASSET_INTANGIBLE', 1);    // Vô hình
define('ASSET_TANGIBLE', 2);    // Hữu hình

/** Các loại cấu hình mã màu */
define('COLOR_ROOM_EMPTY', 1);    // Màu phòng trống
define('COLOR_ROOM_CHECKIN_TODAY', 2);    // Màu phòng checkin hôm nay  
define('COLOR_ROOM_STAY', 3);    // Màu phòng đang ở
define('COLOR_ROOM_NO_SHOW', 4);    // Màu phòng không đến
define('COLOR_ROOM_CLEAN', 5);    // Màu phòng đang dọn
define('COLOR_ROOM_CHECK', 6);    // Màu kiểm phòng
define('COLOR_ROOM_BROKEN', 7);    // Màu phòng hỏng
define('COLOR_ROOM_INSPECTION', 8);    // Màu phòng kiểm tra
define('COLOR_ROOM_OUT', 9);    // Màu phòng ra ngoài



?>