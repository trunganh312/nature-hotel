<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('general_attribute_edit');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Sửa thông tin thuộc tính';
$table      =   'attribute_name';
$field_id   =   'atn_id';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('atn_group', DATA_INTEGER, 0, 'Bạn chưa chọn nhóm thuộc tính')
        ->add('atn_name', DATA_STRING, '', 'Bạn chưa nhập tên thuộc tính')
        ->add('atn_type', DATA_INTEGER, 0, 'Bạn chưa chọn kiểu thuộc tính')
        ->add('atn_alias_search', DATA_STRING, '')
        ->add('atn_order', DATA_INTEGER, 0)
        ->add('atn_note', DATA_STRING, '')
        ->add('atn_text_join_meta', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    $group  =   getValue('atn_group', GET_INT, GET_POST, 0);
    $param  =   getValue('atn_alias_search', GET_STRING, GET_POST, '');

    //Trong cùng 1 group thì ko được có 2 attribute có cùng param search
    $check  =   $DB->query("SELECT atn_id
                            FROM attribute_name
                            WHERE atn_group = $group AND atn_alias_search = '$param' AND atn_id <> $record_id")
                            ->getOne();
    if (!empty($check)) {
        $Query->addError('Param search này đã tồn tại trong nhóm');
    }
    
    //Kiểm tra lỗi
    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
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
    //Tạo ra các biến sẵn được lấy ra từ bản ghi này để fill vào form
    $Query->generateVariable($record_info);
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->select('Nhóm thuộc tính', 'atn_group', $cfg_group, $atn_group, true)?>
    <?=$Form->text('Tên thuộc tính', 'atn_name', $atn_name, true)?>
    <?=$Form->select('Kiểu thuộc tính', 'atn_type', $cfg_type_attribute, $atn_type, true)?>
    <?=$Form->checkbox('Bắt buộc chọn giá trị', 'atn_require', $atn_require)?>
    <?=$Form->checkbox('Cho phép lọc tìm kiếm', 'atn_show_filter', $atn_show_filter)?>
    <?=$Form->text('Param search', 'atn_alias_search', $atn_alias_search, false)?>
    <?=$Form->text('Thứ tự', 'atn_order', $atn_order, false, 'Thứ tự hiển thị ở bên ngoài website')?>
    <?=$Form->textarea('Mô tả', 'atn_note', $atn_note)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>