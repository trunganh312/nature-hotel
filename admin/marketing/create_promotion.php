<?
include('config_module.php');
$Auth->checkPermission('promotion_create');

require_once($path_core . "Model/PromotionModel.php");
$PromotionModel = new PromotionModel;

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Thêm mới chương trình khuyến mại';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('promotions');
$Query->add('pro_type', DATA_INTEGER, 0, 'Bạn chưa chọn kiểu giảm giá')
        ->add('pro_value', DATA_INTEGER, 0)
        ->add('pro_condition_type', DATA_INTEGER, 0, 'Bạn chưa chọn điều kiện áp dụng')
        ->add('pro_updated_at', DATA_INTEGER, CURRENT_TIME)
        ->add('pro_created_at', DATA_INTEGER, CURRENT_TIME)
        ->add('pro_name', DATA_STRING, '')
        ->add('pro_description', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if ($Query->submitForm()) {
    
    //Nếu không nhập mã thì sẽ tự sinh ra mã ngẫu nhiên
    $pro_code   =   getValue('pro_code', GET_STRING, GET_POST, '');
    if ($pro_code == '') {
        $pro_code   =   $PromotionModel->generateRandomCode();
    } else {
        $pro_code   =   strtoupper($pro_code);

        // Kiểm tra độ dài
        if (strlen($pro_code) != 10) {
            $Query->addError('Mã khuyến phải cần có độ dài 10 ký tự');
        } else {
            //Kiểm tra xem có bị trùng mã ko
            $check      =   $PromotionModel->getInfoByCode($pro_code, "pro_id");
            if (!empty($check)) {
                $Query->addError('Mã khuyến mại này đã tồn tại');
            }
        }
    }
    $_POST['pro_code']  =   $pro_code;
    $Query->add('pro_code', DATA_STRING, $pro_code, 'Bạn chưa nhập mã khuyến mại');

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
    if ($daterange_int['from'] >= $daterange_int["to"] || $daterange_int["to"] < CURRENT_TIME) {
        $Query->addError('Thời gian bắt đầu/kết thúc không hợp lệ');
    }

    //Validate
    if ($Query->validate()) {

        $Query->add('pro_start_time', DATA_INTEGER, $daterange_int["from"])
            ->add('pro_end_time', DATA_INTEGER, $daterange_int["to"]);
        
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            
            reload_parent_window();
            
        }
    }
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
    $Query->generateVariable();
            
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->error)?>
    <?=$Form->text('Mã khuyến mại', 'pro_code', isset($pro_code) ? $pro_code : '', false, 'Gồm 12 ký tự chữ. Không bắt buộc.')?>
    <?=$Form->text('Tên', 'pro_name', $pro_name, true)?>
    <?=$Form->select('Kiểu giảm giá', 'pro_type', PromotionModel::TYPE_LABEL, $pro_type, true)?>
    <?=$Form->text('Giá trị giảm', 'pro_value', $pro_value, true)?>
    <?=$Form->dateRangePicker('Thời bắt đầu/kết thúc', 'daterange', array_get($_POST, 'daterange'), true)?>
    <?=$Form->select('Điều kiện áp dụng', 'pro_condition_type', PromotionModel::CONDITION_LABEL, $pro_condition_type, true)?>
    <?=$Form->textarea('Mô tả', 'pro_description', $pro_description)?>
    <?=$Form->button('Thêm mới')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>