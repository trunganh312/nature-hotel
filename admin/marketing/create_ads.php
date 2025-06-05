<?
include('config_module.php');
$Auth->checkPermission('mkt_ads_create');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Thêm mới Ads';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('advertising');
$Query->add('adv_campaign', DATA_INTEGER, 0, 'Bạn chưa chọn Campaign')
        ->add('adv_name', DATA_STRING, '', 'Bạn chưa nhập tên Ads')
        ->add('adv_url', DATA_STRING, '', 'Bạn chưa nhập URL')
        ->add('adv_description', DATA_STRING, '')
        ->add('adv_time_create', DATA_INTEGER, CURRENT_TIME)
        ->add('adv_last_update', DATA_INTEGER, CURRENT_TIME)
        ->add('adv_admin_create', DATA_INTEGER, $Auth->id);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if ($Query->submitForm()) {
    
    $camp   =   getValue('adv_campaign', GET_INT, GET_POST);
    $name   =   getValue('adv_name', GET_STRING, GET_POST, '', 1);
    if ($DB->count("SELECT COUNT(adv_id) AS total FROM advertising WHERE adv_campaign = $camp AND adv_name = '$name'") > 0) {
        $Query->addError('Tên Ads này đã tồn tại trong Campaign');
    }
    
    //Validate form
    if ($Query->validate()) {
        
        //Lấy ID của advertising để gán URL UTM theo ID
        $adv_id =   $DB->executeReturn($Query->generateQueryInsert());
        
        if ($adv_id > 0) {
                
            $url    =   getValue('adv_url', GET_STRING, GET_POST, '');
            
            if ($DB->execute("UPDATE advertising SET adv_url_utm = '" . $url . get_url_symbol_query($url) . PARAM_ADS . "=$adv_id' WHERE adv_id = $adv_id LIMIT 1") > 0) {
                set_session_toastr();
                reload_parent_window();
            } else {
                $Query->addError('Đã có lỗi khi update URL UTM, vui lòng liên hệ báo cho Admin');
            }
            
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
    //Tạo ra các biến sẵn để fill vào form dựa vào các hàm add trường dữ liệu ở trên (GenerateQuery)
    $Query->generateVariable();
    
    $account    =   getValue('account', GET_INT, GET_POST);
    $campaigns  =   $Model->getListData('campaign', 'cam_id, cam_name', 'cam_account = ' . $account, 'cam_name');
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->error)?>
    <?//=$Form->select('Kênh MKT', 'source', $cfg_mkt_chanel, $source, true)?>
    <?=$Form->select('Ads Account', 'account', $cfg_ads_account, $account, true)?>
    <?=$Form->select('Campaign', 'adv_campaign', $campaigns, $adv_campaign, true)?>
    <?=$Form->text('Tên Ads', 'adv_name', $adv_name, true)?>
    <?=$Form->text('URL', 'adv_url', $adv_url, true)?>
    <?=$Form->textarea('Mô tả', 'adv_description', $adv_description)?>
    <?=$Form->button('Thêm mới')?>
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
