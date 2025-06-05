<?
include('config_module.php');
$Auth->checkPermission('banner_create');

/** --- Khai báo một số biến cơ bản --- **/
$page_title     =   'Thêm mới banner';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('banner');
$Query->add('ban_position', DATA_INTEGER, 0, 'Bạn chưa chọn vị trí')
        ->add('ban_name', DATA_STRING, '', 'Bạn chưa nhập tên banner', 'Tên banner này đã tồn tại')
        ->add('ban_title', DATA_STRING, '', 'Bạn chưa nhập tiêu đề hiển thị')
        ->add('ban_description', DATA_STRING, '', 'Bạn chưa nhập mô tả')
        ->add('ban_link', DATA_STRING, '')
        ->add('ban_icon', DATA_STRING, '')
        ->add('ban_label', DATA_STRING, '')
        ->add('ban_text_link', DATA_STRING, '')
        ->add('ban_order', DATA_INTEGER, 0)
        ->add('ban_time_create', DATA_INTEGER, CURRENT_TIME)
        ->add('ban_time_update', DATA_INTEGER, CURRENT_TIME)
        ->add('ban_admin_create', DATA_INTEGER, $Auth->id)
        ->add('ban_active', DATA_INTEGER, 0);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if ($Query->submitForm()) {
    
    //Ảnh đại diện
    $Upload =   new Upload('ban_image', $path_upload_banner, 0, 0, '');
    $ban_image  =   $Upload->new_name;
    $Query->addError($Upload->error);
    $Query->add('ban_image', DATA_STRING, $ban_image, 'Bạn chưa nhập ảnh cho banner');
    
    //Validate form
    if ($Query->validate()) {
        
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            
            redirect_url('list_banner.php');
            
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
    <?=$Layout->loadHead($page_title);?>
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
    //Tạo ra các biến sẵn để fill vào form dựa vào các hàm add trường dữ liệu ở trên (GenerateQuery)
    $Query->generateVariable();
    
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
    <?=$Form->text('Link', 'ban_link', $ban_link)?>
    <?=$Form->text('Icon', 'ban_icon', $ban_icon, false, 'Font Awesome 5.15')?>
    <?=$Form->text('Label HOT', 'ban_label', $ban_label, false, 'Label HOT để nổi bật hơn các banner khác')?>
    <?=$Form->text('Text button', 'ban_text_link', $ban_text_link, false, 'Text hiển thị ở link/button khi click')?>
    <?=$Form->text('Thứ tự', 'ban_order', $ban_order)?>
    <?=$Form->checkbox('Active', 'ban_active', 1)?>
    <?=$Form->button('Thêm mới')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>
