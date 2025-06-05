<?

use src\Facades\DB;
use src\Services\CommonService;

include('config_module.php');
$Auth->checkPermission('general_attribute_value_list');

/** --- Khai báo một số biến cơ bản --- **/
$table          =   "attribute_value";
$field_id       =   "atv_id";
$page_title     =   'Danh sách các giá trị của thuộc tính';
$has_edit       =   $Auth->hasPermission('general_attribute_value_edit');
$has_create     =   $Auth->hasPermission('general_attribute_value_create');
//Lấy thông tin của attribute_name
$atn_id =   getValue('id');
$attribute_info =   $DB->query("SELECT * FROM attribute_name WHERE atn_id = " . $atn_id)->getOne();
if (empty($attribute_info)) {
    exitError('Dữ liệu này không tồn tại!');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable($table, $field_id);
$Table->column('atv_name', 'Tên giá trị', TAB_TEXT, true, true)
        ->column('atv_value_hexa', 'Value', TAB_NUMBER, false, true)
        ->column('atv_icon', 'Icon', TAB_TEXT)
        ->column('atv_order', 'Thứ tự', TAB_NUMBER, false, true);
if ($has_edit) {
    $Table->column('atv_hot', 'Hot', TAB_CHECKBOX)
            ->column('atv_active', 'Act', TAB_CHECKBOX)
            ->addED(true)
            ->setEditFileName('attribute_value_edit.php');
}
$Table->addSQL("SELECT * FROM attribute_value WHERE atv_attribute_id = " . $atn_id . " ORDER BY atv_active DESC, atv_order, atv_name");

$rows = DB::pass()->query($Table->sql_table)->toArray();


$res = [
    'rows' => $rows,
    'permissions' => [
        'hasEdit' => $has_edit,
        'hasCreate' => $has_create,
    ],
    "pagination" => [
        "total"   => $Table->getTotalRecord(),
        "current" => get_current_page('page'),
        "pageSize" => $Table->getPageSize()
    ],
];


if(getValue('json')) {
    CommonService::resJson($res);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead($page_title)?>
    <style>
        .title_note {
            text-align: right;
        }
    </style>
</head>
<body class="windows-thickbox listing_page">
    <?
    $Layout->setPopup(true)
            ->setNoteTitle('<a href="attribute_value_create.php?id=' . $atn_id . '&url=' . base64_encode($_SERVER['REQUEST_URI']) . '" class="btn_popup_title" title="Thêm mới giá trị thuộc tính"><i class="fas fa-plus-circle"></i> Thêm mới</a>')
            ->header($page_title);
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
        <?=$Table->createTR($stt, $row[$field_id]);?>
        <?=$Table->showFieldText('atv_name')?>
        <?=$Table->showFieldNumber('atv_value_hexa')?>
        <td class="text-center">
            <i class="<?=$row['atv_icon']?>"></i>
        </td>
        <?=$Table->showFieldNumber('atv_order')?>
        <?
        if ($has_edit) {
            echo    $Table->showFieldCheckbox('atv_hot');
            echo    $Table->showFieldCheckbox('atv_active');
        }
        ?>
        <?=$Table->closeTR($row[$field_id])?>
        <?
    }
    ?>
    <?=$Table->closeTable();?>
    <?
    $Layout->footer();
    ?>    
</body>
</html>

