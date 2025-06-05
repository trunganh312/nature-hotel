<?

use src\Services\CommonService;

include('../../Core/Config/require_crm.php');

//Lấy các thông tin để xác định xem active cái gì, ở bảng nào
$field      =   getValue('field', GET_STRING, GET_GET, '');
$record_id  =   getValue('id');
$env        =   getValue('type', GET_STRING, GET_GET, '');

//Các trường có thể dùng cho việc Tick/Untick để kiểm tra tránh truyền sai trường gây lỗi câu query
$arr_field  =   ['mod_active'];

switch ($field) {
    //Module
    case 'mod_active':
        $Auth->checkPermission('module_edit');
        $table      =   'modules';
        $field_id   =   'mod_id';
        $arr_field  =   ['mod_active'];
        break;
    case 'modf_active':
        $Auth->checkPermission('module_edit_feature');
        $table      =   'modules_file';
        $field_id   =   'modf_id';
        $arr_field  =   ['modf_active'];
        break;
    
    //Place: Area, City, District, Ward
    case 'are_active':
    case 'are_hot':
        $Auth->checkPermission('place_edit');
        $table      =   'area';
        $field_id   =   'are_id';
        $arr_field  =   ['are_active', 'are_hot'];
        break;
    case 'cit_active':
    case 'cit_hot':
    case 'cit_show_menu':
        $Auth->checkPermission('place_edit');
        $table      =   'cities';
        $field_id   =   'cit_id';
        $arr_field  =   ['cit_hot', 'cit_show_menu', 'cit_active'];
        break;
    case 'dis_active':
    case 'dis_hot':
        $Auth->checkPermission('place_edit');
        $table      =   'districts';
        $field_id   =   'dis_id';
        $arr_field  =   ['dis_hot', 'dis_active'];
        break;
    
    //Service
    case 'ser_active':
        $Auth->checkPermission('hotel_service_edit');
        $table      =   'services';
        $field_id   =   'ser_id';
        $arr_field  =   ['ser_active'];
        break;

    //Surcharge
    case 'sur_active':
        $Auth->checkPermission('hotel_surcharge_edit');
        $table      =   'surcharges';
        $field_id   =   'sur_id';
        $arr_field  =   ['sur_active'];
        break;
    
    //Attribute
    case 'atn_active':
    case 'atn_hot':
    case 'atn_show_filter':
    case 'atn_require':
    case 'atn_join_meta':
    case 'atn_canonical':
    case 'atn_sennet_active':
        $Auth->checkPermission('general_attribute_edit');
        $table      =   'attribute_name';
        $field_id   =   'atn_id';
        $arr_field  =   ['atn_active', 'atn_hot', 'atn_show_filter', 'atn_require', 'atn_join_meta', 'atn_canonical', 'atn_sennet_active'];
        break;
    case 'atv_active':
    case 'atv_hot':
    case 'atv_fee':
        $Auth->checkPermission('general_attribute_value_edit');
        $table      =   'attribute_value';
        $field_id   =   'atv_id';
        $arr_field  =   ['atv_active', 'atv_hot', 'atv_fee'];
        break;
    
    //Company
    case 'com_active':
        $Auth->checkPermission('account_admin_company_edit');
        $table      =   'company';
        $field_id   =   'com_id';
        $arr_field  =   ['com_active'];
        break;
    
    //Department
    case 'dep_active':
        $Auth->checkPermission('account_admin_department_edit');
        $table  =   'admins_department';
        $field_id   =   'dep_id';
        $arr_field  =   ['dep_active'];
        break;

    //Admin
    case 'adm_active':
        $Auth->checkPermission('account_admin_admin_edit');
        $table      =   'admins';
        $field_id   =   'adm_id';
        $arr_field  =   ['adm_active'];
        break;
    
    //Admin group
    case 'gro_active':
    case 'gro_no_level':
        $Auth->checkPermission('account_admin_group_edit');
        $table  =   'admins_group';
        $field_id   =   'gro_id';
        $arr_field  =   ['gro_active', 'gro_no_level'];
        break;
    
    //Permission
    case 'per_active':
    case 'per_check_owner':
    case 'per_allow_leader':
    case 'per_company_config':
        if (!$Auth->cto) exit('Error');
        
        if ($env == 'admin') {
            $Auth->checkPermission('account_permission_admin_edit');
            $table  =   'admins_permission';
            $arr_field  =   ['per_active', 'per_check_owner', 'per_allow_leader'];
        } else {
            $Auth->checkPermission('account_permission_user_edit');
            $table  =   'users_permission';
            $arr_field  =   ['per_active', 'per_company_config'];
        }
        $field_id   =   'per_id';
        break;

    //User
    case 'use_active':
    case 'use_save_log_access':
        $Auth->checkPermission('user_edit');
        $table      =   'users';
        $field_id   =   'use_id';
        $arr_field  =   ['use_active', 'use_save_log_access'];
        break;

    //Hotel
    case 'hot_active':
    case 'hot_hot':
    case 'hot_top':
        $table      =   'hotel';
        $field_id   =   'hot_id';
        $arr_field  =   ['hot_active', 'hot_hot', 'hot_top'];
        break;
    
    //Loại phòng
    case 'roty_active':
        $table      =   'room_type';
        $field_id   =   'roty_id';
        $arr_field  =   ['roty_active'];
        break;
    
    //Loại phòng
    case 'bed_active':
        $table      =   'bed';
        $field_id   =   'bed_id';
        $arr_field  =   ['bed_active'];
        break;
    case 'doc_active':
        $table      =   'document';
        $field_id   =   'doc_id';
        $arr_field  =   ['doc_active'];
        break;

    default:
        exit('404!');
        break;
}

