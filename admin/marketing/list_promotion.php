<?
include('config_module.php');
$Auth->checkPermission('promotion_list');

require_once($path_core . "Model/PromotionModel.php");

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Chương trình khuyến mại';
$has_edit   =   $Auth->hasPermission('promotion_edit');
$field_id   =   'pro_id';
/** --- End of Khai báo một số biến cơ bản --- **/

//Mảng chứa kiểu promotions
$pro_type           =   PromotionModel::TYPE_LABEL;
$pro_condition_type =   PromotionModel::CONDITION_LABEL;

/** --- DataTable --- **/
$Table  =   new DataTable('promotions', $field_id);
$Table->column('pro_code', 'Mã', TAB_TEXT, true)
        ->column('pro_name', 'Tên', TAB_TEXT, true)
        ->column('pro_condition_type', 'Điều kiện', TAB_SELECT, true)
        ->column('pro_type', 'Kiểu giảm', TAB_SELECT, true)
        ->column('pro_value', 'Giá trị giảm', TAB_NUMBER, false, true)
        ->column('pro_start_time', 'Ngày bắt đầu', TAB_DATE, true, true)
        ->column('pro_end_time', 'Ngày kết thúc', TAB_DATE, false, true);
if ($has_edit) {
    $Table->column('pro_active', 'Act', TAB_CHECKBOX, false, true)
        ->addED(true);
}
$Table->setEditFileName('edit_promotion.php')
    ->setEditThickbox(['width' => 800, 'height' => 600, 'title' => 'Sửa chương trình khuyến mại']);
$Table->setShowTimeMinute(true);
$Table->addSQL();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
</head>
<body>
    <?
    if ($has_edit)  $Layout->setTitleButton('<a href="create_promotion.php?TB_iframe=true&width=800&height=600" class="thickbox" title="Thêm mới chương trình khuyến mại"><i class="fas fa-plus-circle"></i> Thêm mới</a>');
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
        <?=$Table->showFieldText('pro_code')?>
        <?=$Table->showFieldText('pro_name')?>
        <?=$Table->showFieldArray('pro_condition_type')?>
        <?=$Table->showFieldArray('pro_type')?>
        <?=$Table->showFieldNumber('pro_value')?>
        <?=$Table->showFieldDate('pro_start_time')?>
        <?=$Table->showFieldDate('pro_end_time')?>
        <?
        if ($has_edit)  echo    $Table->showFieldCheckbox('pro_active');
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