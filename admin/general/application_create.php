<?php
use src\Services\CommonService;


include('config_module.php');

// Check permissions
$Auth->checkPermission('application_create');

/** --- Khai báo một số biến cơ bản --- **/
$page_title = 'Thêm mới ứng dụng';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query = new GenerateQuery('application');
$Query->add('app_app_id', DATA_STRING, '', 'Bạn chưa nhập App ID')
    ->add('app_app_secret', DATA_STRING, '')
    ->add('app_request', DATA_INTEGER, 0)
    ->add('app_request_max', DATA_INTEGER, 0);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    $app_id = getValue('app_app_id', GET_STRING, GET_POST, '');

    // Kiểm tra xem app_app_id đã tồn tại chưa
    $check = $DB->query("SELECT app_id FROM application WHERE app_app_id = '$app_id'")->getOne();
    if (!empty($check)) {
        $Query->addError('App ID này đã tồn tại');
    }

    // Validate form
    if ($Query->validate()) {
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
            CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
        }
    } else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
}
/** --- End of Submit form --- **/

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead($page_title)?>
</head>
<body class="windows-thickbox">
    <?
    $Layout->setPopup(true)->header($page_title);
    ?>
    <?
    // Tạo các biến sẵn để fill vào form dựa vào các hàm add trường dữ liệu ở trên (GenerateQuery)
    $Query->generateVariable();
    
    // Show form data
    $Form = new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->text('App ID', 'app_app_id', $app_app_id, true)?>
    <?=$Form->text('App Secret', 'app_app_secret', $app_app_secret, false)?>
    <?=$Form->text('Requests', 'app_request', $app_request, false, 'Số lượng yêu cầu hiện tại', 'number')?>
    <?=$Form->text('Max Requests', 'app_request_max', $app_request_max, false, 'Số lượng yêu cầu tối đa', 'number')?>
    <?=$Form->checkbox('Kích hoạt', 'app_active', $app_active)?>
    <?=$Form->button('Thêm mới')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>