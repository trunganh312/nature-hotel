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
        $page_title =   'Thêm mới quyền Admin';
        $table      =   'admins_permission';
        break;

    case 'user':
        $page_title =   'Thêm mới quyền User';
        $table      =   'users_permission';
        break;
    
    default:
        exit('Bạn không có quyền sử dụng tính năng này');
        break;
}

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('per_module_id', DATA_INTEGER, 0, 'Vui lòng chọn module')
        ->add('per_feature_id', DATA_INTEGER, 0)
        ->add('per_alias', DATA_STRING, '', 'Vui lòng nhập Alias của quyền', 'Alias này đã tồn tại')
        ->add('per_name', DATA_STRING, '', 'Vui lòng nhập tên quyền')
        ->add('per_description', DATA_STRING, '')
        ->add('per_active', DATA_INTEGER, 1);
if ($env == 'admin') {
    $Query->add('per_check_owner', DATA_INTEGER, 0)
        ->add('per_allow_leader', DATA_INTEGER, 0);
} else {
    $Query->add('per_company_config', DATA_INTEGER, 0);
}
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    //Validate form
    if ($Query->validate()) {
        
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
            CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
        }
    }else {
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
    //Tạo ra các biến sẵn để fill vào form dựa vào các hàm add trường dữ liệu ở trên (GenerateQuery)
    $Query->generateVariable();
    
    //Lấy ra các Module
    $array_module   =   $Model->getListData('modules', 'mod_id, mod_name', ' mod_env = ' . ($env == 'admin' ? ADMIN_CRM : ADMIN_USER), 'mod_name');

    $module_id  =   getValue('module', GET_INT, GET_GET, $per_module_id);
    //Lấy ra các menu tính năng của module
    $arr_feature    =   $Model->getListData('modules_file', 'modf_id, modf_name', ' modf_module_id = ' . $module_id . ' AND modf_is_parent = 0', 'modf_order');
                
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->textHTML('Hệ thống', $env)?>
    <?=$Form->select('Module', 'per_module_id', $array_module, $module_id, true)?>
    <?=$Form->select('Tính năng', 'per_feature_id', $arr_feature, $per_feature_id)?>
    <?=$Form->text('Alias', 'per_alias', $per_alias, true, 'Viết liền, nối nhau bởi dấu _')?>
    <?=$Form->text('Tên quyền', 'per_name', $per_name, true)?>
    <?=$Form->textarea('Mô tả', 'per_description', $per_description)?>
    <?
    if ($env == 'admin') {
        ?>
        <?=$Form->checkbox('Check quyền sở hữu dữ liệu', 'per_check_owner', $per_check_owner)?>
        <?=$Form->checkbox('Cho phép leader xử lý', 'per_allow_leader', $per_allow_leader)?>
        <?
    } else {
        ?>
        <?=$Form->checkbox('Check quyền sở hữu dữ liệu theo cấu hình của công ty', 'per_company_config', $per_company_config)?>
        <?
    }
    ?>
    <?=$Form->button('Thêm mới')?>
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