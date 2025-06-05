<?
/** --- Các biến chứa các giá trị của constant để dùng trong class Log generate ra log content --- **/

/** --- Ko được sửa tên các biến này vì nó được lưu dạng text trong bảng field_table --- **/

//Các trạng thái xử lý đặt dich vụ với partner
$var_status_partner =   $cfg_status_partner;

//Các trạng thái xử lý đơn
$var_booking_status =   [
                        STT_NEW         =>  'Mới',
                        STT_PROCESS     =>  'Đang xử lý',
                        STT_HOLD        =>  'Tạm khóa',
                        STT_SUCCESS     =>  'Thành công',
                        STT_COMPLETE   =>  'Hoàn thành',
                        STT_CANCEL      =>  'Hủy'
                        ];

//Tình trạng gửi email
$var_send_email =   [
    0   =>  'Chưa gửi',
    1   =>  'Đã gửi'
];

//Các trường dạng checkbox
$var_checkbox =   [
    0   =>  'No',
    1   =>  'Yes'
];
?>