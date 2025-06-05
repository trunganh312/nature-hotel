<?
include('config_module.php');
$Auth->checkPermission('mkt_ads_view');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Xem thông tin Ads';
$table      =   'advertising';
$field_id   =   'adv_id';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT " . $table . ".*, cam_source, cam_name, cam_account
                                FROM " . $table . "
                                INNER JOIN campaign ON adv_campaign = cam_id
                                WHERE " . $field_id . " = " . $record_id)
                                ->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
</head>
<body class="windows-thickbox">
    <?
    $Layout->setPopup(true)->header($page_title);
    ?>
	<?
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->textHTML('Kênh MKT', $cfg_mkt_chanel[$record_info['cam_source']])?>
    <?=$Form->textHTML('Tài khoản', $cfg_ads_account[$record_info['cam_account']])?>
    <?=$Form->textHTML('Campaign', $record_info['cam_name'])?>
    <?=$Form->textHTML('Tên Ads', $record_info['adv_name'])?>
    <?=$Form->textHTML('URL', '<a href="' . $record_info['adv_url'] . '" target="_blank">' . $record_info['adv_url'] . '</a>')?>
    <?=$Form->textHTML('Mô tả', $record_info['adv_description'])?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>