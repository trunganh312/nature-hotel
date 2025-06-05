<?php
class FakeLogin extends Model {

    private $table  =   null;
    private $prefix =   null;
    public  $info   =   [];
    public  $id     =   0;
    public  $name   =   '';
    public  $cto    =   false;

    const   PWD_DEFAULT     =   'S112233';
    const   SECRET_KEY      =   'NjuW18$LI';

    public function __construct() {
        
        parent::__construct();

        /* Lấy Table theo Environment đang sử dụng */
        if (is_user()) {
            $this->table    =   'users';
            $this->prefix   =   'use_';
        } else if (is_admin()) {
            $this->table    =   'admins';
            $this->prefix   =   'adm_';
        } else {
            save_log('error_environment.cfn', 'Error Environment');
            exit('Error Environment');
        }
    }
    
    /**
     * Auth::generateInfoLogged()
     * Sau khi login thành công thì gán các thông tin của User vào class để sử dụng
     * @param mixed $data
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
    }

    /**
     * Fake login Account for Test
     */
    function fakeLogin($account_id = 0)
    {
            
        if ($account_id == 0) $account_id  =  getValue('id');
        
        if ($this->verifyToken($account_id)) {
            
            $row    =   $this->DB->query("SELECT * FROM {$this->table} WHERE {$this->prefix}id = $account_id")->getOne();
            
            //$this->generateInfoLogged($row);
            $_SESSION['logged_id']  =   intval($row[$this->prefix . 'id']);
            $_SESSION['fake_login'] =   1;
            $_SESSION['keymk']      =   $this->encodePassword($row[$this->prefix . 'password']);
            
        }
        
        redirect_url('/');
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
     * Auth:genToken()
     * Generate ra token de verify khi gửi link
     * @param integer $random
     * @return
     */
    function genToken($random) {
        $token   =  md5($random . self::SECRET_KEY . '|' . date('dmY'));
        return $token;
    }
    
    /**
     * Auth::verifyToken()
     * Xác thực token cho những link có kèm theo token
     * @param integer $account_id
     * @param string $table
     * @return
     */
    function verifyToken($account_id) {
        
        //Lấy thông tin Account
        $row    =   $this->DB->query("SELECT * FROM {$this->table} WHERE {$this->prefix}id = $account_id")->getOne();
        
        if (!empty($row)) {
            
            $token  =   getValue('token', GET_STRING, GET_GET, '');
            
            //Kiểm tra tính hợp lệ của token
            if ($token == $this->genToken($row[$this->prefix . 'random'])) {
                return true;
            } else {
                $this->addError('Liên kết không hợp lệ hoặc đã quá thời gian cho phép!');
                return false;
            }
        }
        
        $this->addError('Tài khoản không tồn tại!');
        return false;
    }

    function generatePassword($password, $random)
    {
        return md5($password . '|' . self::SECRET_KEY . $random);
    }

    // Reset mật khẩu tất cả user
    function resetPassword($type = 'users', $mode = 'all', $id = 0, $pwd = PWD_DEFAULT) {
        // Lấy danh sách tài khoản theo type
        if($mode == 'all') {
            $data   =   $this->DB->query("SELECT * FROM $type")->toArray();
        
            foreach ($data as $row) {
                // Reset mật khẩu
                $password = $this->generatePassword($pwd, $row['use_random']);

                // Update password
                $this->DB->query("UPDATE $type SET use_password = '$password' WHERE use_id = " . $row['use_id']);
            }
        }else {
            $data   =   $this->DB->query("SELECT * FROM $type WHERE use_id = $id")->getOne();
        
            // Reset mật khẩu
            $password = $this->generatePassword($pwd, $data['use_random']);

            // Update password
            $this->DB->query("UPDATE $type SET use_password = '$password' WHERE use_id = " . $data['use_id']);

            return true;
        }
        
    }
}