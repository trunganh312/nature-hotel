<?php
include('../../Core/Config/require_web.php');
require_once($path_core . 'Classes/Calendar.php');

/** --- Khai báo một số biến cơ bản --- **/
$table          =   'room';
$field_id       =   'roo_id';
$record_id      =   getValue("id");
$price_type     =   getValue("price_type");
$ajax           =   getValue("ajax");
$date           =   getValue("date", 'str', 'GET');
$record_info    =   $Model->getRecordInfo($table, $field_id, $record_id);
if(empty($record_info) || !$User->isVGStaff()) {
    exit('Dữ liệu này không tồn tại!');
}
$price_type     =   CON_PRICE_TYPE_CAPITAL;
/** --- End of Khai báo một số biến cơ bản --- **/

// Lấy danh sách giá
$hotel_id   = $record_info['roo_hotel_id'];
$prices     = [];
$tables     = [];
$timestamp_view = $time_checkin;
$date_view   		= date('ym', $timestamp_view);

// Lấy ra tất cả table chứa data của ks
$partition  = ($hotel_id%CFG_PARTITION_TABLE)+1;
$result     = $DB->query("SHOW TABLES FROM ". ENV_DB_DBNAME ." LIKE 'room_price%';")->toArray();
foreach($result as $v) {
    foreach ($v as $v) {
        if(preg_match("/^room_price_{$date_view}{$partition}$/", $v)) {
            $tables[] = $v;
        }
    }
}

// Lấy ra data price
foreach ($tables as $v) {
    $prices = array_merge($prices, $DB->query("SELECT * FROM {$v} WHERE rop_hotel_id = {$hotel_id} AND rop_room_id = {$record_id} AND rop_type = {$price_type};")->toArray());
}
unset($tables);

// Gộp giá vào ngày
$list_price =   [];
$list_month =   [];
foreach ($prices as $item) {
    if(empty($list_price[$item["rop_day"]])) $list_price[$item["rop_day"]] = [];
    $list_price[$item["rop_day"]][] =   $item;
    // Lấy danh sách tháng
    $temp = date('Y', $item["rop_day"]);
    if(empty($list_month[$temp][0])) {
        $list_month[$temp][0]   =   date('Y', $item["rop_day"]);
    }
    if(in_array(date('m', $item["rop_day"]), $list_month[$temp])) continue;
    $list_month[$temp][]    =   date('m', $item["rop_day"]);
}
unset($prices);
ksort($list_month);

$list_month =   array_values($list_month);
$calendar   =   new Calendar($list_price, true);

// Nếu là gọi ajax thì chỉ show lịch
// if($ajax) {
//     $date_value     = explode('-', $date);
//     $_GET['year']   = $date_value[0];
//     $_GET['month']  = $date_value[1];
//     echo $calendar->show();
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
		<?=$Layout->loadHead()?>
		<link rel="stylesheet" href="<?=DOMAIN_CRM ?>/theme/css/price.css" />
    <style>
    	div#calendar ul.label li{
		    color: #000;
		    font-size: 15px;
    	}
    </style>
</head>
<body>
    <div class="manage-price">
        <!-- <div class="form-group">
            <label style="width: 185px;">Chọn tháng để xem giá:</label>
            <div class="form_inputt">
                <select style="width: 245px;" class="form-control select-month">
                    <?=generate_optgroup_month() ?>
                </select>
            </div>
        </div> -->
        <h4 class="text-center">Giá nội bộ tháng <?=date("m/Y", $timestamp_view) ?> phòng <?=$record_info["roo_name"] ?></h4>
        <div class="calendar">
            <div class="loadding"></div>
            <?=$calendar->show() ?>
        </div>
    </div>
    <?=$Layout->loadFooter()?>
    <script type="text/javascript">
    $(document).ready(function() {
        // Các thông tin call api
        // const room_id  = <?=$record_id ?>;
        // const hotel_id = <?=$record_info["roo_hotel_id"] ?>;
        // const base_url = '<?=base_url() ?>';

        // function run_waitMe(){
        //     $('.main_content').waitMe('init');
        // }
        // function stop_waitMe() {
        //     $('.main_content').waitMe('hide');
        // }
          
        // // Load cleandar
        // function load_cleander() {
        //     let val_time    =   $('.select-month').val();
        //     let val_type    =   $('#price-type').val();

        //     $('.manage-price .calendar').load(`price.php?ajax=1&date=${val_time}&id=${room_id}&price_type=${val_type}`);
        // }

        // //Lấy tháng mới 
        // $('.manage-price').on('change', '.select-month',function() {
        //     load_cleander();
        // });

        // Hoạt động khi bị nhúng làm iframe, sẽ gửi height hiện tại sang cho page parent
        setTimeout(function() {
            window.parent.postMessage($(document).outerHeight() + 5, '*');
        }, 300);
    });
    </script>
</body>
</html>
