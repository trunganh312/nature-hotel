<?
include('config_module.php');
$Auth->checkPermission('mkt_voucher_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Mã giảm giá';
$has_edit   =   $Auth->hasPermission('mkt_voucher_edit');
$has_view_expire    =   $Auth->hasPermission('mkt_view_all_expire');
$field_id   =   'vou_id';
/** --- End of Khai báo một số biến cơ bản --- **/

//Mảng chứa kiểu voucher
$vou_type   =   $cms_cfg_voucher_type;
$vou_call_reminder  =   [0 => 'Chưa gọi', 1 => 'Đã gọi'];

/** --- DataTable --- **/
$Table  =   new DataTable('voucher', $field_id);
$Table->column('vou_code', 'Mã Voucher', TAB_TEXT, true)
        ->column('vou_type', 'Kiểu giảm', TAB_SELECT, true)
        ->column('vou_value', 'Giá trị giảm', TAB_NUMBER, false, true)
        ->column('vou_quantity', 'S/Lượng', TAB_NUMBER, false, true)
        ->column('vou_time_used', 'Đã S/Dụng', TAB_NUMBER, false, true)
        ->column('vou_time_expire', 'Hạn sử dụng', TAB_DATE, false, true)
        ->column('vou_time_create', 'Ngày tạo', TAB_DATE, false, true)
        ->column('vou_booking_id', 'Booking', TAB_TEXT)
        ->column('vou_description', 'Mô tả', TAB_TEXT, true);
if ($has_edit) {
    $Table->column('vou_active', 'Act', TAB_CHECKBOX, false, true)
        ->addED(true);
}
$Table->addSearchData(
    [
    'booking'           =>  ['label' => 'Mã Booking', 'type' => TAB_TEXT],
    'vou_call_reminder' =>  ['label' => 'Nhắc sử dụng', 'type' => TAB_SELECT, 'query' => true]
    ]
);
$Table->setEditFileName('edit_voucher.php')
        ->setEditThickbox(['width' => 800, 'height' => 600, 'title' => 'Sửa']);

//Câu SQL
$sql_where = $sql_join = "";
$status =   getValue('status', GET_STRING, GET_GET, '');
if ($status == 'expire') {
    $sql_where  =   " AND vou_call_reminder = 0 AND vou_booking_id > 0 AND vou_time_used < vou_quantity
                    AND vou_time_expire < " . (CURRENT_TIME + NUMBER_DAY_VOUCHER_REMINDER * 86400);
    //Set phân trang to ra để continue ko bị ngắt
    $Table->setPageSize(5000);
    $page_title .=  ' sắp hết hạn';
}

//Tìm theo mã booking
$code   =   getValue('booking', GET_STRING, GET_GET, '', 1);
if (!empty($code)) {
    //Lấy chữ cái đầu tiên để xem là đơn gì thì join với bảng tương ứng
    $first  =   strtoupper(substr($code, 0, 1));
    switch ($first) {
        case 'H':
            $sql_join   .=  " INNER JOIN booking_hotel ON vou_booking_id = bkho_id";
            $sql_where  .=  " AND vou_booking_group = " . GROUP_HOTEL . " AND bkho_code LIKE '%$code%'";
        break;
        case 'T':
            $sql_join   .=  " INNER JOIN booking_tour ON vou_booking_id = bkto_id";
            $sql_where  .=  " AND vou_booking_group = " . GROUP_TOUR . " AND bkto_code LIKE '%$code%'";
        break;
        case 'V':
            $sql_join   .=  " INNER JOIN booking_ticket ON vou_booking_id = bkti_id";
            $sql_where  .=  " AND vou_booking_group = " . GROUP_TICKET . " AND bkti_code LIKE '%$code%'";
        break;
    }
    
}

$Table->addSQL("SELECT *
                FROM voucher $sql_join
                WHERE 1" . $sql_where);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
    <style>
        #vou_call_reminder {
            width: 150px;
        }
    </style>
</head>
<body>
    <?
    if ($has_edit)  $Layout->setTitleButton('<a href="create_voucher.php?TB_iframe=true&width=800&height=600" class="thickbox" title="Thêm mới voucher"><i class="fas fa-plus-circle"></i> Thêm mới</a>');
    $Layout->header($page_title);
    ?>
    <?=$Table->createTable()?>
    <?
    //Hiển thị data
    $data   =   $DB->query($Table->sql_table)->toArray();
    $stt    =   0;
    
    //Lấy list Staff của Admin để ẩn đi các voucher ko phải của mình
    $list_staff =   $Auth->getAllStaffAdmin($Auth->id);
    
    foreach ($data as $row) {
        // Lấy booking code
        $bk_code    =   '';
        $href       =   '';
        if ($row['vou_booking_id'] > 0) {
            if ($row['vou_booking_group'] == GROUP_HOTEL) {
                $booking_info   =   $DB->query("SELECT bkho_code AS code, adm_id, adm_name
                                                FROM booking_hotel
                                                INNER JOIN admin ON bkho_admin_process = adm_id
                                                WHERE bkho_id = " . $row['vou_booking_id'])
                                                ->getOne();
                $href   =   '/module/booking_hotel/create.php';
            } else if ($row['vou_booking_group'] == GROUP_TOUR) {
                $booking_info   =   $DB->query("SELECT bkto_code AS code, adm_id, adm_name
                                                FROM booking_tour
                                                INNER JOIN admin ON bkto_admin_process = adm_id
                                                WHERE bkto_id = " . $row['vou_booking_id'])
                                                ->getOne();
                $href   =   '/module/booking_tour/edit.php';
            } else if ($row['vou_booking_group'] == GROUP_TICKET) {
                $booking_info   =   $DB->query("SELECT bkti_code AS code, adm_id, adm_name
                                                FROM booking_ticket
                                                INNER JOIN admin ON bkti_admin_process = adm_id
                                                WHERE bkti_id = " . $row['vou_booking_id'])
                                                ->getOne();
                $href   =   '/module/booking_ticket/edit.php';
            }
            $bk_code    =   $booking_info['code'];
            //Nếu là list reminder mà ko phải bk của mình thì continue
            if ($status == 'expire' && !$Auth->boss && !in_array($booking_info['adm_id'], $list_staff) && !$has_view_expire)   continue;
        } else {
            if (isset($booking_info)) unset($booking_info);
        }
        
        $Table->setRowData($row);
        $stt++;
        ?>
        <?=$Table->createTR($stt, $row[$field_id]);?>
        <?=$Table->showFieldText('vou_code')?>
        <?=$Table->showFieldArray('vou_type')?>
        <?=$Table->showFieldNumber('vou_value')?>
        <?=$Table->showFieldNumber('vou_quantity')?>
        <?=$Table->showFieldNumber('vou_time_used')?>
        <?=$Table->showFieldDate('vou_time_expire')?>
        <?=$Table->showFieldDate('vou_time_create')?>
        <td class="text-center">
            <?
            if (!empty($bk_code)) {
                echo    '<p><a class="thickbox" title="Chi tiết đơn ' . $bk_code . '" href="' . $href . '?id=' . $row['vou_booking_id'] . '&TB_iframe=true&width=1000&height=600">' . $bk_code . '</a></p>';
                
                if ($row['vou_call_reminder'] == 1) {
                    echo    '<p><a class="thickbox badge bg-warning" title="Xem phản hồi của khách khi mời sử dụng voucher" href="call_reminder.php?id=' . $row['vou_id'] . '&TB_iframe=true&width=600&height=400">Xem ý kiến KH</a></p>';
                } else {
                    if ($status == 'expire') {
                        echo    '<p><a class="thickbox badge bg-warning" title="Gọi nhắc khách sử dụng voucher" href="call_reminder.php?id=' . $row['vou_id'] . '&TB_iframe=true&width=800&height=500">Gọi nhắc khách</a></p>';
                    }
                }
            }
            ?>
        </td>
        <td class="text-center">
            <p><?=$row['vou_description']?></p>
            <?=(!empty($booking_info['adm_name']) ? '<p>(' . $booking_info['adm_name'] . ')</p>' : '')?>
        </td>
        <?
        if ($has_edit)  echo    $Table->showFieldCheckbox('vou_active');
        ?>
        <?=$Table->closeTR($row[$field_id]);?>
        <?
    }
    ?>
    <?=$Table->closeTable();?>
    <?
    $Layout->footer();
    ?>
</body>