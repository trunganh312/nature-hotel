<?
include('config_module.php');
$Auth->checkPermission('mkt_ads_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Danh sách Ads';
$table      =   'advertising';
$field_id   =   'adv_id';

//Nếu có lọc theo source thì cho lọc thêm theo campaign
$source =   getValue('cam_source');

//Các biến sử dụng cho DS
$cam_source     =   $cfg_mkt_chanel;
$cam_account    =   $cfg_ads_account;
$adv_campaign   =   $Model->getListData('campaign', 'cam_id, cam_name', $source > 0 ? 'cam_source = ' . $source : '');
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('adv_name', 'Tên Ads', TAB_TEXT, true, true)
        ->column('cam_source', 'Kênh MKT', TAB_SELECT, true)
        ->column('cam_account', 'Ads Account', TAB_SELECT, true)
        ->column('adv_campaign', 'Campaign', TAB_SELECT, $source > 0 ? true: false)
        ->column('adv_url_utm', 'URL', TAB_TEXT)
        ->column('adv_count_visit', 'Visit', TAB_NUMBER, false, true)
        ->column('adv_count_bk', 'Booking', TAB_NUMBER, false, true)
        ->column('adv_count_bk_success', 'BK Success', TAB_NUMBER, false, true)
        ->column('adv_description', 'Mô tả', TAB_TEXT);
if ($Auth->hasPermission('mkt_ads_edit')) {
    $Table->addED(true)
            ->setEditFileName('edit_ads.php')
            ->setEditThickbox(['width' => 800, 'height' => 400, 'title' => 'Sửa thông tin Ads']);
}

$Table->addSQL("SELECT advertising.*, cam_source, cam_account
                FROM advertising
                INNER JOIN campaign ON adv_campaign = cam_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title);?>
    <style>
        #adv_campaign {
            width: 200px;
        }
    </style>
</head>
<body class="sidebar-mini listing_page">
    <?
    if ($Auth->hasPermission('mkt_ads_create'))  $Layout->setTitleButton('<a class="thickbox" href="create_ads.php?TB_iframe=true&width=800&height=400" title="Thêm mới Ads"><i class="fas fa-plus-circle"></i> Thêm mới</a>');
    $Layout->header($page_title);
    ?>
    <?=$Table->showTableData()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>

