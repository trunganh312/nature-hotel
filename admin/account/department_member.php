<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('account_admin_department_member_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Danh sách nhân viên của Phòng/Ban';
$has_edit   =   $Auth->hasPermission('account_admin_department_list_member_edit');

//Lấy thông tin của Phòng/Ban
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM admins_department WHERE dep_id = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable('admins', 'adm_id');
$Table->column('adm_avatar', 'Avatar', TAB_IMAGE)
        ->column('adm_name', 'Họ tên', TAB_TEXT, true)
        ->column('adm_email', 'Email', TAB_TEXT, true)
        ->column('adm_phone', 'Điện thoại', TAB_TEXT, true)
        ->column('deac_time_create', 'Ngày tham gia', TAB_DATE, false, true)
        ->column('deac_note', 'Ghi chú', TAB_TEXT)
        ->setFieldHidden(['id'])
        ->setPathImage(DOMAIN_STATIC . '/image/admin/small/');
if ($has_edit) {
    $Table->column('remove', 'Xóa');
}
$Table->addSQL("SELECT adm_id, adm_name, adm_avatar, adm_email, adm_phone, deac_time_create, deac_note
                FROM admins_department_admins
                INNER JOIN admins ON deac_account_id = adm_id
                WHERE deac_department_id = $record_id
                ORDER BY adm_name, deac_time_create DESC");

$data   =   $DB->query($Table->sql_table)->toArray();

$data = array_map(function($item) use ($Router, $Auth) {
    $item['deac_time_create']   = date("d/m/Y", $item['deac_time_create']);
    return $item;
}, $data);

$res = [
    "rows" => $data,
    "pagination" => [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
    "params" => [],
    "others" => [],
    "hasUpdate" => isset($has_edit) ? $has_edit : false,
    "department_id" => intval($record_id),
    "department_name" => isset($record_info) ? $record_info['dep_name'] : null,
    "hasPermission" => isset($has_permission) ? $has_permission : false,
];

if(getValue('json')) {
    CommonService::resJson($res);
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead($page_title)?>
    <style>
        .col_remove {
            width: 50px;
        }
    </style>
</head>
<body class="windows-thickbox listing_page">
    <?
    $Layout->setPopup(true);
    if ($has_edit)   $Layout->setNoteTitle('<a href="department_member_add.php?id=' . $record_id . '&url=' . base64_encode($_SERVER['REQUEST_URI']) . '" class="btn_popup_title" title="Thêm mới nhân viên vào Phòng/Ban này"><i class="fas fa-plus-circle"></i> Thêm mới</a>');
    $Layout->header($page_title);
    ?>
    <?=$Table->createTable();?>
    <?
    //Hiển thị data
    $data   =   $DB->query($Table->sql_table)->toArray();
    $stt    =   0;
    foreach ($data as $row) {
        $Table->setRowData($row);
        $stt++;
        ?>
        <?=$Table->createTR($stt, $row['adm_id']);?>
        <?=$Table->showFieldImage('adm_avatar')?>
        <?=$Table->showFieldText('adm_name')?>
        <?=$Table->showFieldText('adm_email')?>
        <?=$Table->showFieldText('adm_phone')?>
        <?=$Table->showFieldDate('deac_time_create')?>
        <?=$Table->showFieldText('deac_note')?>
        <?
        if ($has_edit) {
            ?>
            <td class="text-center">
                <span class="cursor" onclick="remove_record(<?=$record_id?>, <?=$row['adm_id']?>);">
                    <i class="far fa-times-circle"></i>
                </span>
            </td>
            <?
        }
        ?>
        <?=$Table->closeTR($row['adm_id'])?>
        <?
    }
    ?>
    <?=$Table->closeTable();?>
    <?
    $Layout->footer();
    ?>
    <script>
        function remove_record(department_id, account_id) {
            if (confirm('Bạn có chắc chắn muốn xóa thành viên này khỏi phòng <?=$record_info['dep_name']?>?')) {
                $.post(
                    'department_member_remove.php',
                    {department: department_id, member: account_id},
                    function (data) {
                        if (data.status != 1) {
                            alert(data.error);
                        } else {
                            $("#tr_" + account_id).fadeOut().remove();
                        }
                    },
                    "json"
                );
            }
        }
    </script>
</body>
</html>

