<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('general_attribute_create');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Thêm mới thuộc tính';
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('attribute_name');
$Query->add('atn_group', DATA_INTEGER, 0, 'Bạn chưa chọn nhóm thuộc tính')
        ->add('atn_name', DATA_STRING, '', 'Bạn chưa nhập tên thuộc tính')
        ->add('atn_type', DATA_INTEGER, 0, 'Bạn chưa chọn kiểu thuộc tính')
        ->add('atn_alias_search', DATA_STRING, '')
        ->add('atn_require', DATA_INTEGER, 0)
        ->add('atn_show_filter', DATA_INTEGER, 0)
        ->add('atn_order', DATA_INTEGER, 0)
        ->add('atn_note', DATA_STRING, '')
        ->add('atn_active', DATA_INTEGER, 1);
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    $group  =   getValue('atn_group', GET_INT, GET_POST, 0);
    $param  =   getValue('atn_alias_search', GET_STRING, GET_POST, '');
    $filter =   getValue('atn_show_filter', GET_INT, GET_POST, 0);
    
    //Nếu cho lọc thì bắt buộc phải có param search
    if ($filter == 1 && empty($param)) {
        $Query->addError('Bạn chưa nhập param search');
    }

    //Trong cùng 1 group thì ko được có 2 attribute có cùng param search
    $check  =   $DB->query("SELECT atn_id
                            FROM attribute_name
                            WHERE atn_group = $group AND atn_alias_search = '$param'")
                            ->getOne();
    if (!empty($check)) {
        $Query->addError('Param search này đã tồn tại trong nhóm');
    }
    
    //Tự tăng số thứ tự của column lên để ko bị trùng
    $atn_column =   1;
    $row    =   $DB->query("SELECT MAX(atn_column) AS max_column
                            FROM attribute_name
                            WHERE atn_group = " . $group)
                            ->getOne();
    if (isset($row['max_column'])) {
        $atn_column =   (int)$row['max_column'] + 1;
    }
    $Query->add('atn_column', DATA_INTEGER, $atn_column);
    
    //Validate form
    if ($Query->validate()) {
        
        if ($DB->execute($Query->generateQueryInsert()) > 0) {
            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
            CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
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
    <?=$Layout->loadHead($page_title)?>
</head>
<body class="windows-thickbox">
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
    <?=$Form->showError($Query->getError())?>
    <?=$Form->textHTML('Lưu ý', 'Nếu thêm mới mà làm tăng số column lên > 15 thì phải update code ở Class AttributeModel. Tạo thêm xong vào DB sửa ID thành 9,14,15 bị thiếu.')?>
    <?=$Form->select('Nhóm thuộc tính', 'atn_group', $cfg_group, $atn_group, true)?>
    <?=$Form->text('Tên thuộc tính', 'atn_name', $atn_name, true)?>
    <?=$Form->select('Kiểu thuộc tính', 'atn_type', $cfg_type_attribute, $atn_type, true)?>
    <?=$Form->checkbox('Bắt buộc chọn giá trị', 'atn_require', $atn_require)?>
    <?=$Form->checkbox('Cho phép lọc tìm kiếm', 'atn_show_filter', $atn_show_filter)?>
    <?=$Form->text('Param search', 'atn_alias_search', $atn_alias_search, false)?>
    <?=$Form->text('Thứ tự', 'atn_order', $atn_order, false, 'Thứ tự hiển thị ở bên ngoài website')?>
    <?=$Form->textarea('Mô tả', 'atn_note', $atn_note)?>
    <?=$Form->button('Thêm mới')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>
