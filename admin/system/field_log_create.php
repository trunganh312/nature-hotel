<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('system_create_field_log');

/** --- Khai báo một số biến cơ bản --- **/
$table      =   'field_log';
$field_id   =   'fie_id';
$page_name  =   'Thêm mới trường lưu log';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery($table);
$Query->add('fie_table_id', DATA_INTEGER, 0, 'Bạn chưa nhập tên bảng')
        ->add('fie_field', DATA_STRING, '', 'Bạn chưa nhập tên trường')
        ->add('fie_name', DATA_STRING, '', 'Bạn chưa nhập tên lưu log của trường')
        ->add('fie_type', DATA_INTEGER, 0, 'Bạn chưa chọn kiểu dữ liệu')
        ->add('fie_table_target', DATA_STRING, '')
        ->add('fie_field_id', DATA_STRING, '')
        ->add('fie_field_value', DATA_STRING, '')
        ->add('fie_variable', DATA_STRING, '')
        ->add('fie_description', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {

    //Trong cùng 1 table thì ko có 2 field trùng tên
    $table_id   =   getValue('fie_table_id', GET_INT, GET_POST);
    $field_name =   getValue('fie_field', GET_STRING, GET_POST);
    if (!empty($DB->query("SELECT fie_id FROM field_log WHERE fie_field = '" . $field_name . "' AND fie_table_id = $table_id")->getOne())) {
        $Query->addError('Tên trường đã tồn tại trong bảng');
    }
    
    //Validate form
    if ($Query->validate()) {
        
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
        }
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
}
/** --- End of Submit form --- **/

//Check type để show các input cho phù hợp với từng loại dữ liệu
$fie_type       =   getValue('fie_type', GET_INT, GET_POST, FIELD_TEXT);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead($page_name)?>
    <style>
        <?
        if ($fie_type == FIELD_DATABASE) {
            ?>
            .control_fie_table_target, .control_fie_field_id, .control_fie_field_value {
                display: block;
            }
            .control_fie_variable {
                display: none;
            }
            <?
        } else if ($fie_type == FIELD_CONSTANT) {
            ?>
            .control_fie_table_target, .control_fie_field_id, .control_fie_field_value {
                display: none;
            }
            .control_fie_variable {
                display: block;
            }
            <?
        } else {
            ?>
            .control_fie_table_target, .control_fie_field_id, .control_fie_field_value, .control_fie_variable {
                display: none;
            }
            <?
        }
        ?>
    </style>
</head>
<body class="windows-thickbox">
    <?
    $Layout->setPopup(true)->header($page_name);
    ?>
	<?
    //Tạo ra các biến sẵn để fill vào form dựa vào các hàm add trường dữ liệu ở trên (GenerateQuery)
    $Query->generateVariable();
    
    //Giữ nguyên tên bảng để ko phải nhập nhiều lần khi nhập nhiều trường cùng 1 bảng liên tục
    $fie_table_id   =   getValue('fie_table_id', GET_INT, GET_POST, getValue('table'));

    //Lấy ra List table lưu log
    $list_table =   $Model->getListData('table_log', 'talo_id, talo_table', '', 'talo_table');
    if ($fie_type == 0) $fie_type   =   FIELD_TEXT;
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->select2('Bảng', 'fie_table_id', $list_table, $fie_table_id, true)?>
    <?=$Form->text('Tên trường', 'fie_field', $fie_field, true)?>
    <?=$Form->text('Tên lưu log', 'fie_name', $fie_name, true)?>
    <?=$Form->select('Kiểu dữ liệu', 'fie_type', $cfg_field_type, $fie_type, true)?>
    <?=$Form->text('Bảng lấy dữ liệu', 'fie_table_target', $fie_table_target, false, 'Nếu kiểu dữ liệu là Database')?>
    <?=$Form->text('Trường ID', 'fie_field_id', $fie_field_id, false, 'Trường ID của bảng lấy dữ liệu')?>
    <?=$Form->text('Trường giá trị', 'fie_field_value', $fie_field_value, false, 'Trường lấy ra text hiển thị của bảng lấy dữ liệu')?>
    <?=$Form->text('Biến lấy giá trị', 'fie_variable', $fie_variable, false, 'Nếu kiểu dữ liệu là Constant. Ko bao gồm $.')?>
    <?=$Form->text('Mô tả', 'fie_description', $fie_description)?>
    <?=$Form->button('Thêm mới')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
    <script>
        $('#fie_type').change(function() {
            var _value = $(this).val();
            if (_value == <?=FIELD_DATABASE?>) {
                $('.control_fie_table_target, .control_fie_field_id, .control_fie_field_value').show();
                $('.control_fie_variable').hide();
            } else if (_value == <?=FIELD_CONSTANT?>) {
                $('.control_fie_table_target, .control_fie_field_id, .control_fie_field_value').hide();
                $('.control_fie_variable').show();
            } else {
                $('.control_fie_table_target, .control_fie_field_id, .control_fie_field_value, .control_fie_variable').hide();
            }
        });
    </script>
</body>
</html>
