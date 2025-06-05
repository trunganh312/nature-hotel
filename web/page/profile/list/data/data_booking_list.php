<?
require_once($path_core . 'Classes/DataTable.php');

$BookingModel   =   new BookingModel;
$page_title     =   'Quản lý đơn đã đặt';
/** --- End of Khai báo một số biến cơ bản --- **/

/** Các trạng thái của đơn **/
$arr_status =   $BookingModel->getListStatusLabel();
$arr_status[-1]  =   'Tất cả các đơn';

/** Lấy đồng thời tất cả các loại đơn ra hiển thị ở 1 list chung **/
$arr_sql    =   [];

//Lấy đơn theo từng DS
$status =   getValue('status', GET_INT, GET_GET, -1);
$code   =   getValue('code', GET_STRING, GET_GET, '', 1);
$object =   getValue('object', GET_STRING, GET_GET, '', 1);
$date   =   getValue('date', GET_STRING, GET_GET, '');

foreach ($cfg_table_booking as $table => $prefix) {
    
    //Câu SQL lọc
    $sql    =   "";
    
    //Tên của tour, hote và các câu join tương ứng
    $name = $join = "";
    switch ($table) {
        case 'booking_tour':
            $name   =   "tou_id AS object_id, tou_name AS object_name, tou_group AS object_cate";
            $join   =   "INNER JOIN tour ON bkto_tour_id = tou_id";
            if ($object != '') {
                $sql    .=  " AND tou_name LIKE '%" . $object . "%'";
            }
            break;
        case 'booking_hotel':
            $name   =   "hot_id AS object_id, hot_name AS object_name, 'hotel' AS object_cate";
            $join   =   "INNER JOIN hotel ON bkho_hotel_id = hot_id";
            if ($object != '') {
                $sql    .=  " AND hot_name LIKE '%" . $object . "%'";
            }
            break;
        case 'booking_ticket':
            $name   =   "tic_id AS object_id, tic_name AS object_name, 'ticket' AS object_cate";
            $join   =   "INNER JOIN ticket ON bkti_ticket_id = tic_id";
            if ($object != '') {
                $sql    .=  " AND tic_name LIKE '%" . $object . "%'";
            }
            break;
    }
    //Lọc theo trạng thái
    if ($status != -1) {
        $sql    .=  " AND " . $prefix . "status = " . $status;
        if (isset($arr_status[$status]))    $page_title =   $arr_status[$status];
    }
    
    //Lọc theo code
    if ($code != '') {
        $sql    .=  " AND " . $prefix . "code LIKE '%" . $code . "%'";
    }
    
    //Lọc theo ngày checkin
    if ($date != '') {
        $time_range =   generate_time_from_date_range($date);
        $checkin    =   $time_range['from'];
        $checkout   =   $time_range['to'];
        if ($checkin > 0 && $checkout > $checkin) {
            $sql    .=  " AND " . $prefix . "checkin BETWEEN $checkin AND $checkout";
        }
    }
    
    $arr_sql[]  =   "(SELECT " . $prefix . "id AS id, $name,
                    '$table' AS group_bk, " . $prefix . "code AS code, " . $prefix . "status AS status, " . $prefix . "time_create AS time_create,
                    " . $prefix . "checkin AS checkin,
                    " . $prefix . "money_pay AS money_pay, " . $prefix . "money_paid AS money_paid
                    FROM $table
                    $join
                    WHERE " . $prefix . "user_id = " . $User->id . " $sql)";
}

/** --- DataTable --- **/
$Table  =   new DataTable('booking', 'id');
$Table->column('code', 'Mã đơn', TAB_TEXT, true)
        ->column('object', 'Thông tin đặt', TAB_TEXT, true)
        //->column('adult', 'Số người', TAB_TEXT)
        ->column('money_pay', 'Số tiền (VNĐ)', TAB_NUMBER);
$Table->setShowTotalRecord(false);
$Table->addSearchData([
                        'date'  =>  ['label' => 'Ngày nhận phòng', 'type' => TAB_DATE, 'query' => false]
                        ]);
$Table->getSQLSearch();
$Table->setFieldHidden(['mod', 'status']);
?>