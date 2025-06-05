<?

use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('module_create_feature');

/** --- Khai báo một số biến cơ bản --- **/
$page_title =   'Thêm mới tính năng cho Module';
$module_id  =   getValue('module_id');
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('modules_file');
$Query->add('modf_module_id', DATA_INTEGER, $module_id)
        ->add('modf_name', DATA_STRING, '', 'Bạn chưa nhập tên tính năng')
        ->add('modf_parent_id', DATA_INTEGER, 0)
        ->add('modf_file', DATA_STRING, '')
        ->add('modf_order', DATA_INTEGER, 0)
        ->add('modf_active', DATA_INTEGER, 1)
        ->add('modf_note', DATA_STRING, '');
/** --- End of Class query để insert dữ liệu --- **/

/** --- Submit form --- **/
if (CommonService::isPost()) {
    
    //Validate form
    if ($Query->validate()) {
        
        if ($DB->execute($Query->generateQueryInsert()) >= 0) {
            //Nếu chọn Parent thì update is_parent cho
            $modf_parent_id =   getValue('modf_parent_id', GET_INT, GET_POST);
            if ($modf_parent_id > 0) {
                $DB->execute("UPDATE modules_file SET modf_is_parent = 1 WHERE modf_id = $modf_parent_id LIMIT 1");
            }

            CommonService::resJson();
        } else {
            $Query->addError('Đã có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
        }
    }else {
        CommonService::resJson($Query->getError(), STATUS_INACTIVE, 'Error!');
    }
}
/** --- End of Submit form --- **/

//Lấy ra các menu parent
$parent =   $DB->query("SELECT modf_id, modf_name
                        FROM modules_file
                        WHERE modf_module_id = " . $module_id . " AND modf_parent_id = 0 AND modf_file = ''
                        ORDER BY modf_name")->toArray();
$modf_parents   =   [];
foreach ($parent as $row) {
    $modf_parents[$row['modf_id']]  =   $row['modf_name'];
}

$res = [
    'others'    =>  []
];

$res['others']['modf_parents'] = [];
foreach ($modf_parents as $k => $v) {
    $res['others']['modf_parents'][] = [
        "value" => $k,
        "label" => $v
    ];
}

if(getValue('json')) {
    CommonService::resJson($res);
}

?>

<!DOCTYPE html>
<html lang="en">
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
    
    //Lấy ra các menu parent
    $parent =   $DB->query("SELECT modf_id, modf_name
                            FROM modules_file
                            WHERE modf_module_id = " . $module_id . " AND modf_parent_id = 0 AND modf_file = ''
                            ORDER BY modf_name")->toArray();
    $modf_parents   =   [];
    foreach ($parent as $row) {
        $modf_parents[$row['modf_id']]  =   $row['modf_name'];
    }
    
    //Lấy ra giá trị lớn nhất hiện tại của mod_order để tự động cho order +1
    $modf_order =   1;                
    $row    =   $DB->query("SELECT MAX(modf_order) AS max_order FROM modules_file WHERE modf_module_id = " . $module_id)->getOne();
    if (isset($row['max_order'])) {
        $modf_order  =   (int)$row['max_order'] + 1;
    }
    
    //Show form data
    $Form   =   new GenerateForm;
    ?>
    <?=$Form->createForm()?>
    <?=$Form->showError($Query->getError())?>
    <?=$Form->text('Tên tính năng/nhóm', 'modf_name', $modf_name, true)?>
    <?=$Form->select('Thuộc nhóm', 'modf_parent_id', $modf_parents, $modf_parent_id)?>
    <?=$Form->text('File thực thi', 'modf_file', $modf_file, false, '<br>Nếu là tính năng xuất hiện ở menu trái thì bắt buộc phải nhập. Nếu là nhóm hoặc tính năng ko xuất hiện ở menu trái thì để trống.')?>
    <?=$Form->text('Thứ tự', 'modf_order', $modf_order)?>
    <?=$Form->textarea('Mô tả', 'modf_note', $modf_note)?>
    <?=$Form->button('Thêm mới')?>
    <?=$Form->closeForm()?>
    <?
    $Layout->footer();
    ?>
</body>
</html>