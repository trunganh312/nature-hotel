<?
include('config_module.php');
$Auth->checkPermission('mkt_convert_image_webp');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Convert sang ảnh WEBP';
$error  =   [];
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Submit form --- **/
$action =   getValue('action', GET_STRING, GET_POST, '');
if ($action == 'submitted') {
    
    $path_upload    =   $path_root . '/image/temp_up/';
    
    $Upload     =   new Upload('image', $path_upload, 10, 10);
    
    if (!empty($Upload->new_name)) {
        $_SESSION['file_converted'] =   $path_upload . $Upload->new_name;
        redirect_url('convert_image_webp.php');
    } else {
        $error[]    =   $Upload->error;
    }
    
}
/** --- End of Submit form --- **/

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
</head>
<body class="">
    <?
    $Layout->header($page_title);
    ?>
	<?
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($error)?>
    <?
    if (!empty($_SESSION['file_converted'])) {
        echo    $Form->textHTML('File converted', $_SESSION['file_converted']);
        unset($_SESSION['file_converted']);
    }
    ?>
    <?=$Form->file('File', 'image', true)?>
    <?=$Form->button('Convert')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>
