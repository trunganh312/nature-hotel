<?php
/**
 * Class UserModel
 * Version 1.0
 */

//Model
class UserModel extends Model {

	function __construct()
	{
		parent::__construct();
	}

    /**
     * User::getUserOfGroup()
     * Lấy danh sách User thuộc một nhóm đối tượng nào đó: Sale, MKT, Kế toán...
     * @param int $group: ID của group, được khai báo ở file constants
     * @param string $name_more: Gán thêm Email, Phòng ban... vào tên của User: [email, department]
     * @return [key => value]
     */
    function getUserOfGroup($group, $name_more = '') {

        $staffs =   [];

        $field_name =   "use_name";
        switch ($name_more) {
            case 'email':
                $field_name =   "CONCAT(use_name, ' - ', use_email)";
                break;
            
            case 'department':
                $field_name =   "CONCAT(use_name, ' - ', dep_name)";
                break;
        }
        $field_name .=  " AS name";
        
        $data   =   $this->DB->query("SELECT use_id, $field_name
                                        FROM users
                                        INNER JOIN users_department_users ON use_id = deac_account_id
                                        INNER JOIN users_department ON deac_department_id = dep_id
                                        WHERE dep_type = " . (int)$group . " AND dep_company_id = " . COMPANY_ID . " AND dep_active = 1 AND use_active = 1
                                        ORDER BY name")
                                        ->toArray();
        foreach ($data as $row) {
            if($name_more == 'department') {
                $name = explode('-',  $row['name']);
                $dep_name = explode('|--',  end($name));
                $staffs[$row['use_id']] =  $name[0] . ' - ' . end($dep_name);
            }else{
                $staffs[$row['use_id']] =  $row['name'];
            }
        }
        
        return $staffs;
    }
    

    /**
     * Lấy ra list các công ty của User Login
     */
    function getCompanyList($field = 'com_id, com_name', $return = 'key', $user_id = 0) {
        if ($user_id == 0)   $user_id =   ACCOUNT_ID;
        //Lấy ra các cty của User Login
        $data   =   $this->DB->query("SELECT $field
                                        FROM company
                                        INNER JOIN users_department ON(com_id = dep_company_id)
                                        INNER JOIN users_department_users ON(dep_id = deac_department_id)
                                        WHERE com_active = 1 AND deac_account_id = $user_id AND dep_is_default = 1
                                        ORDER BY com_group, com_name")
                                        ->toArray();
        
        //Nếu return về dạng row
        if ($return == 'row') {
            return $data;
        }

        //Return về dạng key=>value để show ở select
        $arr_company    =   [];
        foreach ($data as $row) {
            $arr_company[$row['com_id']]    =   $row['com_name'];
        }

        return $arr_company;
    }

    /**
     * Check user có thuộc company hay ko
     */
    function checkUserCompany($user_id, $company_id = COMPANY_ID) {
        if (!empty($this->DB->query("SELECT deac_account_id
                                    FROM users_department
                                    INNER JOIN users_department_users ON dep_id = deac_department_id
                                    WHERE deac_account_id = $user_id AND dep_company_id = $company_id AND dep_is_default = 1")->getOne())) {
            return true;
        }
        
        return false;
    }
	
	/**
	 * Lấy ra list các Group của User
	 */
    function getGroupIdsOfUser($user_id) {
        $arr_group  =   [];
        $data   =   $this->DB->query("SELECT grac_group_id
                                        FROM users_group_users
										INNER JOIN users_group ON grac_group_id = gro_id
                                        WHERE grac_account_id = " . $user_id . sql_company('gro_'))
                                        ->toArray();
        foreach ($data as $row) {
            $arr_group[]    =   $row['grac_group_id'];
        }

        return $arr_group;
    }
	
}