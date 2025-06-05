<?
include('config_module.php');
$Auth->checkPermission('promotion_edit');

require_once($path_core . "Model/PromotionModel.php");
$PromotionModel = new PromotionModel;

/** --- Khai báo một số biến cơ bản --- **/
$page_title     =   'Sửa chương trình khuyến mại';
$field_id       =   'pro_id';
$record_id      =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM promotions WHERE pro_id = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('promotions');
$Query->add('pro_type', DATA_INTEGER, 0, 'Bạn chưa chọn kiểu giảm giá')
        ->add('pro_value', DATA_INTEGER, 0)
        ->add('pro_updated_at', DATA_INTEGER, CURRENT_TIME)
        ->add('pro_name', DATA_STRING, '')
        ->add('pro_description', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if ($Query->submitForm()) {
    
    /** --- Lấy kiểu giảm giá để xử lý từng trường hợp cụ thể --- **/
    $vou_type   =   getValue('pro_type', GET_INT, GET_POST, 0);
    $vou_value  =   getValue('pro_value', GET_DOUBLE, GET_POST, 0);
    
    if ($vou_type == VOUCHER_TYPE_MONEY || $vou_type == VOUCHER_TYPE_PERCENT) {
        if ($vou_value <= 0)    $Query->addError('Bạn chưa nhập giá trị giảm giá');
    }
    
    //Không cho phép giảm quá 100%
    if ($vou_type == VOUCHER_TYPE_PERCENT) {
        $vou_value  =   getValue('pro_value', GET_INT, GET_POST, 0);
        if ($vou_value > 100)   $Query->addError('Giá trị giảm giá vượt quá 100%');
    }

    // Kiểm tra thời gian
    $daterange      =   getValue('daterange', GET_STRING, GET_POST);
    $daterange_int  =   generate_time_from_date_range($daterange, false);
    $daterange_int["to"]    +=  86399;   //Cho đến cuối ngày

    // Nếu sửa thời gian thì mới kiểm tra
    if ($daterange_int['from'] >= $daterange_int["to"] || $daterange_int["to"] < CURRENT_TIME) {
        $Query->addError('Thời gian bắt đầu/kết thúc không hợp lệ');
    }

    //Validate
    if ($Query->validate($field_id, $record_id)) {

        $Query->add('pro_start_time', DATA_INTEGER, $daterange_int["from"])
            ->add('pro_end_time', DATA_INTEGER, $daterange_int["to"]);
        
        $DB->execute($Query->generateQueryUpdate($field_id, $record_id));
    
        //Quay lại trang DS
        reload_parent_window();
    }
} else {
    $_POST['daterange'] = date('d/m/Y', $record_info["pro_start_time"]) .' - '. date('d/m/Y', $record_info["pro_end_time"]);
}
/** --- End of Submit form --- **/

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
</head>
<body class="windows-thickbox form_full">
    <?
    $Layout->setPopup(true)->header($page_title);
    ?>
	<?
    //Tạo ra các biến sẵn để fill vào form dựa vào các hàm add trường dữ liệu ở trên (GenerateQuery)
    $Query->generateVariable($record_info);
            
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->error)?>
    <?=$Form->textHTML('Mã khuyến mại', $pro_code)?>
    <?=$Form->text('Tên', 'pro_name', $pro_name, true)?>
    <?=$Form->select('Kiểu giảm giá', 'pro_type', PromotionModel::TYPE_LABEL, $pro_type, true)?>
    <?=$Form->text('Giá trị giảm', 'pro_value', $pro_value, true)?>
    <?=$Form->dateRangePicker('Thời bắt đầu/kết thúc', 'daterange', array_get($_POST, 'daterange'), true)?>
    <?=$Form->textarea('Mô tả', 'pro_description', $pro_description)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>