<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('system_module_update');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Sửa thông tin Module';
$table      =   'modules';
$field_id   =   'mod_id';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('mod_name', DATA_STRING, '', 'Bạn chưa nhập tên module')
        ->add('mod_group', DATA_INTEGER, 0, 'Bạn chưa chọn nhóm của module')
        ->add('mod_folder', DATA_STRING, '', 'Bạn chưa nhập Folder của module')
        ->add('mod_icon', DATA_STRING, '')
        ->add('mod_order', DATA_INTEGER, 0)
        ->add('mod_note', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    //Check trùng folder
    $group  =   getValue('mod_group', GET_INT, GET_POST);
    $folder =   getValue('mod_folder', GET_STRING, GET_POST, '');
    $name =   getValue('mod_name', GET_STRING, GET_POST, '');
    $check  =   $DB->query("SELECT mod_id FROM $table WHERE mod_group = $group AND mod_folder = '$folder' AND mod_id <> $record_id")->getOne();
    if (!empty($check)) {
        $Query->addError('Folder này đã được sử dụng ở module khác');
    }
    $check  =   $DB->query("SELECT mod_id FROM $table WHERE mod_group = $group AND mod_name = '$name' AND mod_id <> $record_id")->getOne();
    if (!empty($check)) {
        $Query->addError('Tên này đã được sử dụng ở module này rồi');
    }


    //Dựa vào folder_group để lấy ra Env (Admin hay User)
    if ($group == MODULE_SENNET) {
        $Query->add('mod_env', DATA_INTEGER, ADMIN_CRM);
    } else {
        $Query->add('mod_env', DATA_INTEGER, ADMIN_USER);
    }

    //Validate form
    if ($Query->validate($field_id, $record_id)) {
        
        $DB->execute($Query->generateQueryUpdate($field_id, $record_id));
        CommonService::resJson();
        
    }else{
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
    <?=$Form->text('Tên module', 'mod_name', $mod_name, true)?>
    <?=$Form->select('Nhóm tính năng', 'mod_group', $cfg_module_group_name, $mod_group, true)?>
    <?=$Form->text('Folder', 'mod_folder', $mod_folder, true)?>
    <?=$Form->text('Icon', 'mod_icon', $mod_icon)?>
    <?=$Form->text('Thứ tự', 'mod_order', $mod_order)?>
    <?=$Form->textarea('Mô tả', 'mod_note', $mod_note)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>