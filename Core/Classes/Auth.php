<?php

use src\Libs\Router;
use src\Models\ConfigUser;
use src\Models\Hotel;

class Auth extends Model {

    public  $table  =   null;
    private $prefix =   null;
    private $path_login =   '/login';

    private $logged =   false;
    public  $info   =   [];
    public  $id     =   0;
    public  $name   =   '';
    public  $cto    =   false;
    public  $superAccount   =   false;
    public  $single =   false;  //Đánh dấu User chỉ thuộc 1 công ty hay thuộc nhiều công ty
    public  $work_space =   0;  //Đang làm việc ở môi trường HMS hay TA
    private $all_permission =   []; //Tất cả các quyền của Account login
    private $list_staff     =   []; //Lưu các staff của 1 account để cache ko cần phải query nhiều lần
    public  $alias_menu =   ''; //Alias của quyền thuộc tính năng menu bên trái để show/open menu ra
    public  $config_user    =   []; // Mảng cấu hình của khách sạn/ công ty

    //2 đối tượng sẽ làm việc nếu là User
    public  $company    =   [];
    public  $hotel      =   [];

    const   PWD_DEFAULT     =   'S112233';
    const   SECRET_KEY      =   'NjuW18$LI';

    /**
     * @param string $email, $password
     * @param bool $redirect: Có redirect về trang login hay ko
     */
    public function __construct($email = '', $password = '', $redirect = true) {
        
        parent::__construct();

        $this->table    =   'admins';
        $this->prefix   =   'adm_';
        $this->path_login = '/login.php';
        
        if(isset($_SESSION['login_error'])) unset($_SESSION['login_error']);
        
        // dd($_SESSION);
        //Nếu đã login rồi thì lấy thông tin từ session 
        if (empty($email) && empty($password)) {
            
            $logged_id  =   getValue('logged_id', GET_INT, GET_SESSION);

            $row =  $this->DB->pass(false)->query("SELECT *
                                    FROM {$this->table}
                                    WHERE {$this->prefix}active = 1 AND {$this->prefix}id = $logged_id")
                                    ->getOne();
            if (!empty($row)) {
                
                if ($this->checkPasswordSession($row[$this->prefix . 'password'])) {

                    /** --- Nếu là CRM thì tạo TK cho nhân viên nên sẽ ko cho để mật khẩu mặc định --- **/
                    if ($this->table == 'admins' && $this->generatePassword(self::PWD_DEFAULT, $row[$this->prefix . 'random']) == $row[$this->prefix . 'password']) {
                        $exp    =   explode('?', $_SERVER['REQUEST_URI']);
                        $exp    =   explode('/', $exp[0]);
                        $file   =   end($exp);
                        
                        if ($file != 'profile.php' && $file != 'logout.php') {    
                            redirect_url('/profile/profile.php');
                        }
                    }

                    /** --- Nếu thông tin đăng nhập chính xác thì generate ra thông tin của Admin để sử dụng --- **/
                    $this->generateInfoLogged($row);
                    
                    //Lưu thời gian last_online
                    $fake_login = getValue('fake_login', GET_INT, GET_SESSION, 0);
                    if ($fake_login <= 0 || !isset($fake_login)) {
                        $this->DB->pass(false)->execute("UPDATE {$this->table} SET {$this->prefix}last_online = " . CURRENT_TIME . " WHERE {$this->prefix}id = $logged_id LIMIT 1");
                    }
                    
                } else {

                    $this->logged   =   false;
                    
                    if(isset($_SESSION['login_error'])) unset($_SESSION['login_error']);
                    
                    if ($redirect)  redirect_url($this->path_login);
                    
                }
                
            } else {
                
                if ($redirect)  redirect_url($this->path_login);
                
            }
            
        } else {
            
            //Trường hợp đăng nhập từ form login
            $email  =    clear_injection($email);

            //Kiểm tra xem tài khoản có tồn tại ko
            $row    =   $this->DB->pass(false)->query("SELECT *
                                        FROM {$this->table}
                                        WHERE {$this->prefix}active = 1 AND {$this->prefix}email = '$email'")
                                        ->getOne();

            if (!empty($row)) {

                $pwd_encoded    =   $this->generatePassword($password, $row[$this->prefix . 'random']);
                if ($pwd_encoded == $row[$this->prefix . 'password']) {

                    $this->DB->pass(false)->execute("UPDATE $this->table
                                        SET {$this->prefix}last_login = " . CURRENT_TIME . ", {$this->prefix}ip_login = '" . clear_injection($_SERVER['REMOTE_ADDR']) . "'
                                        WHERE {$this->prefix}id = " . $row[$this->prefix . 'id'] . "
                                        LIMIT 1");

                    //Nếu khớp thì generate ra thông tin của Admin để sử dụng
                    $_SESSION['logged_id']  =   intval($row[$this->prefix . 'id']);
                    $_SESSION['keymk']      =   $this->encodePassword($row[$this->prefix . 'password']);
                    $this->generateInfoLogged($row);
                                                    
                } else {
                    $this->addError('Email hoặc mật khẩu không hợp lệ. Vui lòng kiểm tra lại!');
                }
            } else {
                $this->addError('Tài khoản không tồn tại!');
            }
            
        }
        
        //Return thông tin của Admin
        return $this->info;
    }

    function envAdmin() {
        if ($this->table == 'admins')    return true;
        return false;
    }

    function envUser() {
        if ($this->table == 'users')    return true;
        return false;
    }

    /**
     * Set alias để show menu bên trái
     */
    function setAliasMenu($alias) {
        $this->alias_menu   =   $alias;
    }

    function setCompany($info) {
        $this->company  =   $info;
    }
    function setHotel($info) {
        $this->hotel    =   $info;
    }

    /*** Hàm lấy ID công ty
     * @return int
     */
    public function getCompanyId() {
        return $this->company['com_id'] ?? 0;
    }

    /*** Hàm lấy ID khách sạn
     * @return int
     */
    public function getHotelId() {
        return $this->hotel['hot_id'] ?? 0;
    }
    
    /**
     * Auth::generateInfoLogged()
     * Sau khi login thành công thì gán các thông tin của User vào class để sử dụng
     * @param mixed $data: Data 
     * @return void
     */
    function generateInfoLogged($data)
    {
        //Remove prefix đi để dùng cho tiện
        foreach ($data as $key => $value) {
            $key    =   str_replace($this->prefix, '', $key);
            $this->info[$key] =   $value;
        }

        $this->id       =  (int)$this->info['id'];
        $this->name     =   $this->info['name'];
        $this->cto      =   !empty($this->info['cto']) ? true : false;
        $this->logged   =  true;

        //Cần dùng nhiều ở trong các Class nên define luôn để ko cần phải global
        define('ACCOUNT_ID', $this->id);

        //Check xem có phải là Super Admin hay ko

        //Nếu là Admin thì chỉ cần check trường boss hoặc cto là được
        if ($this->envAdmin()) {

            if ($this->info['boss'] == 1 || $this->cto) $this->superAccount =   true;

        }
    }

    /**
     * Auth::generateRandom()
     * Generate giá trị cho trường random
     * @return Integer[0, 99999999]
     */
    function generateRandom()
    {
        do {
            $value  =   rand(0, 99999999);
            $result =   $this->DB->query("SELECT {$this->prefix}id FROM {$this->table} WHERE {$this->prefix}random = {$value}")->getOne();
        } while(!empty($result));
        
        return $value;
    }

    /**
     * Auth::generatePassword()
     * Generate ra password cua Admin
     * @param mixed $password: Real password
     * @param mixed $random: Random number of admin
     * @return string md5
     */
    function generatePassword($password, $random)
    {
        return md5($password . '|' . self::SECRET_KEY . $random);
    }

    /**
     * Auth::encodePassword()
     * Encode password de luu vao session dang nhap
     * @param mixed $password
     * @return string md5
     */
    function encodePassword($password)
    {
        return md5(self::SECRET_KEY . '|' . $password);
    }

    /**
     * Auth::checkPasswordSession()
     * Kiem tra chuoi session cua ID dang login co chinh xac ko
     * @param mixed $password
     * @return boolean
     */
    function checkPasswordSession($password)
    {
        $keymk  =   getValue('keymk', GET_STRING, GET_SESSION, '');
        if ($keymk === $this->encodePassword($password)) {
            return true;
        }

        return false;
    }

    public function logged() {
        return $this->logged;
    }

    /**
     * Auth::logout()
     *
     * @param string $redirect
     * @return void
     */
    function logout()
    {
        if (isset($_SESSION['logged_id']))   unset($_SESSION['logged_id']);
        if (isset($_SESSION['company_working']))   unset($_SESSION['company_working']);
        session_destroy();
        session_unset();
        redirect_url($this->path_login);
    }

    function getAllPermission() {

        /** Nếu đã có list quyền rồi thì return luôn để tránh phải gọi lại query **/
        if (!empty($this->all_permission)) {
            return $this->all_permission;
        }
        
        //Lấy ra tất cả các quyền của Account đăng nhập
        //Nếu là Admin thì lấy trực tiếp từ bảng Quyền
        if ($this->envAdmin()) {
            $data   =   $this->DB->query("SELECT per_alias
                                            FROM admins_permission_groups
                                            INNER JOIN admins_permission ON (pega_permission_id = per_id)
                                            WHERE pega_group_id IN(SELECT grac_group_id
                                                                    FROM admins_group_admins
                                                                    WHERE grac_account_id = " . $this->id . ")")
                                            ->toArray();
        } 
        
        foreach ($data as $row) {
            $this->all_permission[] =   $row['per_alias'];
        }
        
        return $this->all_permission;
    }

    /**
     * Check xem có quyền hay ko
     * @param string $permission
     * @return bool
     */
    function hasPermission($permission) {

        //Super Account
        if ($this->isSuperAccount()) return true;
        
        //Check xem quyền cần check có nằm trong array chứa tất cả các quyền của Account đăng nhập hay ko
        if (in_array($permission, $this->getAllPermission())) {
            return true;
        }
        
        return false;
    }

    /**
     * Check quyền sử dụng tính năng
     * @param string $permission
     * @param int $owner_id: Truyền vào ID của người sở hữu quyền xử lý dữ liệu
     * @param bool $exit: Nếu ko có quyền thì exit hay return bool
     */
    function checkPermission($permission, $owner_id = 0, $exit = true) {
        
        //Super Account
        if ($this->isSuperAccount()) return true;
        
        $permission =   clear_injection($permission);
        
        //Đầu tiên là check xem có quyền sử dụng tính năng trước
        $has_permission =   $this->hasPermission($permission);
        
        //Nếu ko có quyền thì return hoặc exit luôn
        if (!$has_permission) {
            if ($exit) {
                exitError('Bạn không có quyền sử dụng tính năng này');
            } else {
                $this->msg  =   'Bạn không có quyền sử dụng tính năng này';
                return false;
            }
        }
        
        //Nếu có quyền thì check tiếp xem quyền này có thuộc loại quyền mà Check quyền sở hữu dữ liệu hay ko

        //Nếu là Admin thì check trực tiếp từ giá trị của trường per_check_owner trong bảng admins_permission, còn nếu là User thì sẽ check theo cấu hình của mỗi công ty, trong bảng company_config_permisison
        if ($this->envAdmin()) {
            $check  =   $this->DB->pass()->query("SELECT per_id, per_check_owner AS check_owner, per_allow_leader AS allow_leader
                                            FROM admins_permission
                                            WHERE per_alias = '$permission'")
                                            ->getOne();
        } 

        if (isset($check['check_owner']) && $check['check_owner'] == 1) {
            
            //Nếu Account đang login ko phải là owner của dữ liệu thì check tiếp các logic quyền theo Level
            if ($owner_id != $this->id) {
                
                //Check tiếp xem Account đang đăng nhập có thuộc nhóm nào mà Nhóm đó ko bị check quyền theo level hay ko, chỉ cần thuộc 1 trong các nhóm đó thì là có quyền luôn
                $level  =   $this->DB->query("SELECT gro_id
                                                FROM {$this->table}_group
                                                INNER JOIN {$this->table}_group_{$this->table} ON (gro_id = grac_group_id)
                                                WHERE gro_no_level = 1 AND grac_account_id = " . $this->id . ($this->envUser() ? " AND gro_company_id = " . COMPANY_ID : "") . "
                                                LIMIT 1")
                                                ->getOne();
                                                        
                //Nếu ko thuộc nhóm nào mà Ko bị check quyền theo level thì lại check tiếp xem Quyền này có cho phép Leader xử lý và Account đang đăng nhập có phải là leader của Owner hay ko
                if (empty($level)) {
                    
                    //Lại check tiếp, nếu quyền này mà ko cho phép leader xử lý thì ko có quyền
                    if ($check['allow_leader'] != 1) {
                        if ($exit) {
                            exitError('Bạn không có quyền xử lý dữ liệu này - Mã: ' . __LINE__);
                        } else {
                            $this->msg  =   'Bạn không có quyền xử lý dữ liệu này - Mã: ' . __LINE__;
                            return false;
                        }
                    }
                    
                    //Nếu cho phép leader thì lấy ra DS các staff của Account để check xem có phải là leader của Owner hay ko
                    $list_staff =   $this->getStaffOfAccount($this->id);
                    
                    if (!in_array($owner_id, $list_staff)) {
                        if ($exit) {
                            exitError('Bạn không có quyền xử lý dữ liệu này - Mã: ' . __LINE__);
                        } else {
                            $this->msg  =   'Bạn không có quyền xử lý dữ liệu này - Mã: ' . __LINE__;
                            return false;
                        }
                    }
                }
                
            }
        }
        
        return true;
    }
    
    function isSuperAccount() {
        return $this->superAccount;
    }

    /**
     * Generate ra câu SQL where field IN (list_user_id)
     * @param string $field: Tên của trường sẽ cho vào câu SQL WHERE
     * @return string SQL
     */
    function sqlPermission($field) {
        
        if ($this->isSuperAccount()) {
            return "";
        }
        
        /**
         * Nếu account này thuộc 1 nhóm quyền nào đó mà Ko bị check quyền theo level (VD: Kế toán check tiền nong của booking) */
        if (!empty($this->DB->query("SELECT gro_id
                                    FROM {$this->table}_group
                                    INNER JOIN {$this->table}_group_{$this->table} ON (gro_id = grac_group_id)
                                    WHERE gro_active = 1 AND gro_no_level = 1 AND grac_account_id = " . $this->id . ($this->envUser() ? sql_company('gro_') : "") . "
                                    LIMIT 1")
                                    ->getOne())) return "";
        
        /**
         * Nếu ko thuộc nhóm nào mà ko bị check quyền level thì sẽ lấy ra các staff
         */
        $list_staff =   $this->getStaffOfAccount($this->id, false);
        //dd($list_staff);
        $sql    =   " AND $field IN($list_staff)";
        
        return $sql;
    }
    
    
    /**
     * Auth::getStaffOfAccount()
     * Lấy ra tất cả các nhân viên của 1 admin
     * @param int $account_id: Nếu = 0 thì lấy theo ID của Admin đăng nhập
     * @param bool $return_array: Nếu true thì trả về [id, id, id], false thì trả về 1,2,3,4
     * @return [id, id, id] OR 1,2,3,4
     */
    function getStaffOfAccount($account_id = 0, $return_array = true) {
        
        //Nếu ko truyền account_id thì lấy theo Account đang login    
        if ($account_id == 0) $account_id   =   $this->id;
        
        //Nếu chưa tồn tại mảng cache lưu của $account_id thì mới cần query ra
        if (!isset($this->list_staff[$account_id])) {
            
            /**
             * Logic:
             * Lấy ra tất cả các nhân viên của Phòng/Ban mà mình làm Manager
             * Đệ quy để lấy ra tất cả các Phòng/Ban cấp dưới của Phòng/Ban mà mình làm Manager, mỗi Phòng/Ban sẽ lấy ra tất cả các nhân viên của Phòng/Ban đó 
             */
            
            //Gán cho chính mình
            $array_id       =   [$account_id];
            $arr_department =   []; //Mảng lưu các Phòng/Ban do mình quản lý và các phòng ban cấp con
            
            //Lấy ra ID của Phòng/Ban mà mình quản lý và tất cả nhân viên của Phòng/Ban đó
            $data   =   $this->DB->query("SELECT DISTINCT deac_account_id, deac_department_id
                                            FROM {$this->table}_department
                                            INNER JOIN {$this->table}_department_{$this->table} ON dep_id = deac_department_id
                                            WHERE dep_active = 1 AND dep_is_default = 0 AND dep_manager_id = $account_id" . ($this->envUser() ? sql_company('dep_') : ""))
                                            ->toArray();
            //dd($data);
            foreach ($data as $row) {
                if (!in_array($row['deac_account_id'], $array_id))    $array_id[] =   $row['deac_account_id'];
                if (!in_array($row['deac_department_id'], $arr_department))  $arr_department[]   =   $row['deac_department_id'];
            }
            //dd($arr_department);
            //Đệ quy để lấy ra tất cả các Phòng/Ban cấp con và Nhân viên của các Phòng/Ban đó
            while (!empty($arr_department)) {
                $list_id        =   implode(',', $arr_department);
                $arr_department =   []; //Reset lại mảng Array để đệ quy vòng mới
                //Lấy ra tất cả các phòng/ban mà Các manager này làm Leader
                $member =   $this->DB->query("SELECT DISTINCT deac_account_id, deac_department_id
                                                FROM {$this->table}_department
                                                INNER JOIN {$this->table}_department_{$this->table} ON dep_id = deac_department_id
                                                WHERE dep_active = 1 AND dep_parent_id IN($list_id)" . ($this->envUser() ? sql_company('dep_') : ""))
                                                ->toArray();
                //dump($this->DB->sql);
                foreach ($member as $row) {
                    if (!in_array($row['deac_account_id'], $array_id))    $array_id[] =   $row['deac_account_id'];
                    if (!in_array($row['deac_department_id'], $arr_department))  $arr_department[]   =   $row['deac_department_id'];
                }
            }
            
            //Gán vào mảng chứa list_staff của class để sử dụng lại, tránh query nhiều lần
            $this->list_staff[$account_id]    =   $array_id;
            
        } else {
            
            //Nếu đã có mảng cache của $account_id thì lấy từ cache ra
            $array_id   =   $this->list_staff[$account_id];
        }
        
        //Nếu return dạng chuỗi: 1,2,3,4
        if (!$return_array) {
            $list_id    =   !empty($array_id) ? implode(',', $array_id) : '0';
            return $list_id;
        }

        //Return array ID;
        return $array_id;
    }

    /**
     * Lấy ra list ID của tất cả các phòng/ban do 1 account quản lý, bao gồm cả cấp con
     * Active là true thì check cả active có = 1 không, con false thì lấy tất
     * @param int $account_id
     * @return [id, id, id]
     */
    function getDepartmentOfAccount($account_id = 0, $active = true) {
        
        //Nếu ko truyền account_id thì lấy theo Account đang login    
        if ($account_id == 0) $account_id   =   $this->id;

        //Mảng chứa các department ID
        $list_department    =   [];
        
        $arr_department =   []; //Mảng lưu các Phòng/Ban do mình quản lý và các phòng ban cấp con để đệ quy
        
        //Tùy vào môi trường Admin hay User mà lấy ra khác nhau
        $sql    =   " AND dep_is_default = 0";
        if ($this->envUser()) {
            $sql    .=  sql_company('dep_');
        }
        if (!$this->isSuperAccount()) {
            $sql    .=  " AND dep_manager_id = $account_id";
        }

        $active_conds = $active ? ' AND dep_active = 1 ' : '';

        //Lấy ra ID của Phòng/Ban mà mình quản lý
        $data   =   $this->DB->query("SELECT dep_id
                                        FROM {$this->table}_department
                                        WHERE 1=1 $active_conds $sql")
                                        ->toArray();
        //dd($data);
        foreach ($data as $row) {
            //Gán vào mảng chính lấy các ID
            $list_department[]  =   $row['dep_id'];
            //Gán vào mảng Child để đệ quy
            $arr_department[]   =   $row['dep_id'];
        }

        //Nếu là Super Admin thì lấy All deparmen từ 1 câu query ở trên rồi nên ko cần query đệ quy nữa
        if ($this->isSuperAccount()) {
            //return  $list_department;
        }

        //dd($arr_department);
        //Đệ quy để lấy ra tất cả các Phòng/Ban cấp con và Nhân viên của các Phòng/Ban đó
        while (!empty($arr_department)) {
            $list_id        =   implode(',', $arr_department);
            
            $arr_department =   []; //Reset lại mảng Array để đệ quy vòng mới
            //Lấy ra tất cả các phòng/ban mà Các manager này làm Leader
            $data   =   $this->DB->query("SELECT dep_id
                                            FROM {$this->table}_department
                                            WHERE 1=1 $active_conds AND dep_is_default = 0 AND dep_parent_id IN($list_id)" . ($this->envUser() ? sql_company('dep_') : "") . (!empty($list_department) ? " AND dep_id NOT IN(" . implode(',', $list_department) . ")" : ""))
                                            ->toArray();
            //dump($this->DB->sql);
            foreach ($data as $row) {
                //Gán vào mảng chính lấy các ID
                $list_department[]  =   $row['dep_id'];
                //Gán vào mảng Child để đệ quy
                $arr_department[]   =   $row['dep_id'];
            }
        }

        //Return array ID
        return $list_department;
    }

    /**
     * Tạo mới tài khoản User
     */
    function createUser($data) {
        
        $data['random'] =   $this->generateRandom();
        $data['active'] =   1;
        $data['time_create']    =   CURRENT_TIME;
        $data['last_update_info']   =   CURRENT_TIME;
        $data['phone']  =   convert_phone_number($data['phone']);
        //Encode Password
        $data['password']   =   $this->generatePassword($data['password'], $data['random']);

        if (!validate_email($data['email'])) {
            $this->addError('Email không hợp lệ');
        }

        if (!validate_phone($data['phone'])) {
            $this->addError('Số điện thoại không hợp lệ');
        }

        //Convert để các key index của mảng data thêm tiền tố use_
        foreach ($data as $key => $value) {
            //$data[''];
        }
        
        $Query  =   new GenerateQuery('users');
        $Query->add('use_name', DATA_STRING, $data['name'], 'Bạn chưa nhập họ tên')
            ->add('use_email', DATA_STRING, $data['email'], 'Bạn chưa nhập Email', 'Email này đã tồn tại')
            ->add('use_phone', DATA_STRING, $data['phone'], 'Bạn chưa nhập số điện thoại', 'Số điện thoại này đã tồn tại')
            ->add('use_password', DATA_STRING, $data['password'])
            ->add('use_random', DATA_INTEGER, $data['random'])
            ->add('use_time_create', DATA_INTEGER, CURRENT_TIME)
            ->add('use_last_update_info', DATA_INTEGER, CURRENT_TIME);
        if ($Query->validate()) {
            if ($this->DB->execute($Query->generateQueryInsert()) > 0) {
                redirect_url('/');
            }
        }

        return false;
    }

    /**
     * Auth:createAdmin
     * @param array $data
     */
    function createAdmin($data) {
        
        $data['random'] =   $this->generateRandom();
        $data['active'] =   1;
        $data['time_create']    =   CURRENT_TIME;
        $data['last_update_info']   =   CURRENT_TIME;
        $data['phone']  =   convert_phone_number($data['phone']);
        //Encode Password
        $data['password']   =   $this->generatePassword($data['password'], $data['random']);

        if (!validate_email($data['email'])) {
            $this->addError('Email không hợp lệ');
        }

        if (!validate_phone($data['phone'])) {
            $this->addError('Số điện thoại không hợp lệ');
        }

        //Convert để các key index của mảng data thêm tiền tố use_
        foreach ($data as $key => $value) {
            //$data[''];
        }
        
        $Query  =   new GenerateQuery('admins');
        $Query->add('adm_name', DATA_STRING, $data['name'], 'Bạn chưa nhập họ tên')
            ->add('adm_email', DATA_STRING, $data['email'], 'Bạn chưa nhập Email', 'Email này đã tồn tại')
            ->add('adm_phone', DATA_STRING, $data['phone'], 'Bạn chưa nhập số điện thoại', 'Số ĐT này đã tồn tại')
            ->add('adm_password', DATA_STRING, $data['password'])
            ->add('adm_random', DATA_INTEGER, $data['random'])
            ->add('adm_active', DATA_INTEGER, $data['active'])
            ->add('adm_time_create', DATA_INTEGER, CURRENT_TIME)
            ->add('adm_last_update', DATA_INTEGER, CURRENT_TIME);
        if ($Query->validate()) {
            if ($this->DB->execute($Query->generateQueryInsert()) > 0) {
                redirect_url('/');
            }
        }
        
        $this->addError($Query->getError());
        return false;
    }
    
    function getListIDGroupOfAccount($admin_id, $return_array = false) {
        $array_id   =   [];
        
        $data   =   $this->DB->query("SELECT grac_group_id FROM admins_group_admins WHERE grac_account_id = " . $admin_id)->toArray();
        foreach ($data as $row) {
            $array_id[] =   $row['grac_group_id'];
        }

        //Nếu return dạng chuỗi: 1,2,3,4
        if (!$return_array) {
            $list_id    =   !empty($array_id) ? implode(',', $array_id) : '0';
            return $list_id;
        }

        //Nếu return theo Array;
        return $array_id;
    }

    /**
     * Fake login Account for Test
     */
    function fakeLogin($account_id = 0)
    {
        
        //Chi co super admin moi co quyen fake login
        if ($this->cto) {
            
            if ($account_id == 0) $account_id  =  getValue('id');
            
            if ($this->verifyToken($account_id)) {
                
                $row    =   $this->DB->query("SELECT * FROM {$this->table} WHERE {$this->prefix}id = $account_id")->getOne();
                
                $this->generateInfoLogged($row);
                $_SESSION['logged_id']  =   intval($row[$this->prefix . 'id']);
                $_SESSION['keymk']      =   $this->encodePassword($row[$this->prefix . 'password']);
                
            }
        }
        
        redirect_url('/');
    }
    
    /**
     * Auth:genToken()
     * Generate ra token de verify khi gửi link
     * @param integer $random
     * @return
     */
    function genToken($random) {
        
        $token   =  md5($random . self::SECRET_KEY . '|' . date('dmY'));
        return $token;
    }

    function verifyToken($account_id) {
        
        //Lấy thông tin Admin
        $row    =   $this->DB->query("SELECT * FROM admins WHERE adm_id = $account_id")->getOne();
        
        if (!empty($row)) {
            
            $token  =   getValue('token', GET_STRING, GET_GET, '');
            
            //Kiểm tra tính hợp lệ của token
            if ($token == $this->genToken($row['adm_random'])) {
                                                
                //Return để ở bên ngoài giao diện cho tạo luôn password
                return true;
                
            } else {
                $this->addError('Liên kết không hợp lệ hoặc đã quá thời gian cho phép!');
                return false;
            }
        }
        
        $this->addError('Tài khoản không tồn tại!');
        return false;
    }

    function isHMS() {
        if ($this->work_space == MODULE_HOTEL)  return true;
        return false;
    }
    
    function isTA() {
        if ($this->work_space == MODULE_AGENCY)  return true;
        return false;
    }

    function isEMS() {
        if ($this->work_space == MODULE_EMS)  return true;
        return false;
    }

    // getConfigUser
    function getConfigUser($key = null) {
        // Nếu mà có r thì return luôn
        if(!empty($this->config_user)) return $this->config_user;

        $conds = [
            'cous_company_id' => COMPANY_ID
        ];
        if($this->isHMS()) {
            $conds['cous_hotel_id'] = HOTEL_ID;
        }
        $this->config_user = ConfigUser::pass()->where($conds)->getOne();

        if(empty($this->config_user)) {
            $this->config_user = [];
        }
        
        if($key) return $this->config_user[$key] ?? null;
        
        return $this->config_user;
    }
    
    /**
     * Lấy danh sách nhân sự của một công ty
     * 
     * @param int $company_id ID của công ty cần lấy danh sách nhân sự, mặc định là công ty hiện tại
     * @param array $conditions Điều kiện bổ sung để lọc danh sách nhân sự
     * @param bool $active Chỉ lấy nhân sự đang hoạt động (active = 1) hay không
     * @param bool $return_array Trả về dạng mảng hay chuỗi ID
     * @return array|string Mảng thông tin nhân sự hoặc chuỗi ID của nhân sự
     */
    function getCompanyStaff($company_id = 0, $conditions = [], $active = true, $return_array = false) {
        // Nếu không truyền company_id thì lấy theo công ty hiện tại
        if ($company_id <= 0) {
            $company_id = COMPANY_ID;
        }
        
        // Kiểm tra xem company_id có hợp lệ không
        if ($company_id <= 0) {
            return $return_array ? [] : '0';
        }
        
        // Xây dựng câu truy vấn cơ bản
        // Lấy danh sách người dùng từ các nhóm quyền của công ty
        $sql = "SELECT DISTINCT u.* FROM users u
                INNER JOIN users_group_users ugu ON (u.use_id = ugu.grac_account_id)
                INNER JOIN users_group ug ON (ugu.grac_group_id = ug.gro_id)
                WHERE ug.gro_company_id = $company_id";
        
        // Thêm điều kiện active nếu cần
        if ($active) {
            $sql .= " AND u.use_active = 1 AND ug.gro_active = 1";
        }
        
        // Thêm các điều kiện bổ sung nếu có
        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                // Xử lý an toàn để tránh SQL injection
                $field = clear_injection($field);
                if (is_numeric($value)) {
                    $sql .= " AND $field = $value";
                } else {
                    $value = clear_injection($value);
                    $sql .= " AND $field = '$value'";
                }
            }
        }
        
        // Thực hiện truy vấn
        $data = $this->DB->pass(false)->query($sql)->toArray();
        
        // Xử lý kết quả trả về
        if ($return_array) {
            return $data; // Trả về mảng đầy đủ thông tin nhân sự
        } else {
            // Trả về chuỗi ID: 1,2,3,4
            $ids = [];
            foreach ($data as $row) {
                $ids[] = $row['use_id'];
            }
            return $ids;
        }
    }
    
}