if (!in_array($field, $arr_field))  exit('Wrong!');

//Lấy giá trị hiện tại của trường
$record_info    =   $DB->query("SELECT * FROM " . $table . " WHERE " . $field_id . " = " . $record_id)->getOne();

/** Có một số trường hợp cần check quyền sau khi có thông tin bản ghi **/
switch ($field) {
    case 'bkto_call_checkin':
        $Auth->checkPermission('booking_tour_detail', $record_info['bkto_admin_process']);
        break;

    case 'bkho_call_checkin':
        $Auth->checkPermission('booking_hotel_detail', $record_info['bkho_admin_process']);
        break;
    
    case 'hot_active':
        $Auth->checkPermission('hotel_edit', $record_info['hot_user_create']);
        break;
    
    case 'hot_top':
        $Auth->checkPermission('hotel_tick_hot', $record_info['hot_user_create']);
        break;
}

if (!empty($record_info)) {
    $value  =   abs($record_info[$field] - 1);
    
    //Một số trường hợp cần lưu thời gian tick hoặc xử lý những nghiệp vụ riêng
    $sql_more   =   "";
    switch ($field) {
        case 'mobk_confirm':
            $sql_more   .=  ", mobk_time_confirm = " . CURRENT_TIME . ", mobk_admin_confirm = " . $Auth->id;
        break;
        case 'mosp_confirm':
            $sql_more   .=  ", mosp_time_confirm = " . CURRENT_TIME . ", mosp_admin_confirm = " . $Auth->id;
        break;
    }
    
    //Update giá trị mới
    if ($DB->execute("UPDATE $table SET $field = $value $sql_more WHERE $field_id = $record_id LIMIT 1") > 0) {
        CommonService::resJson();
        echo    generate_checkbox_icon($value);
        
        /** Lưu log **/
        $data_new   =   $DB->query("SELECT $field FROM $table WHERE $field_id = $record_id")->getOne();
        $Log->setTable($table)->genContent($record_info, $data_new)->createLog($record_id);
        
    } else {
        echo    'Error!';
    }
} else {
    echo    'Nothing!';
}

?>