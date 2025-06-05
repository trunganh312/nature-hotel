<?
require_once($path_core . 'Classes/DataTable.php');

$page_title     =   'Quản lý mã giảm giá';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable('voucher', 'vou_id');
$Table->column('vou_vode', 'Mã ', TAB_TEXT)
            ->column('vou_value', 'Giá trị giảm', TAB_NUMBER)
            ->column('vou_quantity', 'Số lượng', TAB_NUMBER)
            ->column('vou_time_expire', 'Hạn sử dụng', TAB_DATE)
            ->column('vou_time_create', 'Ngày tạo', TAB_DATE)
            ->column('vou_active', 'Trạng thái', TAB_CHECKBOX);
$Table->addSQL("SELECT voucher.* 
                FROM voucher
                LEFT JOIN booking_hotel ON vou_booking_id = bkho_id
                LEFT JOIN booking_tour ON vou_booking_id = bkto_id
                WHERE vou_user_id = {$User->id} OR bkho_user_id = {$User->id} OR bkto_user_id = {$User->id}
                ORDER BY vou_time_create DESC, vou_active DESC
            ");
?>