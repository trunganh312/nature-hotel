<?php

use src\Libs\Vue;
use src\Models\Company;
use src\Services\CommonService;

include('config_module.php');

//Check quyền
$Auth->checkPermission('hotel_list');
//Alias menu
$Auth->setAliasMenu('hotel_list');

/** --- Khai báo một số biến cơ bản --- **/
$table              =   'hotel';
$field_id           =   'hot_id';
$page_title         =   'Danh sách khách sạn';
$has_edit           =   $Auth->hasPermission('hotel_edit');
$has_change_owner   =   $Auth->hasPermission('hotel_change_owner');
$has_view_count     =   $Auth->hasPermission('hotel_view_count');  //Xem các thông tin lượt view, book...
$has_view_booking = $Auth->hasPermission('hms_booking_list_all'); // Quyền xem booking
$hot_type   =   $cfg_hotel_type;
$hot_star   =   $cfg_hotel_star;
$hot_city   =   $cfg_city;
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('hot_picture', 'Ảnh', TAB_IMAGE)
    ->column('hot_name', 'Khách sạn', TAB_TEXT, true)
    ->column('hot_type', 'Loại hình', TAB_SELECT, true)
    ->column('hot_star', 'Hạng sao', TAB_SELECT, true)
    ->column('hot_city', 'Tỉnh/TP', TAB_SELECT, true)
    ->column('hot_company_id', 'Chủ sở hữu', TAB_TEXT)
    ->column('hot_time_create', 'Ngày tạo', TAB_DATE);
if ($has_view_count) {
    $Table->column('hot_count_view', 'View', TAB_NUMBER, false, true)
        ->column('hot_count_booking', 'Book', TAB_NUMBER, false, true);
}
if(getValue('pageSize')) {
    $Table->setPageSize(getValue('pageSize'));
}

if ($has_edit) {
    $Table->column('hot_top', 'Top', TAB_CHECKBOX, false, true)
        ->column('hot_active', 'Act', TAB_CHECKBOX, false, true)
        ->addED(true);
}

$Table->setPathImage(DOMAIN_STATIC . '/hotel/')
    ->addSQL();

$data   =   $DB->query($Table->sql_table)->toArray();

// Lặp qua trả về đúng kiểu dữ liệu
$data = array_map(function($item) use ($hot_city, $hot_star, $hot_type, $Router) {
    $item['hot_type_text']      = array_get($hot_type, $item['hot_type']);
    $item['hot_city']           = array_get($hot_city, $item['hot_city']);
    $item['hot_picture']        = $Router->srcHotel($item['hot_id'], $item['hot_picture'], SIZE_SMALL);
    $item['hot_time_create']    = date("d/m/Y", $item['hot_time_create']);
    $item['com_name']           = 'Chưa có sở hữu';
    return $item;
}, $data);

$res = [
    "rows" => $data,
    "pagination" => [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
    "params" => [],
    "others" => [],
    'permissions' => [
        'hasEdit' => $has_edit,
        'hasChangeOwner' => $has_change_owner,
        'hasViewCount' => $has_view_count,
        'hasViewBooking' => $has_view_booking,  
    ],
];

$res['others']['hot_type'] = [];
foreach ($hot_type as $k => $v) {
    $res['others']['hot_type'][] = [
        "value" => $k,
        "label" => $v
    ];
}

$res['others']['hot_city'] = [];
foreach ($hot_city as $k => $v) {
    $res['others']['hot_city'][] = [
        "value" => $k,
        "label" => $v
    ];
}


$res['others']['hot_star'] = [];
foreach ($hot_star as $k => $v) {
    $res['others']['hot_star'][] = [
        "value" => $k,
        "label" => $v
    ];
}

if(getValue('json')) {
    CommonService::resJson($res);
}


Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-hotel-list', 'admin');
