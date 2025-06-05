<?
include('../../../Core/Config/require_web.php');
require_once($path_core . 'Classes/GenerateQuery.php');
require_once($path_core . 'Classes/GenerateForm.php');

//Nếu chưa login thì rediret luôn về trang chủ
if (!$User->logged) {
    redirect_url(DOMAIN_WEB);
}
        
/** Các biến config **/
//Thư mục up ảnh temp
$path_image_temp    =   '../../../../Vietgoing_CRM/image/temp_up/';

//Thư mục lưu ảnh của Tour
$path_image_tour    =   '../../../../Vietgoing_CRM/image/tour/';

//Resize ảnh
$array_resize   =   [
                    SIZE_LARGE  =>  ['maxwidth' => 1020, 'maxheight' => 750],
                    SIZE_MEDIUM =>  ['maxwidth' => 680, 'maxheight' => 500],
                    SIZE_SMALL  =>  ['maxwidth' => 340, 'maxheight' => 250]
                    ];

/** --- Menu sidebar --- **/
$array_menu_sidebar =   [
                        'info' => ['file' => 'info', 'label' => 'Cập nhật thông tin', 'icon' => 'fas fa-info'],
                        'booking' => ['file' => 'booking_list', 'label' => 'Đơn đã đặt', 'icon' => 'fas fa-calendar-alt'],
                        'voucher' => ['file' => 'voucher_list', 'label' => 'Mã giảm giá', 'icon' => 'fas fa-money'],
                        'referral'  =>  ['file' => 'referral', 'label' => 'Giới thiệu bạn bè', 'icon' => 'fas fa-users']
                        ];
/** --- Lấy module --- **/
$module =   getValue('mod', GET_STRING, GET_GET, 'info');
$popup  =   getValue('popup', GET_STRING, GET_GET, '');

if (isset($array_menu_sidebar[$module])) {
    
    $file_data  =   'data/data_' . $array_menu_sidebar[$module]['file'] . '.php';
    $file_view  =   'view/view_' . $array_menu_sidebar[$module]['file'] . '.php';
    $page_title =   $array_menu_sidebar[$module]['label'];
    
} else {
    
    //Mặc định sẽ là trang thông tin cá nhân
    $file_data  =   'data/data_info.php';
    $file_view  =   'view/view_info.php';
    $page_title =   'Thông tin cá nhân';
    
}

//Include file xử lý Data
include($file_data);

//Set các thông tin layout
$Layout->setIndex(false)
        ->setTitle($page_title)
        ->setReadCSS(false)
        ->setCSS(['profile'])
        ->setJS(['page.profile']);

?>