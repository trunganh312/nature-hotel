<?
include('config_module.php');
$Auth->checkPermission('mkt_ads_edit');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Sửa thông tin Ads';
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

/** --- Class query để update dữ liệu --- **/
$Query  =   new GenerateQuery('advertising');
$Query->add('adv_campaign', DATA_INTEGER, 0, 'Bạn chưa chọn Campaign')
        ->add('adv_name', DATA_STRING, '', 'Bạn chưa nhập tên Ads')
        ->add('adv_url', DATA_STRING, '', 'Bạn chưa nhập URL')
        ->add('adv_description', DATA_STRING, '')
        ->add('adv_last_update', DATA_INTEGER, CURRENT_TIME);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if ($Query->submitForm()) {
    
    //Check trùng tên
    $name   =   getValue('adv_name', GET_STRING, GET_POST, '', 1);
    if ($DB->count("SELECT COUNT(adv_id) AS total FROM advertising
                    WHERE adv_campaign = " . $record_info['adv_campaign'] . " AND adv_name = '$name'
                        AND adv_id <> $record_id") > 0) {
        $Query->addError('Tên Ads này đã tồn tại trong Campaign');
    }
    
    
    //Update URL UTM            
    $url    =   getValue('adv_url', GET_STRING, GET_POST, '');
    $Query->add('adv_url_utm', DATA_STRING, $url . get_url_symbol_query($url) . PARAM_ADS . '=' . $record_id);
    
    //Kiểm tra lỗi
    if ($Query->validate($field_id, $record_id)) {
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            set_session_toastr();
            reload_parent_window();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
        }
        
    }
}
/** --- End of Submit form --- **/

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
    <style>
        #adv_url {
            width: 100%;
        }
    </style>
</head>
<body class="windows-thickbox">
    <?
    $Layout->setPopup(true)->header($page_title);
    ?>
	<?
    //Tạo ra các biến sẵn được lấy ra từ bản ghi này để fill vào form
    $Query->generateVariable($record_info);
    
    $campaigns  =   $Model->getListData('campaign', 'cam_id, cam_name', 'cam_account = ' . $cam_account, 'cam_name');
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->error)?>
    <?=$Form->textHTML('Kênh MKT', $cfg_mkt_chanel[$cam_source])?>
    <?=$Form->select('Ads Account', 'account', $cfg_ads_account, $cam_account, true)?>
    <?=$Form->select('Campaign', 'adv_campaign', $campaigns, $adv_campaign, true)?>
    <?=$Form->text('Tên Ads', 'adv_name', $adv_name, true)?>
    <?=$Form->text('URL', 'adv_url', $adv_url, true)?>
    <?=$Form->textarea('Mô tả', 'adv_description', $adv_description)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
    <script>
        $("#account").change(function () {
            $("#adv_campaign").load(
                "/module/common/get_select_child.php?type=campaign&id=" + $(this).val()
            );
        });
    </script>
</body>
</html>