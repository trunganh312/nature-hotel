<?
include('config_module.php');
$Auth->checkPermission('mkt_voucher_edit');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'voucher';
$field_id   =   'vou_id';
$page_title =   'Sửa thông tin voucher';
$record_id  =   getValue('id');
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();
if (empty($record_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('vou_last_modify', DATA_INTEGER, CURRENT_TIME)
        ->add('vou_title', DATA_STRING, '')
        ->add('vou_message', DATA_STRING, '')
        ->add('vou_description', DATA_STRING, '');
$Query->setRemoveHTML(false);
/** --- End of Class query để insert dữ liệu --- **/

//Giữ lại giá trị của range KM nếu submit form lỗi
$range_input    =   '';

/** --- Submit form --- **/
if ($Query->submitForm()) {
    
    /** --- Lấy kiểu giảm giá để xử lý từng trường hợp cụ thể --- **/
    $vou_type   =   getValue('vou_type', GET_INT, GET_POST, 0);
    $vou_value  =   getValue('vou_value', GET_DOUBLE, GET_POST, 0);
    
    if ($vou_type == VOUCHER_TYPE_MONEY || $vou_type == VOUCHER_TYPE_PERCENT) {
        if ($vou_value <= 0)    $Query->addError('Bạn chưa nhập giá trị giảm giá');
    }
    
    //Không cho phép giảm quá 100%
    if ($vou_type == VOUCHER_TYPE_PERCENT) {
        $vou_value  =   getValue('vou_value', GET_INT, GET_POST, 0);
        if ($vou_value > 100)   $Query->addError('Giá trị giảm giá vượt quá 100%');
    }

    /** --- Nếu giảm giá theo mức tiền --- **/
    if ($vou_type == VOUCHER_TYPE_RANGE) {
        $range  =   [];
        for ($i = 1; $i <= 5; $i++) {
            $min        =   getValue('min_' . $i, GET_INT, GET_POST, 0);
            $max        =   getValue('max_' . $i, GET_INT, GET_POST, 0);
            $discount   =   getValue('discount_' . $i, GET_INT, GET_POST, 0);
            if ($min >= 0 && $max > $min && $discount > 0) {
                $range[]    =   [
                                'min'       =>  $min,
                                'max'       =>  $max,
                                'discount'  =>  $discount
                                ];
            }
        }
        if (!empty($range)) {
            $vou_range_discount =   json_encode($range);
            $range_input    =   $vou_range_discount;
            $Query->add('vou_range_discount', DATA_STRING, $vou_range_discount);
        } else {
            $Query->addError('Bạn chưa nhập khoảng các mức giảm giá');
        }
    } else {
        $Query->add('vou_range_discount', DATA_STRING, '');
    }
    
    $Query->add('vou_type', DATA_INTEGER, 0, 'Bạn chưa chọn kiểu giảm giá');
    $Query->add('vou_value', DATA_INTEGER, 0);
    $Query->add('vou_quantity', DATA_INTEGER, 1);
    
    //Thời hạn sử dụng
    $time_expire        =   getValue('time_expire', GET_STRING, GET_POST, '');
    $vou_time_expire    =   str_totime($time_expire) + 86399;   //Cho đến cuối ngày
    $_POST['vou_time_expire']   =   $vou_time_expire;
    $Query->add('vou_time_expire', DATA_INTEGER, 0, 'Bạn chưa nhập hạn sử dụng');
    
    //Validate
    if ($Query->validate($field_id, $record_id)) {
        
        $update =   $DB->execute($Query->generateQueryUpdate($field_id, $record_id));
        set_session_toastr();
        //Quay lại trang DS
        reload_parent_window();
        
    }
}
/** --- End of Submit form --- **/

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
    <style>
        .form-group .form_input .table-range {
            width: auto;
        }
        .form-group .form_input .table-range td {
            padding: 3px 0;
        }
        .form-group .form_input .table-range .form-control {
            text-align: right;
            width: 120px;
            min-width: auto;
        }
    </style>
</head>
<body class="windows-thickbox">
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
    <?=$Form->textHTML('Mã Voucher', $vou_code)?>
    <?=$Form->select('Kiểu giảm giá', 'vou_type', $cms_cfg_voucher_type, $vou_type, true)?>
    <?=$Form->text('Giá trị giảm', 'vou_value', $vou_value, true)?>
    <div class="form-group <?=($vou_type == VOUCHER_TYPE_RANGE ? '' : ' hide')?>" id="range_discount">
        <label><span class="mark-require">*</span> <span style="color: #666;">Các mức giảm giá</span></label>
        <div class="form_input">
            <table class="table-range">
                <?
                if (!empty($range_input))   $vou_range_discount =   $range_input;
                
                if ($vou_range_discount != '') {
                    
                    $range  =   json_decode($vou_range_discount, true);
                    
                    $stt    =   0;
                    foreach ($range as $r) {
                        $stt++;
                        ?>
                        <tr>
                            <td>Từ&nbsp;<input type="text" name="min_<?=$stt?>" value="<?=$r['min']?>" class="form-control" /></td>
                            <td>&nbsp;đến&nbsp;<input type="text" name="max_<?=$stt?>" value="<?=$r['max']?>" class="form-control" />:</td>
                            <td>&nbsp;Giảm&nbsp;<input type="text" name="discount_<?=$stt?>" value="<?=$r['discount']?>" class="form-control" /></td>
                            <td><i class="form-add-note">VNĐ</i></td>
                        </tr>
                        <?
                    }
                    //Tối đa 5 khoảng giảm giá
                    $stt++;
                    for ($i = $stt; $i <= 5; $i++) {
                        ?>
                        <tr>
                            <td>Từ&nbsp;<input type="text" name="min_<?=$i?>" class="form-control" /></td>
                            <td>&nbsp;đến&nbsp;<input type="text" name="max_<?=$i?>" class="form-control" />:</td>
                            <td>&nbsp;Giảm&nbsp;<input type="text" name="discount_<?=$i?>" class="form-control" /></td>
                            <td><i class="form-add-note">VNĐ</i></td>
                        </tr>
                        <?
                    }
                } else {
                    //Tối đa 5 khoảng giảm giá
                    for ($i = 1; $i <= 5; $i++) {
                        ?>
                        <tr>
                            <td>Từ&nbsp;<input type="text" name="min_<?=$i?>" class="form-control" /></td>
                            <td>&nbsp;đến&nbsp;<input type="text" name="max_<?=$i?>" class="form-control" />:</td>
                            <td>&nbsp;Giảm&nbsp;<input type="text" name="discount_<?=$i?>" class="form-control" /></td>
                            <td><i class="form-add-note">VNĐ</i></td>
                        </tr>
                        <?
                    }
                }
                ?>
            </table>
        </div>
    </div>
    <?=$Form->text('Số lượng', 'vou_quantity', $vou_quantity, true, 'Nếu là mã chỉ dùng 1 lần thì nhập 1')?>
    <?=$Form->datePicker('Hạn sử dụng', 'time_expire', date('d/m/Y', $vou_time_expire), true)?>
    <?=$Form->text('Voucher Title', 'vou_title', $vou_title, false, 'Dùng hiển thị trên website khi giảm giá theo mức tiền')?>
    <?=$Form->text('Voucher Message', 'vou_message', $vou_message)?>
    <?=$Form->textarea('Giới thiệu', 'vou_description', $vou_description)?>
    <?=$Form->button('Cập nhật')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
    <script>
        $(function() {
            $('#vou_type').change(function() {
                var value = $(this).val();
                if (value == <?=VOUCHER_TYPE_RANGE?>) {
                    $('#range_discount').show();
                } else {
                    $('#range_discount').hide();
                }
            });
        });
    </script>
</body>
</html>