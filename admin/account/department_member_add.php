<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('account_admin_department_member_add');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Thêm mới nhân viên vào Phòng/Ban';
/** --- End of Khai báo một số biến cơ bản --- **/

$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM admins_department WHERE dep_id = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('admins_department_admins');
$Query->add('deac_department_id', DATA_INTEGER, $record_id)
        ->add('deac_account_id', DATA_INTEGER, 0, 'Bạn chưa chọn Nhân viên')
        ->add('deac_time_create', DATA_INTEGER, CURRENT_TIME)
        ->add('deac_account_create', DATA_INTEGER, $Auth->id)
        ->add('deac_note', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {

    //Kiểm tra xem Admin này đã được gán vào phòng ban này chưa
    $admin_id   =   getValue('deac_account_id', GET_INT, GET_POST);
    $check  =   $DB->query("SELECT * FROM admins_department_admins WHERE deac_department_id = $record_id AND deac_account_id = $admin_id")->getOne();
    if (!empty($check)) {
        $Query->addError('Nhân viên này đã nằm trong Phòng/Ban này rồi');
    }
    
    //Validate form
    if ($Query->validate()) {
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Có lỗi xảy ra khi thêm mới thành viên, vui lòng thử lại');
        }
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error');
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
            min-width: 310px;
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
    
    //Lấy thông tin Account để giữ lại form
    $admin_name =   '';
    $admin_id   =   getValue('deac_account_id', GET_INT, GET_POST);
    $row    =   $DB->query("SELECT adm_name, adm_email FROM admins WHERE adm_id = " . $admin_id)->getOne();
    if (!empty($row)) {
        $admin_name =   $row['adm_name'] . ' - ' . $row['adm_email'];
    }
                
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->textHTML('Phòng/Ban', $record_info['dep_name'])?>
    <?=$Form->textSearchAuto('Nhân viên', 'deac_account_id', 'admin', $admin_name, $deac_account_id, true, 'Gõ tên hoặc Email của tài khoản để tìm')?>
    <?=$Form->textarea('Giới thiệu', 'deac_note', $deac_note)?>
    <?=$Form->button('Thêm mới')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>