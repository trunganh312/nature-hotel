<?
include('config_module.php');
$Auth->checkPermission('config_website');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'configuration';
$field_id   =   'con_id';
$page_title =   'Cấu hình hệ thống';
$record_id	= 	1;
$record_info    =   $DB->query("SELECT *  FROM " . $table . " WHERE " . $field_id . " = ". $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('con_disable_email', DATA_INTEGER, 0)
    ->add('con_price_type_client', DATA_INTEGER, 0, 'Bạn chưa chọn loại giá hiển thị')
    //->add('con_price_type_capital', DATA_INTEGER, 0, 'Bạn chưa chọn loại giá vốn')
    ->add('con_price_type_promotion', DATA_INTEGER, 0, 'Bạn chưa chọn loại giá khuyến mại')
    ->add('con_header_html', DATA_INTEGER, 0)
    ->add('con_price_cache_month_enable', DATA_INTEGER, 0)
    ->add('con_key_map', DATA_STRING, '', 'Bạn chưa nhập key Google map')
    ->add('con_zalo_token_expired', DATA_INTEGER, 0)
    ->add('con_zalo_access_token', DATA_STRING, '')
    ->add('con_zalo_refresh_token', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

$Query->setRemoveHTML(false);

/** --- Submit form --- **/
if ($Query->submitForm()) {

    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        $DB->execute($Query->generateQueryUpdate($field_id, $record_id));
        
        /** Lưu log **/
        $data_new   =   $DB->query("SELECT * FROM configuration WHERE con_id = $record_id")->getOne();
        $Log->setTable('configuration')->genContent($record_info, $data_new)->createLog($record_id);
        
        set_session_toastr();
        redirect_url($_SERVER['REQUEST_URI']);
    }
    
}
/** --- End of Submit form --- **/

$arr_room_price_type = $Model->getListData('room_price_type', 'rpt_id, rpt_name', 'rpt_active = 1');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
    <style>
        .form-group .form_input .form-control {
            width: 70%;
        }
        #con_policy_cancel {
            width: 70%;
            height: 120px;
        }
    </style>
</head>
<body class="sidebar-mini listing_page">
    <?
    $Layout->header($page_title);
    ?>
	<?
    //Tạo ra các biến sẵn được lấy ra từ bản ghi này để fill vào form
    $Query->generateVariable($record_info);
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->error)?>
    <?=$Form->text('Key Google map', 'con_key_map', $con_key_map, true)?>
    <?=$Form->select('Loại giá hiển thị', 'con_price_type_client', $arr_room_price_type, $con_price_type_client, true)?>
    <?//=$Form->select('Loại giá vốn', 'con_price_type_capital', $arr_room_price_type, $con_price_type_capital, true)?>
    <?=$Form->select('Loại giá khuyến mại', 'con_price_type_promotion', $arr_room_price_type, $con_price_type_promotion, true)?>
    <?=$Form->checkbox('Disable gửi email', 'con_disable_email', $con_disable_email)?>
    <?=$Form->checkbox('Header dùng HTML tĩnh', 'con_header_html', $con_header_html)?>
    <?=$Form->checkbox('Sử dụng giá tương đối', 'con_price_cache_month_enable', $con_price_cache_month_enable)?>
    <?=$Form->textarea('Zalo access token', 'con_zalo_access_token', $con_zalo_access_token, false, 'Lấy token mới tại <a target="_tblank" href="https://developers.zalo.me/tools/explorer/3075836128630070920">tại đây</a>')?>
    <?=$Form->textarea('Zalo refresh token', 'con_zalo_refresh_token', $con_zalo_refresh_token)?>
    <?=$Form->number('Zalo token expired', 'con_zalo_token_expired', $con_zalo_token_expired, false, 'Mặc định nên để 80000s')?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>