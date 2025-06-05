<?php

use src\Facades\DB;
use src\Libs\Utils;
use src\Services\CommonService;

include('config_module.php');

$feature_active = getValue('active', GET_STRING, GET_GET, '');

$res = [];

// TODO: Lấy thông tin menu cho user thôi, create menu cho admin sau

// Lấy thông tin logo, tên khách sạn
if (Utils::auth()->envAdmin()) {
    $res["logo"] = $cfg_path_theme_image . 'logo-h.png';
} else {
    $res["logo"] = $Router->srcCompany(Utils::auth()->company['com_logo_crm'], SIZE_SMALL);
    if (Utils::auth()->work_space == MODULE_HOTEL) {
        $res["hotel"] = Utils::auth()->hotel['hot_name'];
    }
}

//Chỉ lấy ra các module và các file tính năng mà user này có quyền
$sql_module =   "mod_active = 1";
//Lấy ra các module thuộc Env đang sử dụng
if (Utils::auth()->envAdmin()) {
    $sql_module .=  " AND mod_env = " . ADMIN_CRM;
} else if (Utils::auth()->envUser()) {
    $sql_module .=  " AND mod_env = " . ADMIN_USER . " AND mod_group IN(" . implode(',', array_merge($cfg_module_common, [Utils::auth()->company['com_group']])) . ")";
} else {
    $sql_module .=  " AND mod_env = 0";
}

// Lấy thông tin menu
$list_feature = $modules = [];

