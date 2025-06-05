<?
include('config_module.php');
$Auth->checkPermission('mkt_campaign_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Danh sách Campaign';
$table      =   'campaign';
$field_id   =   'cam_id';

//Các biến sử dụng cho DS
$cam_source     =   $cfg_mkt_chanel;
$cam_account    =   $cfg_ads_account;
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('cam_source', 'Kênh', TAB_SELECT, true)
        ->column('cam_account', 'Ads Account', TAB_SELECT, true)
        ->column('cam_name', 'Tên campaign', TAB_TEXT, true)
        ->column('cam_description', 'Mô tả', TAB_TEXT);
if ($Auth->hasPermission('mkt_campaign_edit')) {
    $Table->addED(true)
            ->setEditFileName('edit_campaign.php')
            ->setEditThickbox(['width' => 600, 'height' => 400, 'title' => 'Sửa thông tin campaign']);
}

$Table->addSQL();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title);?>
</head>
<body class="sidebar-mini listing_page">
    <?
    if ($Auth->hasPermission('mkt_campaign_create'))  $Layout->setTitleButton('<a class="thickbox" href="create_campaign.php?TB_iframe=true&width=600&height=400" title="Thêm mới campaign"><i class="fas fa-plus-circle"></i> Thêm mới</a>');
    $Layout->header($page_title);
    ?>
    <?=$Table->showTableData()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>

