<?
include('config_module.php');
$Auth->checkPermission('mkt_campaign_edit');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Sửa thông tin campaign';
$table      =   'campaign';
$field_id   =   'cam_id';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT *
                                FROM " . $table . "
                                WHERE " . $field_id . " = " . $record_id)
                                ->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

/** --- Class query để update dữ liệu --- **/
$Query  =   new GenerateQuery('campaign');
$Query->add('cam_source', DATA_INTEGER, 0, 'Bạn chưa chọn kênh MKT')
        ->add('cam_account', DATA_INTEGER, 0, 'Bạn chưa chọn tài khoản Ads')
        ->add('cam_name', DATA_STRING, '', 'Bạn chưa nhập tên campaign')
        ->add('cam_description', DATA_STRING, '')
        ->add('cam_last_update', DATA_INTEGER, CURRENT_TIME);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if ($Query->submitForm()) {
    
    //Trong cùng 1 account thì 2 campaign ko được trùng tên nhau
    $cam_name   =   getValue('cam_name', GET_STRING, GET_POST, '', 1);
    if ($DB->count("SELECT COUNT(cam_id) AS total FROM campaign WHERE cam_account = " . $record_info['cam_account'] . "
                    AND cam_name = '$cam_name' AND cam_id <> $record_id") > 0) {
        $Query->addError('Tên campaign này đã tồn tại trong tài khoản Ads bạn chọn');
    }
    
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
</head>
<body class="windows-thickbox">
    <?
    $Layout->setPopup(true)->header($page_title);
    ?>
	<?
    //Tạo ra các biến sẵn được lấy ra từ bản ghi này để fill vào form
    $Query->generateVariable($record_info);
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->error)?>
    <?=$Form->select('Kênh MKT', 'cam_source', $cfg_mkt_chanel, $cam_source, true)?>
    <?=$Form->select('Account', 'cam_account', $cfg_ads_account, $cam_account, true)?>
    <?=$Form->text('Tên campaign', 'cam_name', $cam_name, true)?>
    <?=$Form->textarea('Mô tả', 'cam_description', $cam_description)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>