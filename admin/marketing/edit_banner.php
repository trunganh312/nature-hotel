<?
include('config_module.php');
$Auth->checkPermission('banner_edit');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'banner';
$field_id   =   'ban_id';
$page_title =   'Sửa thông tin banner';
$url_return =   base64_decode(getValue('url', 'str', 'GET', base64_encode('list_banner.php')));
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('ban_position', DATA_INTEGER, 0, 'Bạn chưa chọn vị trí')
        ->add('ban_name', DATA_STRING, '', 'Bạn chưa nhập tên banner', 'Tên banner này đã tồn tại')
        ->add('ban_title', DATA_STRING, '', 'Bạn chưa nhập tiêu đề hiển thị')
        ->add('ban_description', DATA_STRING, '', 'Bạn chưa nhập mô tả')
        ->add('ban_link', DATA_STRING, '')
        ->add('ban_icon', DATA_STRING, '')
        ->add('ban_label', DATA_STRING, '')
        ->add('ban_text_link', DATA_STRING, '')
        ->add('ban_order', DATA_INTEGER, 0)
        ->add('ban_time_update', DATA_INTEGER, CURRENT_TIME);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if ($Query->submitForm()) {
    
    //Ảnh đại diện
    $Upload =   new Upload('ban_image', $path_upload_banner, 0, 0, '');
    $ban_image  =   $Upload->new_name;
    $Query->addError($Upload->error);
    if ($ban_image != '') {
        $Query->add('ban_image', DATA_STRING, $ban_image);
    }
    
    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            
            if ($ban_image != '' && $ban_image != $record_info['ban_image']) {
                if (file_exists($path_upload_banner . $record_info['ban_image'])) {
                    unlink($path_upload_banner . $record_info['ban_image']);
                }
            }
            
            set_session_toastr();
            redirect_url($url_return);
            
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
        .form-group .form_input .form-control {
            width: 60%;
        }
    </style>
</head>
<body class="sidebar-mini">
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
    <?=$Form->select('Vị trí', 'ban_position', $cfg_banner_position, $ban_position, true)?>
    <?=$Form->file('Ảnh', 'ban_image')?>
    <?=$Form->text('Tên banner', 'ban_name', $ban_name, true)?>
    <?=$Form->text('Tiêu đề hiển thị', 'ban_title', $ban_title, true)?>
    <?=$Form->textarea('Mô tả', 'ban_description', $ban_description, true, 'Tối đa 250 ký tự')?>
    <?=$Form->text('Link', 'ban_link', $ban_link, true)?>
    <?=$Form->text('Icon', 'ban_icon', $ban_icon, false, 'Font Awesome 5.15')?>
    <?=$Form->text('Label HOT', 'ban_label', $ban_label, false, 'Label HOT để nổi bật hơn các banner khác')?>
    <?=$Form->text('Text button', 'ban_text_link', $ban_text_link, false, 'Text hiển thị ở link/button khi click')?>
    <?=$Form->text('Thứ tự', 'ban_order', $ban_order)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>
