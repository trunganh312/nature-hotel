<?php
use src\Services\CommonService;

include('config_module.php');

// Check permissions
$Auth->checkPermission('application_edit');

/** --- Khai báo một số biến cơ bản --- **/
$page_title = 'Sửa thông tin ứng dụng';
$table = 'application';
$field_id = 'app_id';
$record_id = getValue('id', GET_INT, GET_GET, 0);
$record_info = $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để update dữ liệu --- **/
$Query = new GenerateQuery($table);
$Query->add('app_app_id', DATA_STRING, '', 'Bạn chưa nhập App ID')
    ->add('app_app_secret', DATA_STRING, '')
    ->add('app_request', DATA_INTEGER, 0)
    ->add('app_request_max', DATA_INTEGER, 0);
/** --- End of Class query để update dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    $app_id = getValue('app_app_id', GET_STRING, GET_POST, '');

    // Kiểm tra xem app_app_id đã tồn tại ở bản ghi khác chưa
    $check = $DB->query("SELECT app_id FROM application WHERE app_app_id = '$app_id' AND app_id <> $record_id")->getOne();
    if (!empty($check)) {
        $Query->addError('App ID này đã tồn tại');
    }

    // Validate form
    if ($Query->validate($field_id, $record_id)) {
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            CommonService::resJson(['success' => 1]);
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
            CommonService::resJson(['success' => 0, 'error' => $Query->getError()], STATUS_INACTIVE);
        }
    } else {
        CommonService::resJson(['success' => 0, 'error' => $Query->getError()], STATUS_INACTIVE);
    }
}
/** --- End of Submit form --- **/

?>

<?=$Layout->loadHead($page_title)?>

<?
$Layout->setPopup(true)->header($page_title);
?>
<?
// Tạo các biến sẵn được lấy ra từ bản ghi này để fill vào form
$Query->generateVariable($record_info);

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
<?=$Form->button('Cập nhật')?>
<?=$Form->closeForm()?>
<?
$Layout->footer();
?>