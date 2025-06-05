<?

use src\Services\CommonService;

require_once("config_module.php");

//Check quyền
$Auth->checkPermission('account_admin_set_permission');
//Alias Menu
$Auth->setAliasMenu('account_admin_set_permission');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Phân quyền cho các nhóm Admin';
/** --- End of Khai báo một số biến cơ bản --- **/

//Nhóm được phân quyền
$group_admin    =   getValue('group', GET_INT, GET_POST, getValue('group'));
$record_info    =   $DB->query("SELECT * FROM users_group WHERE gro_id = " . $group_admin)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

//Các quyền hiện tại của các nhóm
$current_permission =   [];

//Các ID 
$data   =   $DB->query("SELECT pega_group_id, per_id, per_module_id, per_name
                        FROM admins_permission_groups
                        INNER JOIN admins_permission ON (pega_permission_id = per_id)
                        WHERE pega_group_id = $group_admin
                        ORDER BY pega_group_id")
                        ->toArray();
foreach ($data as $row) {
    $current_permission[]   =   $row['per_id'];
}
//Lấy ra các quyền theo modules để hiển thị
$list_permission    =   [];
$data   =   $DB->query("SELECT admins_permission.*, mod_id, mod_name
                            FROM admins_permission
                            INNER JOIN modules ON per_module_id = mod_id
                            WHERE per_active = 1
                            ORDER BY mod_order, per_name")
                            ->toArray();
foreach ($data as $row) {
        $list_permission[$row['per_module_id']][]   =   $row;
}

$list_modules   =   $Model->getListData('modules', 'mod_id, mod_name', 'mod_active = 1 AND mod_env = ' . ADMIN_CRM);

$res = [
    'group_name' => $record_info['gro_name'],
    'current_permissions' => $current_permission,
    'modules' => []
];


foreach ($list_permission as $module_id => $modules) {
    $module_data = [
        'module_id' => $module_id,
        'module_name' => isset($list_modules[$module_id]) ? $list_modules[$module_id] : null,
        'permissions' => []
    ];
    
    foreach ($modules as $per) {
        $module_data['permissions'][] = [
            'per_id' => $per['per_id'],
            'per_name' => $per['per_name'],
            'is_checked' => in_array($per['per_id'], $current_permission)
        ];
    }
    
    $res['modules'][] = $module_data;
}

if(getValue('json') ){
    CommonService::resJson($res);
}

$Query  =   new GenerateQuery('admins_permission_groups');

if(CommonService::isPost() && $group_admin > 0){
    
    //Lấy các quyền được chọn
    $permissions    =   getValue('permission', GET_ARRAY, GET_POST, []);
    //Các quyền mới được add thêm
    $permission_insert  =   array_diff($permissions, $current_permission);
    if (!empty($permission_insert)) {
        $arr_data   =   [];
        foreach ($permission_insert as $per) {
            $arr_data[] =   '(' . $group_admin . ',' . $per . ')';
        }
        
        //Insert data
        $DB->execute("INSERT INTO admins_permission_groups (pega_group_id, pega_permission_id) VALUES" . implode(',', $arr_data));
    }
    
    //Xóa bỏ các quyền cũ
    $permission_delete  =   array_diff($current_permission, $permissions);
    if (!empty($permission_delete)) {
        $arr_data   =   [];
        foreach ($permission_delete as $per) {
            $arr_data[] =  $per;
        }
        
        //Delete data
        $DB->execute("DELETE FROM admins_permission_groups WHERE pega_group_id = $group_admin AND pega_permission_id IN (" . implode(',', $arr_data) . ")");
    }
    
    //Set session và redirect
    CommonService::resJson();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?=$Layout->loadHead($page_title);?>
    <style>
        .group_permission {
            float: left;
            width: 100%;
            padding: 20px 10px;
            border-bottom: 1px dotted #ccc;
        }
        .group_permission:last-child {
            margin-bottom: 20px;
        }
        .group_permission * {
            font-size: 14px;
        }
        .group_permission h5 label {
            display: initial;
            margin-left: 15px;
        }
        .group_permission h5 label input {
            top: 2px;
            position: relative;
        }
        .group_permission .checker {
            margin-right: 0;
            top: -2px;
        }
        .group_permission label {
            display: contents;
            font-weight: 400;
        }
        .group_permission ul {
            float: left;
            width: 25%;
            margin: 0;
        }
        .group_permission li {
            float: left;
            width: 100%;
            list-style: none;
        }
        .form-horizontal .control-button-form .controls {
            margin-left: 0;
        }
        #group {
            width: auto;
        }
        .main_form .form-group.form-button {
            margin-top: 30px;
            float: left;
            width: 100%;
        }
    </style>
</head>
<body class="sidebar-mini">
    <?
    $Layout->header($page_title);
    //Tạo ra các biến sẵn để fill vào form dựa vào các hàm add trường dữ liệu ở trên (GenerateQuery)
    $Query->generateVariable();

    $DataLevel  =   new DataLevelModel('admins_group');
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm();?>
    <?=$Form->select('Nhóm tài khoản', 'group', $DataLevel->getAllLevelStep(), $group_admin);?>
    
    <?
    /** Nếu đã chọn nhóm để phân quyền rồi thì show ra các quyền **/
    if ($group_admin > 0) {
        
        /** --- Lấy ra DS các module --- **/
        $list_modules   =   $Model->getListData('modules', 'mod_id, mod_name', 'mod_active = 1 AND mod_env = ' . ADMIN_CRM);
        
        //Lấy ra các quyền theo modules để hiển thị
        $list_permission    =   [];
        $data   =   $DB->query("SELECT admins_permission.*, mod_id, mod_name
                                FROM admins_permission
                                INNER JOIN modules ON per_module_id = mod_id
                                WHERE per_active = 1
                                ORDER BY mod_order, per_name")
                                ->toArray();
        foreach ($data as $row) {
            $list_permission[$row['per_module_id']][]   =   $row;
        }
        
        //Hiển thị các quyền
        $stt    =   0;
        foreach ($list_permission as $module_id => $modules) {
            $stt++;
            echo    '<div class="group_permission">';
            echo    '<h5>' . $stt . '. ' . $list_modules[$module_id] . '&nbsp;<label><input type="checkbox" class="check_all_permission" />&nbsp;Chọn tất cả</label></h5>';
            $i  =   0;
            //Tính tổng số quyền để chia thành 3 column nhìn sắp xếp theo chữ cái cho nó xuôi.
            $count  =   count($modules);
            $col    =   ceil($count/4);

            echo    '<ul class="">';
            foreach ($modules as $per) {
                ?>
                <li>
                    <label><input type="checkbox" name="permission[]" value="<?=$per['per_id']?>" <?=(in_array($per['per_id'], $current_permission) ? 'checked' : '')?> class="check_item_permission" />&nbsp;<?=$per['per_name']?></label>
                </li>
                <?
                $i++;
                if ($i < $count && $i % $col == 0)    echo '</ul><ul>';
            }
            echo    '</ul>';
            echo    '</div>';
        }
        
        echo    $Form->button('Cập nhật');
        echo    $Form->closeForm();
    }
    $Layout->footer();
    ?>
    <script>
        $(function() {
            $('#group').change(function() {
                window.location.href    =   'set_permission.php?group=' + $(this).val();
            });
            $('.check_all_permission').click(function() {
                if ($(this).is(':checked')) {
                    $(this).parents('.group_permission').find('.check_item_permission').attr('checked', 'checked').parent().addClass('checked');
                } else {
                    $(this).parents('.group_permission').find('.check_item_permission').removeAttr('checked').parent().removeClass('checked');
                }
                
            });
        });
    </script>
</body>
</html>