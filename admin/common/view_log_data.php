<?

use src\Services\CommonService;

include('config_module.php');

/** --- Khai báo một số biến cơ bản --- **/
$page_title     =   'Lịch sử thay đổi dữ liệu';
$record_id      =   getValue('id');
$table          =   getValue('table');
$view_full      =   getValue('vfull', GET_STRING, GET_GET, '');

//Lấy ra tên bảng được lưu ở bảng table_log
$record_info    =   $DB->query("SELECT talo_table
                                FROM table_log
                                WHERE talo_id = $table")
                                ->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu không tồn tại!');
}

//Check quyền
switch ($record_info['talo_table']) {
    case 'company':
        $Auth->checkPermission('hrm_company_history');
        break;

    case 'company_config_permission':
        $Auth->checkPermission('hrm_config_permission_history');
        break;
    
    case 'customer':
        $Auth->checkPermission('hrm_customer_history');
        break;

    case 'customer_request':
        $Auth->checkPermission('hrm_request_history');
        break;

    case 'hotel':
        $Auth->checkPermission('hms_hotel_history');
        break;
    
    case 'hotel_price_type':
        $Auth->checkPermission('hmr_hotel_price_type_history');
        break;

    case 'hotel_service':
        $Auth->checkPermission('hmr_hotel_service_history');
        break;
        
    case 'hotel_surcharge':
        $Auth->checkPermission('hmr_hotel_surcharge_history');
        break;

    case 'room':
        $Auth->checkPermission('hrm_room_history');
        break;
    
    case 'users':
        $Auth->checkPermission('hrm_users_history');
        break;

    case 'admins':
        $Auth->checkPermission('hrm_users_history');
        break;

    case 'users_department':
        $Auth->checkPermission('hrm_department_history');
        break;

    case 'users_group':
        $Auth->checkPermission('hrm_group_history');
        break;

    case 'room_type':
        $Auth->checkPermission('hotel_room_type_history');
        break;
    
    default:
        exitError('Bạn không có quyền sử dụng tính năng này!');
        break;
}

/** --- End of Khai báo một số biến cơ bản --- **/

//Mặc định là xem trong 1 tháng trở lại
if (!isset($_GET['date_range'])) {
    $_GET['date_range'] =   date('d/m/Y', CURRENT_TIME - 30 * 86400) . ' - ' . date('d/m/Y');
}

/** --- DataTable --- **/
$Table  =   new DataTable('table', 'id');
$Table->column('date', 'Ngày')
        ->column('staff', 'Tài khoản')
        ->column('content', 'Nội dung');

if ($Auth->cto && $view_full == 'full') {
    $Table->column('ip', 'IP')
            ->column('data_old', 'Dữ liệu trước')
            ->column('data_new', 'Dữ liệu sau')
            ->column('url', 'URL');
}
$Table->setShowSTT(false);
$Table->setShowTotalRecord(false);
$Table->setFieldHidden(['table', 'id', 'vfull']);

$type_log   =   [
                LOG_CREATE  =>  'Tạo mới',
                LOG_VIEW    =>  'Xem dữ liệu',
                LOG_UPDATE  =>  'Cập nhật'
                ];

$Table->addSearchData([
                        'date_range'    =>  ['label' => 'Thời gian', 'type' => TAB_DATE, 'query' => false],
                        'type_log'      =>  ['label' => 'Kiểu', 'type' => TAB_SELECT, 'query' => false]
                        ]);

//Kiểu log cần xem
$type       =   getValue('type_log');
$date_range =   getValue('date_range', GET_STRING, GET_GET, '');

//Lấy log
$data   =   $Log->getLog($table, $record_id, $date_range, $type);
$Table->getSQLSearch();

// Lặp qua để trả về đúng dữ liệu
$data = array_map(function ($row, $index) {
    return [
        'stt' => $index + 1,
        'log_time' => date('d/m/Y H:i:s', $row['log_time']),
        'user_name' => $row['user_name'],
        'log_content' => $row['log_content']
    ];
}, $data, array_keys($data));
if(CommonService::isGet()) {
    $res = [
        'rows' => $data,
        'others' => [
        ]
    ];

    $res['others']['types'] = [];
    foreach ($type_log as $k => $v) {
     $res['others']['types'][] = [
          "value"=> $k,
          "label"=> $v
     ];
    }
    
    CommonService::resJson($res);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title);?>
</head>
<body class="windows-thickbox listing_page">
    <?
    $Layout->setPopup(true)->header($page_title);
    ?>
    <?=$Table->createTable()?>
    <?
    usort($data, function($a, $b) {
        return $b['log_time'] <=> $a['log_time'];
    });
    
    $stt    =   0;
    foreach ($data as $row) {
        $stt++;
        ?>
        <?=$Table->createTR($stt)?>
        <td><?=date('d/m/Y H:i:s', $row['log_time'])?></td>
        <td><?=$row['user_name']?></td>
        <td><?=$row['log_content']?></td>
        <?
        if ($Auth->cto && $view_full == 'full') {
            ?>
            <td><?=$row['log_ip']?></td>
            <td>
                <pre>
                    <?                    
                    print_r(json_decode(restruct_json_encoded($row['log_data_old']), true));
                    ?>
                </pre>
            </td>
            <td>
                <pre>
                    <?
                    print_r(json_decode(restruct_json_encoded($row['log_data_new']), true));
                    ?>
                </pre>
            </td>
            <td><?=$row['log_url']?></td>
            <?
        }
        ?>
        <?=$Table->closeTR()?>
        <?
    }
    ?>
    <?=$Table->closeTable();?>
    <?
    $Layout->footer();
    ?>
</body>
</html>