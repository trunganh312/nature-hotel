<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('module_edit_feature');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'modules_file';
$field_id   =   'modf_id';
$page_title =   'Sửa thông tin Tính năng module';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('modf_name', DATA_STRING, '', 'Bạn chưa nhập tên tính năng')
        ->add('modf_parent_id', DATA_INTEGER, 0)
        ->add('modf_file', DATA_STRING, '')
        ->add('modf_order', DATA_INTEGER, 0)
        ->add('modf_note', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            //Nếu chọn Parent thì update is_parent cho
            $modf_parent_id =   getValue('modf_parent_id', GET_INT, GET_POST);
            if ($modf_parent_id > 0) {
                $DB->execute("UPDATE modules_file SET modf_is_parent = 1 WHERE modf_id = $modf_parent_id LIMIT 1");
            }

            CommonService::resJson();
        } else {
            $Query->addError('Có lỗi xảy ra khi cập nhật dữ liệu, vui lòng thử lại');
        }
        
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
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
    
    //Lấy ra các menu parent
    $parent =   $DB->query("SELECT modf_id, modf_name
                            FROM $table
                            WHERE modf_module_id = " . $modf_module_id . " AND modf_parent_id = 0 AND modf_id <> " . $record_id . " AND modf_file = ''
                            ORDER BY modf_name")->toArray();
    $modf_parents   =   [];
    foreach ($parent as $row) {
        $modf_parents[$row['modf_id']]  =   $row['modf_name'];
    }
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->text('Tên tính năng', 'modf_name', $modf_name, true)?>
    <?=$Form->select('Thuộc nhóm', 'modf_parent_id', $modf_parents, $modf_parent_id)?>
    <?=$Form->text('File thực thi', 'modf_file', $modf_file, false, '<br>Nếu là tính năng xuất hiện ở menu trái thì bắt buộc phải nhập. Nếu là nhóm hoặc tính năng ko xuất hiện ở menu trái thì để trống.')?>
    <?=$Form->text('Thứ tự', 'modf_order', $modf_order)?>
    <?=$Form->textarea('Mô tả', 'modf_note', $modf_note)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>