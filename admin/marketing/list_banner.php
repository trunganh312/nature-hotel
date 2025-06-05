<?
include('config_module.php');
$Auth->checkPermission('banner_list');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Danh sách banner';
$table      =   'banner';
$field_id   =   'ban_id';
$has_edit   =   $Auth->hasPermission('banner_edit');

//Các biến sử dụng cho DS
$ban_position   =   $cfg_banner_position;

/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('ban_image', 'Ảnh', TAB_IMAGE)
        ->column('ban_position', 'Vị trí', TAB_SELECT, true)
        ->column('ban_name', 'Tên gọi', TAB_TEXT, true)
        ->column('ban_title', 'Tên hiển thị', TAB_TEXT, true)
        ->column('ban_description', 'Mô tả', TAB_TEXT)
        ->column('ban_label', 'Label HOT', TAB_TEXT, true)
        ->column('ban_icon', 'Icon', TAB_TEXT)
        ->column('ban_text_link', 'Text button', TAB_TEXT, true)
        //->column('ban_time_create', 'Ngày tạo', TAB_DATE)
        ->column('ban_time_update', 'Cập nhật', TAB_DATE)
        ->column('adm_name', 'Người cập nhật', TAB_TEXT, true)
        ->column('ban_order', 'Order', TAB_NUMBER, false, true)
        ->setPathImage(DOMAIN_IMAGE . '/banner/');
if ($has_edit) {
    $Table->column('ban_active', 'Act', TAB_CHECKBOX, false, true)
            ->addED(true)
            ->setEditFileName('edit_banner.php');
}
$Table->addSQL("SELECT banner.*, adm_name
                FROM banner
                INNER JOIN admin ON ban_admin_create = adm_id
                ORDER BY ban_time_update DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title);?>
    <style>
        #ban_position {
            width: 200px;
        }
    </style>
</head>
<body class="sidebar-mini listing_page">
    <?
    if ($has_edit)  $Layout->setTitleButton('<a href="create_banner.php" title="Thêm mới banner"><i class="fas fa-plus-circle"></i> Thêm mới</a>');
    $Layout->header($page_title);
    ?>
    <?=$Table->createTable()?>
    <?
    //Hiển thị data
    $data   =   $DB->query($Table->sql_table)->toArray();
    $stt    =   0;
    foreach ($data as $row) {
        $Table->setRowData($row);
        $stt++;
        ?>
        <?=$Table->createTR($stt, $row[$field_id]);?>
        <?=$Table->showFieldImage('ban_image')?>
        <?=$Table->showFieldArray('ban_position')?>
        <?=$Table->showFieldText('ban_name')?>
        <?=$Table->showFieldText('ban_title')?>
        <?=$Table->showFieldText('ban_description')?>
        <?=$Table->showFieldText('ban_label')?>
        <td class="text-center">
            <?
            if (!empty($row['ban_icon'])) {
                echo    '<i class="' . $row['ban_icon'] . '"></i>';
            }
            ?>
        </td>
        <?=$Table->showFieldText('ban_text_link')?>
        <?//=$Table->showFieldDate('ban_time_create')?>
        <?=$Table->showFieldDate('ban_time_update')?>
        <?=$Table->showFieldText('adm_name')?>
        <?=$Table->showFieldNumber('ban_order')?>
        <?
        if ($has_edit)  echo    $Table->showFieldCheckbox('ban_active');
        ?>
        <?=$Table->closeTR($row[$field_id]);?>
        <?
    }
    ?>
    <?=$Table->closeTable();?>
    <?
    $Layout->footer();
    ?>
</body>
</html>