//Nếu ko phải là super account thì sẽ lấy ra list các tính năng được phân quyền
if (!Utils::auth()->isSuperAccount()) {
    //Tùy theo môi trường nào mà sẽ chỉ lấy ra các module của môi trường đó
    if (Utils::auth()->envAdmin()) {
        $list_feature   =   DB::query("SELECT modf_id, modf_module_id, modf_parent_id
                        FROM admins_permission
                        INNER JOIN modules_file ON (per_feature_id = modf_id)
                        INNER JOIN admins_permission_groups ON (per_id = pega_permission_id)
                        INNER JOIN admins_group_admins ON (pega_group_id = grac_group_id)
                        WHERE per_active = 1 AND per_feature_id > 0 AND grac_account_id = " . Utils::auth()->id)
            ->toArray();
    } else if (Utils::auth()->envUser()) {
        $list_feature   =   DB::query("SELECT modf_id, modf_module_id, modf_parent_id
                        FROM users_permission_groups
                        INNER JOIN users_permission ON (pega_permission_id = per_id)
                        INNER JOIN modules_file ON (per_feature_id = modf_id)
                        WHERE per_active = 1 AND per_feature_id > 0
                            AND pega_group_id IN(SELECT grac_group_id
                                                FROM users_group_users
                                                INNER JOIN users_group ON(grac_group_id = gro_id)
                                                WHERE gro_active = 1 AND grac_account_id = " . Utils::auth()->id . " AND gro_company_id = " . COMPANY_ID . ")")
            ->toArray();
    }

    /**
     * Xử lý mảng để chia thành 3 phần:
     * Mảng chứa ID các module để query rồi sort theo mod_order
     * Mảng chứa các ID của Nhóm tính năng và Tính năng ko thuộc nhóm
     * Mảng chứa các ID thuộc các nhóm tính năng
     */
    $arr_module =   [0];    //Mặc định sẽ ko có quyền ở module nào
    $arr_child  =   []; //Mảng lưu những feature mà nằm độc lập (ko thuộc nhóm nào)
    $arr_group  =   []; //Mảng lưu riêng các nhóm chứa ID của các tính năng thuộc nhóm đó
    foreach ($list_feature as $row) {

        //Lấy ra các ID của module để query lại và sort theo mod_order
        if (!in_array($row['modf_module_id'], $arr_module))  $arr_module[]   =   $row['modf_module_id'];

        if (!isset($arr_child[$row['modf_module_id']])) $arr_child[$row['modf_module_id']]  =   [];

        /**
         * Lấy ra list các ID của Tính năng ko thuộc nhóm nào, và ID của các nhóm, lưu vào mảng $arr_child
         * Gom các feature thuộc nhóm nào đó vào với nhau để query trong nhóm đó
         */
        if ($row['modf_parent_id'] > 0) {

            if (!in_array($row['modf_parent_id'], $arr_child[$row['modf_module_id']])) {
                $arr_child[$row['modf_module_id']][]    =   $row['modf_parent_id'];
            }
            $arr_group[$row['modf_parent_id']][]    =   $row['modf_id'];
    
        } else {
            $arr_child[$row['modf_module_id']][]    =   $row['modf_id'];
        }
    }

    $sql_module .=  " AND mod_id IN (" . CommonService::implode($arr_module) . ")";
}

$arr_module =   [0];    //Mặc định sẽ ko có quyền ở module nào
$arr_child  =   []; //Mảng lưu những feature mà nằm độc lập (ko thuộc nhóm nào)
$arr_group  =   []; //Mảng lưu riêng các nhóm chứa ID của các tính năng thuộc nhóm đó
foreach ($list_feature as $row) {

    //Lấy ra các ID của module để query lại và sort theo mod_order
    if (!in_array($row['modf_module_id'], $arr_module))  $arr_module[]   =   $row['modf_module_id'];

    if (!isset($arr_child[$row['modf_module_id']])) $arr_child[$row['modf_module_id']]  =   [];

    /**
     * Lấy ra list các ID của Tính năng ko thuộc nhóm nào, và ID của các nhóm, lưu vào mảng $arr_child
     * Gom các feature thuộc nhóm nào đó vào với nhau để query trong nhóm đó
     */
    if ($row['modf_parent_id'] > 0) {

        if (!in_array($row['modf_parent_id'], $arr_child[$row['modf_module_id']])) {
            $arr_child[$row['modf_module_id']][]    =   $row['modf_parent_id'];
        }
        $arr_group[$row['modf_parent_id']][]    =   $row['modf_id'];
    } else {
        $arr_child[$row['modf_module_id']][]    =   $row['modf_id'];
    }
}

//Lấy ra các Module có quyền
$modules  =  $DB->query("SELECT mod_id, mod_name, mod_env, mod_group, mod_folder, mod_icon
                        FROM modules
                        WHERE {$sql_module}
                        ORDER BY mod_env, mod_group, mod_order")
    ->toArray();
$menus = $menu_open_keys = $menu_selected_keys = [];
foreach ($modules as $module) {
    //Chỉ CTO mới quản lý các cấu hình hệ thống
    if (Utils::auth()->envAdmin() && $module['mod_folder'] == 'system' && !Utils::auth()->cto) {
        continue;
    }

    $menus[$module["mod_id"]] = [
        "id" => $module["mod_id"],
        "key" => 'm_'. $module["mod_id"],
        "label" => $module["mod_name"],
        "title" => $module["mod_name"],
        "icon" => $module["mod_icon"],
        "env" => $module["mod_env"],
        "group" => $module["mod_group"],
        "children" => []
    ];

    //Lấy ra các menu con và các nhóm đã được lọc ra từ bên trên và gán vào mảng $arr_child
    $data_child   =  DB::query("SELECT modf_id, modf_name, modf_file, modf_is_parent
                            FROM modules_file
                            WHERE modf_module_id = " . $module['mod_id'] . " AND modf_active = 1 AND modf_parent_id = 0" . (!Utils::auth()->isSuperAccount() ? " AND modf_id IN(". CommonService::implode(array_get($arr_child, $module['mod_id'], [])) .")" : "") . "
                            ORDER BY modf_order")
        ->toArray();

    foreach ($data_child as $child) {
        $menus[$module["mod_id"]]["children"][$child["modf_id"]] = [
            "id"    => $child["modf_id"],
            "key"   => 'f_'. $child["modf_id"],
            "label"  => $child["modf_name"],
            "title"  => $child["modf_name"],
            "route"  => $child["modf_file"],
            "children"   => []
        ];
        //Nếu là nhóm tính năng
        if ($child['modf_is_parent'] == 1) {
            //Lấy ra các tính năng của nhóm
            $menu   =   DB::pass()->query("SELECT modf_id, modf_name, modf_file, per_alias
                                    FROM modules_file
                                    INNER JOIN ". Utils::auth()->table ."_permission ON modf_id = per_feature_id
                                    WHERE modf_module_id = " . $module['mod_id'] . " AND modf_active = 1 AND modf_parent_id = " . $child['modf_id'] . (!Utils::auth()->isSuperAccount() ? " AND modf_id IN(" . implode(',', $arr_group[$child['modf_id']]) . ")" : "") . "
                                    ORDER BY modf_order")
                ->toArray();

            foreach ($menu as $m) {
                $menus[$module["mod_id"]]["children"][$child["modf_id"]]["children"][$m["modf_id"]] = [
                    "id"    => $m["modf_id"],
                    "key"   => 'f_'. $m["modf_id"],
                    "label" => $m["modf_name"],
                    "title" => $m["modf_name"],
                    "route"  => $m["modf_file"],
                    "active"=> $feature_active == $m['per_alias'],
                ];
                if ($feature_active == $m['per_alias']) {
                    $menu_open_keys = [
                        $menus[$module["mod_id"]]["key"],
                        $menus[$module["mod_id"]]["children"][$child["modf_id"]]['key']
                    ];
                    $menu_selected_keys = [$menus[$module["mod_id"]]["children"][$child["modf_id"]]["children"][$m["modf_id"]]['key']];
                }
            }
        } else {
            //Lấy ra Alias quyền của menu để show open
            $feature  = DB::pass()->query("SELECT per_alias
                                FROM {$Auth->table}_permission
                                WHERE per_active = 1 AND per_feature_id = " . $child['modf_id'])
                ->getOne();

                if(isset($feature['per_alias'])) {
                    if ($feature_active == $feature['per_alias']) {
                        $menu_open_keys = [
                            $menus[$module["mod_id"]]["key"],
                        ];
                        $menu_selected_keys = [$menus[$module["mod_id"]]["children"][$child["modf_id"]]['key']];
                    }
                }
        }

        //Nếu không có menu con thì xóa key children
        if (empty($menus[$module["mod_id"]]["children"][$child["modf_id"]]["children"])) {
            unset($menus[$module["mod_id"]]["children"][$child["modf_id"]]["children"]);
        } else {
            $menus[$module["mod_id"]]["children"][$child["modf_id"]]["children"] = array_values($menus[$module["mod_id"]]["children"][$child["modf_id"]]["children"]);
        }
    }
    $menus[$module["mod_id"]]["children"] = array_values($menus[$module["mod_id"]]["children"]);
}
$res['menu_open_keys'] = $menu_open_keys;
$res['menu_selected_keys'] = $menu_selected_keys;
$res['menus'] = array_values($menus);
CommonService::resJson($res);