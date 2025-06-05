<?
include('config_module.php');
$Auth->checkPermission('account_admin_department_list_member_edit');

$depart_id  =   getValue('department', GET_INT, GET_POST);
$account_id =   getValue('member', GET_INT, GET_POST);
$response   =   [
    'status'    =>  0,
    'error'     =>  ''
];

if ($DB->execute("DELETE FROM admins_department_admins WHERE deac_department_id = $depart_id AND deac_account_id = $account_id LIMIT 1") > 0) {
    //Nếu đây đang là Manager của Department thì phải xóa ở bảng admins_department nữa
    $DB->execute("UPDATE admins_department SET dep_manager_id = 0 WHERE dep_id = $depart_id AND dep_manager_id = $account_id LIMIT 1");
    $response['status'] =   1;
} else {
    $response['error']  =   'Nhân viên này không thuộc Phòng/Ban mà bạn đang xử lý!';
}
echo    json_encode($response);
?>