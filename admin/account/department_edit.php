<?

use src\Facades\DB;
use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('account_admin_department_edit');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'admins_department';
$field_id   =   'dep_id';
$page_title =   'Sửa thông tin Phòng/Ban';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('admins_department');
$Query->add('dep_name', DATA_STRING, '', 'Bạn chưa nhập tên Phòng/Ban', 'Tên Phòng/Ban này đã tồn tại')
        ->add('dep_parent_id', DATA_INTEGER, 0)
        ->add('dep_manager_id', DATA_INTEGER, 0, 'Bạn chưa chọn Người quản lý')
        ->add('dep_description', DATA_STRING, '')
        ->add('dep_last_update', DATA_INTEGER, CURRENT_TIME);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    //Validate form
    if ($Query->validate($field_id, $record_id)) {
    
        /** Avatar **/
        if(isset($_FILES['avatar'])) {
            $Upload     =   new Upload('avatar', $path_image_department, 450, 450);
            $dep_avatar =   $Upload->new_name;
            $Query->addError($Upload->error);
        }
        if (!empty($dep_avatar)) {
            $Query->add('dep_avatar', DATA_STRING, $dep_avatar);
        }
        $parent_id  =   getValue('dep_parent_id', GET_INT, GET_POST, 0);
        if ($parent_id == $record_id)   $Query->addError('Không được chọn Phòng/Ban cấp trên là chính mình');
         // Nếu mình là cấp trên của nó mà mình edit lại chọn nó là cấp trên của mình nữa thì không được
         $parent_id  =   getValue('dep_parent_id', GET_INT, GET_POST, 0);
         $check_parent = DB::pass()->query("SELECT dep_id FROM admins_department WHERE dep_id = $parent_id AND dep_parent_id = $record_id")->getOne();
         if (isset($check_parent)) {
             $Query->addError('Bạn không thể chọn Phòng/ban cấp con làm cấp trên của mình!');
         }
        if(!empty($Query->getError())) {
            CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
        }
        
        if ($DB->execute($Query->generateQueryUpdate($field_id, $record_id)) >= 0) {
            
            //Insert Manager ID vào bảng department_admin
            $manager_id =   getValue('dep_manager_id', GET_INT, GET_POST);
            if ($manager_id != $record_info['dep_manager_id']) {
                $DB->execute("INSERT INTO admins_department_admins(deac_department_id, deac_account_id, deac_time_create, deac_note)
                                VALUES($record_id, $manager_id, " . CURRENT_TIME . ", 'Quản lý')");
            }
            
            //Resize ảnh
            if (!empty($dep_avatar)) {
                $Upload->resizeImage($array_resize);
                
                //Xóa ảnh cũ
                $Image->deleteFile($path_image_department, $record_info['dep_avatar']);
            }
    
            //Nếu chọn phòng ban cấp trên thì update is_parent cho phòng ban đó
            
            if ($parent_id > 0) {
                $DB->execute("UPDATE admins_department SET dep_is_parent = 1 WHERE dep_id = $parent_id LIMIT 1");    
            }
            
            /** Lưu log **/
            $data_new   =   $DB->query("SELECT * FROM admins_department WHERE dep_id = $record_id")->getOne();
            $Log->setTable('admins_department')->genContent($record_info, $data_new)->createLog($record_id);
            
            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
            //Xóa ảnh để đỡ bị rác
            if (!empty($dep_avatar)) {
                $Image->deleteFile($path_image_department, $dep_avatar);
            }
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
</head>
<body class="windows-thickbox">
    <?
    $Layout->setPopup(true)->header($page_title);
    ?>
	<?
    //Tạo ra các biến sẵn được lấy ra từ bản ghi này để fill vào form
    $Query->generateVariable($record_info);
    
    //Lấy ra list các phòng ban
    $DataLevel          =   new DataLevelModel('admins_department');
    $list_department    =   $DataLevel->getAllLevelStep();

    $manager_name   =   '';
    $account    =   $DB->query("SELECT adm_id, adm_name, adm_email FROM admins WHERE adm_id = $dep_manager_id")->getOne();
    if (!empty($account)) {
        $manager_name   =   $account['adm_name'] . ' - ' . $account['adm_email'];
    }
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->file('Avatar', 'avatar', false, 'Ảnh vuông. Kích thước tối thiểu 450x450')?>
    <?=$Form->showImage($Router->srcDepartment($dep_avatar, SIZE_SMALL))?>
    <?=$Form->text('Tên Phòng/Ban', 'dep_name', $dep_name, true)?>
    <?=$Form->select2('Cấp trên', 'dep_parent_id', $list_department, $dep_parent_id)?>
    <?=$Form->textSearchAuto('Người quản lý', 'dep_manager_id', 'admin', $manager_name, $dep_manager_id, true, 'Gõ tên hoặc Email của tài khoản để tìm')?>
    <?=$Form->textarea('Giới thiệu', 'dep_description', $dep_description)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>