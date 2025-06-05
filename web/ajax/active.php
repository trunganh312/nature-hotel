<?
include('../Core/Config/require_web.php');

//Lấy các thông tin để xác định xem active cái gì, ở bảng nào
$field      =   getValue('field', GET_STRING, GET_GET, '');
$record_id  =   getValue('id');

//Các trường có thể dùng cho việc Tick/Untick để kiểm tra tránh truyền sai trường gây lỗi câu query
$arr_field  =   ['tou_active'];
$sql_where  =   "";

switch ($field) {
    
    case 'tou_active':
    case 'tou_hot':
        $table      =   'tour';
        $field_id   =   'tou_id';
        $arr_field  =   ['tou_active', 'tou_hot'];
        break;
        
    default:
        exit('404!');
        break;
}

if (!in_array($field, $arr_field))  exit('Wrong!');

//Lấy giá trị hiện tại của trường
$row    =   $DB->query("SELECT " . $field . " FROM " . $table . " WHERE " . $field_id . " = " . $record_id . $sql_where)->getOne();

if (!empty($row)) {
    $value  =   abs($row[$field] - 1);
    
    //Update giá trị mới
    if ($DB->execute("UPDATE " . $table . " SET " . $field . " = " . $value . " WHERE " . $field_id . " = " . $record_id . " LIMIT 1") > 0) {
        echo    generate_checkbox_icon($value);
    } else {
        echo    'Error!';
    }
} else {
    echo    '404!';
}

?>