<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('module_create');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'modules';
$field_id   =   'mod_id';
$page_title =   'Thêm mới Module';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('mod_name', DATA_STRING, '', 'Bạn chưa nhập tên module', 'Tên module đã tồn tại')
        ->add('mod_group', DATA_INTEGER, 0, 'Bạn chưa chọn nhóm của module')
        ->add('mod_folder', DATA_STRING, '', 'Bạn chưa nhập Folder của module')
        ->add('mod_icon', DATA_STRING, '')
        ->add('mod_order', DATA_INTEGER, 0)
        ->add('mod_active', DATA_INTEGER, 1)
        ->add('mod_note', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    //Check trùng folder
    $group  =   getValue('mod_group', GET_INT, GET_POST);
    $folder =   getValue('mod_folder', GET_STRING, GET_POST, '', 1);
    $name =   getValue('mod_name', GET_STRING, GET_POST, '');
    $check  =   $DB->query("SELECT mod_id FROM $table WHERE mod_group = $group AND mod_folder = '$folder'")->getOne();
    if (!empty($check)) {
        $Query->addError('Folder này đã được sử dụng ở module khác');
    }
    $check  =   $DB->query("SELECT mod_id FROM $table WHERE mod_group = $group AND mod_name = '$name'")->getOne();
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
    if ($Query->validate()) {
        
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
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
    //Tạo ra các biến sẵn để fill vào form dựa vào các hàm add trường dữ liệu ở trên (GenerateQuery)
    $Query->generateVariable();
    
    //Lấy ra giá trị lớn nhất hiện tại của mod_order để tự động cho order +1
    $row    =   $DB->query("SELECT MAX(mod_order) AS max_order FROM " . $table)->getOne();
    if (isset($row['max_order'])) {
        $mod_order  =   (int)$row['max_order'] + 1;
    }
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->text('Tên module', 'mod_name', $mod_name, true)?>
    <?=$Form->select('Nhóm', 'mod_group', $cfg_module_group_name, $mod_group, true)?>
    <?=$Form->text('Folder con', 'mod_folder', $mod_folder, true)?>
    <?=$Form->text('Icon', 'mod_icon', $mod_icon)?>
    <?=$Form->text('Thứ tự', 'mod_order', $mod_order)?>
    <?=$Form->textarea('Mô tả', 'mod_note', $mod_note)?>
    <?=$Form->button('Thêm mới')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>
