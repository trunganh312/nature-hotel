<?

use src\Services\CommonService;

include('config_module.php');
/** Chỉ CTO mới có quyền chỉnh sửa các thông tin liên quan đến quyền **/
if (!$Auth->cto) {
    exit('Bạn không có quyền sử dụng tính năng này!');
}
//Admin hay User
$env    =   getValue('type', GET_STRING, GET_GET, '');
switch ($env) {
    case 'admin':
        $page_title =   'Sửa thông tin quyền Admin';
        $table      =   'admins_permission';
        break;

    case 'user':
        $page_title =   'Sửa thông tin quyền User';
        $table      =   'users_permission';
        break;
    
    default:
        exit('Bạn không có quyền sử dụng tính năng này');
        break;
}

/** --- Khai báo một số biến cơ bản --- **/
$field_id   =   'per_id';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('per_module_id', DATA_INTEGER, 0, 'Vui lòng chọn Module')
        ->add('per_feature_id', DATA_INTEGER, 0)
        ->add('per_name', DATA_STRING, '', 'Vui lòng nhập tên quyền')
        ->add('per_alias', DATA_STRING, '', 'Vui lòng nhập Alias của quyền', 'Alias này đã tồn tại')
        ->add('per_description', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
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
    <?=$Layout->loadHead($page_title);?>
    <style>
        .form-group .form_input .form-control {
            width: 100%;
        }
    </style>
</head>
<body class="windows-thickbox">
    <?
    $Layout->setPopup(true)->header($page_title);
    ?>
	<?
    //Tạo ra các biến sẵn được lấy ra từ bản ghi này để fill vào form
    $Query->generateVariable($record_info);
    
    //Lấy ra các Module
    $array_module   =   $Model->getListData('modules', 'mod_id, mod_name', ' mod_env = ' . ($env == 'admin' ? ADMIN_CRM : ADMIN_USER), 'mod_name');

    //Lấy ra các menu tính năng của module
    $arr_feature    =   $Model->getListData('modules_file', 'modf_id, modf_name', ' modf_module_id = ' . $per_module_id . ' AND modf_is_parent = 0', 'modf_name');
                
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->textHTML('Hệ thống', $env)?>
    <?=$Form->select('Module', 'per_module_id', $array_module, $per_module_id, true)?>
    <?=$Form->select('Tính năng', 'per_feature_id', $arr_feature, $per_feature_id)?>
    <?=$Form->text('Alias', 'per_alias', $per_alias, true, 'Viết liền, nối nhau bởi dấu _. Nếu sửa Alias thì phải sửa source code ở các file sử dụng phân quyền.')?>
    <?=$Form->text('Tên quyền', 'per_name', $per_name, true)?>
    <?=$Form->textarea('Mô tả', 'per_description', $per_description)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
    <script>
        $("#per_module_id").change(function () {
            $("#per_feature_id").load(
                "/common/get_select_child.php?type=module&id=" + $(this).val()
            );
        });
    </script>
</body>
</html>