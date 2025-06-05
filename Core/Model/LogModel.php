<?php

use src\Services\CommonService;

/**
 * Class LogModel
 * @version 1.0
 * 25-03-20
 */

class LogModel extends Model
{

    private $field  =   []; //Mảng lưu các trường dữ liệu cần lưu log
    private $data_old   =   [];  //Mảng chứa các giá trị cũ của bản ghi
    private $data_new   =   [];  //Mảng chứa các giá trị mới của bản ghi
    private $content    =   ''; //Nội dung lưu log
    private $table_id   =   ''; //ID của bảng mà cần lưu log, lấy từ bảng table_log
    private $table_cache    =   []; //Mảng chứa các bảng lưu log cùng với các field để cache, ko cần phải query lại các lần sau


    function __construct()
    {
        parent::__construct();
    }

    /**
     * LogModel::genContent()
     * Generate ra noi dung luu log tu data_old va data_new
     * @param mixed $data_old
     * @param mixed $data_new
     * @return $this
     */
    function genContent($data_old, $data_new)
    {

        $arr_content    =   [];
        $chart_unit     =   ';';

        /*
        dump($data_old);
        dump($data_new);
        exit();
        */
        //So sánh 2 mảng giá trị, nếu mảng mới khác giá trị với mảng cũ thì tùy theo kiểu dữ liệu nào mà lấy ra giá trị để diễn giải bằng lời cho content log

        foreach ($data_new as $field => $value_new) {
            $value_old  =   $data_old[$field];

            /*
            echo    'Field: ' . $field . '<br>';
            echo    'Old: ' . $value_old . '<br>';
            echo    'New: ' . $value_new . '<br>';
            */

            //Chỉ xét trường hợp có sự thay đổi, nếu ko tồn tại field của data_old thì coi như param truyền vào bị lỗi
            if ($value_old != $value_new && isset($this->field[$field])) {
                $field_info =   $this->field[$field];

                /*
                echo    'Field: ' . $field . '<br>';
                echo    'Old: ' . $value_old . '<br>';
                echo    'New: ' . $value_new . '<br>';
                */

                switch ($field_info['fie_type']) {
                    //Nếu là dạng text thì lấy luôn
                    case FIELD_TEXT:
                        $arr_content[]  =   $field_info['fie_name'] . ': ' . $value_old . ' => ' . $value_new;
                        break;
                    //Nếu dạng date time thì show ra dạng dd/mm/YYYY
                    case FIELD_TIME:
                        $arr_content[]  =   $field_info['fie_name'] . ': ' . date("d/m/Y H:i:s", $value_old) . ' => ' . date("d/m/Y H:i:s", $value_new);
                        break;

                    //Nếu dạng lưu theo ID từ 1 bảng trong DB thì query để lấy ra giá trị dạng text của ID
                    case FIELD_DATABASE:
                        if ($field_info['fie_table_target'] != '' && $field_info['fie_field_id'] != '' && $field_info['fie_field_value'] != '') {

                            $f_id   =   clear_injection($field_info['fie_field_id']);
                            $f_name =   clear_injection($field_info['fie_field_value']);

                            //Lấy ra giá trị dạng text của các value ID
                            $values =   $this->DB->pass()->query("SELECT " . $f_id . ", " . $f_name . "
                                                            FROM " . clear_injection($field_info['fie_table_target']) . "
                                                            WHERE " . $f_id . " IN (" . (int)$value_old . "," . (int)$value_new . ")")
                                                            ->toArray();

                            foreach ($values as $row) {

                                if ($row[$f_id] == $value_old) $value_old  =   $row[$f_name];
                                if ($row[$f_id] == $value_new) $value_new  =   $row[$f_name];
                            }
                        }
                        $arr_content[]  =   $field_info['fie_name'] . ': ' . $value_old . ' => ' . $value_new;
                        break;

                    //Nếu dạng lưu theo constant thì cần phải có 1 biến là mảng chứa các giá trị của constant, lưu ở file cfg_variable_log
                    case FIELD_CONSTANT:
                        $variable   =   $field_info['fie_variable'];
                        global $$variable;
                        
                        if (isset($$variable)) {
                            $value_constant =   $$variable;
                            if (isset($value_constant[$value_old]) && isset($value_constant[$value_new])) {
                                $arr_content[]  =   $field_info['fie_name'] . ': ' . $value_constant[$value_old] . ' => ' . $value_constant[$value_new];
                            }
                        }
                        break;
                        
                    case FIELD_BASE64:
                        // Chuyển thành text
                        $old_value = CommonService::decode($value_old, true);
                        $old_value = json_encode($old_value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                        $new_value = CommonService::decode($value_new, true);
                        $new_value = json_encode($new_value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

                        $arr_content[]  =   $field_info['fie_name'] . ': ' . $old_value . ' => ' . $new_value;
                        break;

                    default:
                        $arr_content[]  =   $field_info['fie_name'] . ':' . $value_old . ' => ' . $value_new;
                        break;
                }
            } else {
                //Unset các trường ko cần thiết lưu log để nhẹ dữ liệu khi insert vào table
                unset($data_old[$field]);
                unset($data_new[$field]);
            }
        }

        //dump($arr_content);
        //Gán vào content lưu log
        $this->content  =   implode(($chart_unit . '<br>'), $arr_content);
        $this->data_old =   $data_old;
        $this->data_new =   $data_new;

        //Nếu ko lấy được content thì lưu log lỗi
        //if (empty($arr_content))    $this->error_log    =   json_encode($this->data_old) . ' <=> ' . json_encode($this->data_new);

        //Return this
        return $this;
    }


    /**
     * LogModel::setContent()
     * Set content de luu log
     * @param mixed $content
     * @return $this
     */
    function setContent($content)
    {
        $this->content  =   trim($content);

        //Return this
        return $this;
    }

    /**
     * LogModel::setDataOld()
     * 
     * @param mixed $data
     * @return
     */
    function setDataOld($data)
    {
        $this->data_old =   $data;

        //Return this
        return $this;
    }


    /**
     * LogModel::setDataNew()
     * 
     * @param mixed $data
     * @return
     */
    function setDataNew($data)
    {
        $this->data_new =   $data;

        //Return this
        return $this;
    }

    function setTable($table) {
        
        //Lấy ra ID của table
        $data   =   $this->DB->pass()->query("SELECT talo_id FROM table_log WHERE talo_table = '" . $table . "'")->getOne();
        if (empty($data)) {
            save_log('error_create_log.cfn', 'Table log ' . $table . ' not found');
            if (is_dev())   exitError('Table log ' . $table . ' not found');
            return false;
        }
        $table_id   =   $data['talo_id'];
        $this->table_id =   $table_id;
        
        //Nếu chưa có mảng cache các field của table thì lấy ra các trường được lưu log
        if (empty($this->table_cache[$table_id])) {
            $fields =   [];
            $data   =   $this->DB->query("SELECT * FROM field_log WHERE fie_table_id = " . $table_id)->toArray();
            foreach ($data as $row) {
                $fields[$row['fie_field']]  =   $row;
            }
            
            //Gán vào list field để sử dụng generate ra content
            $this->field    =   $fields;
            //Gán vào cache để ko cần query lại
            $this->table_cache[$table_id]   =   $fields;
        }

        return $this;
    }

    /**
     * LogModel::createLog()
     * Create log - Phai goi hang setContent hoac genContent truoc khi goi ham nay
     * @param integer $record_id
     * @param integer $type: LOG_VIEW, LOG_UPDATE, LOG_CREATE
     * @param string $url: Neu ko truyen vao thi se lay theo URI
     * @return bool result
     */
    function createLog($record_id, $type = LOG_UPDATE, $url = '')
    {
        
        //Global để lấy Account ID
        global  $Auth;
        $record_id  =   (int)$record_id;
        //Check nếu $record_id là dạng string thì là class Log cũ chưa sửa, exit để đi sửa
        if (is_dev() && $record_id == 0) {
            echo    'Chưa sửa hàm Log mới';
            exit();
        }
        
        $account_id =   0;
        if (isset($Auth)) {
            //Ko lưu log của CTO
            if ($Auth->envAdmin() && $Auth->cto && is_pro()) {
                //return false;
            }
            
            //Account ID
            $account_id =   $Auth->id;
        }
        
        //Nếu ko có log thì return luôn ko cần ghi
        if ($this->content == '') {
            return false;
        }

        //IP address
        $log_ip =   isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'N/A';

        //URL thao tác dẫn đến ghi log
        if (empty($url) && isset($_SERVER['REQUEST_URI'])) {
            $url    =   $_SERVER['REQUEST_URI'];
        }

        //Tên của bảng sẽ lưu log (Chia theo tháng)
        $table_log  =   $this->getTableName();

        //Check bảng
        $this->checkTable($table_log);
        
        $Query  =   new GenerateQuery($table_log);
        $Query->setFieldDisableForm(['log_table', 'log_record']);
        $Query->add('log_table', DATA_INTEGER, $this->table_id)
                ->add('log_record', DATA_INTEGER, $record_id)
                ->add('log_content', DATA_STRING, $this->content)
                ->add('log_account', DATA_INTEGER, $account_id)
                ->add('log_time', DATA_INTEGER, CURRENT_TIME)
                ->add('log_ip', DATA_STRING, $log_ip)
                ->add('log_url', DATA_STRING, $url)
                ->add('log_type', DATA_INTEGER, $type);

        //Lưu data json của bản ghi cũ và mới
        if (!empty($this->data_old)) {
            $Query->add('log_data_old', DATA_STRING, json_encode($this->data_old, JSON_UNESCAPED_UNICODE));
        }
        if (!empty($this->data_new)) {
            $Query->add('log_data_new', DATA_STRING, json_encode($this->data_new, JSON_UNESCAPED_UNICODE));
        }
        
        //Ko remove html
        $Query->setRemoveHTML(false);
        
        //Insert log
        if ($Query->validate()) {
            
            if ($this->DB->pass()->execute($Query->generateQueryInsert()) > 0) {
                
                //Reset lại content log để tiếp tục lưu các log khác
                $this->content  =   '';
                $this->data_old =   [];
                $this->data_new =   [];

                return true;
            }
        }

        save_log('error_create_log.cfn', $this->content . ': ' . json_encode($Query->error));

        //Reset lại content log
        $this->content  =   '';
        $this->data_old =   [];
        $this->data_new =   [];

        //Return
        return false;
    }


    /**
     * LogModel::getLog()
     * 
     * @param mixed $table: ID của table được lưu trong bảng table_log, có thể xem của nhiều bảng 1 lần
     * @param mixed $record_id: ID của bản ghi cần xem log
     * @param string $date_range
     * @param integer $type: 0: Xem all, LOG_CREATE, UPDATE, VIEW
     * @return
     */
    function getLog($table, $record_id, $date_range = '', $type = 0)
    {

        //Câu SQL
        $sql    =   "log_table IN($table)";

        //ID của bản ghi
        $sql    .=  " AND log_record = " . (int)$record_id;

        //Câu SQL lọc theo type
        if ($type > 0) {
            $sql    .=  " AND log_type = " . (int)$type;
        }
        
        //Mặc định là lấy log trong 1 tháng
        $time_from  =   CURRENT_TIME - 30 * 86400;
        $time_to    =   CURRENT_TIME;
        //Nếu có lọc theo thời gian
        if ($date_range != '') {
            $time_range =   generate_time_from_date_range($date_range);
            $time_from  =   $time_range['from'];
            $time_to    =   $time_range['to'];
        }
        
        /** Lấy ra các tháng để lấy ra tên bảng **/
        $arr_table  =   [];
        $data_return    =   [];
        
        $start      =   (new DateTime(date('Y-m-d', $time_from)))->modify('first day of this month');
        $end        =   (new DateTime(date('Y-m-d', $time_to)))->modify('first day of next month');
        $interval   =   DateInterval::createFromDateString('1 month');
        $period     =   new DatePeriod($start, $interval, $end);
        
        foreach ($period as $dt) {
            
            //Tùy môi trường Admin hay User mà lấy tên account ở bảng admins hay user
            if (is_user()) {
                $tbl_account    =   'users';
                $pre_account    =   'use_';
                $table          =   'log_' . $dt->format("Ym");
            } else {
                $tbl_account    =   'admins';
                $pre_account    =   'adm_';
                $table          =   'crm_log_' . $dt->format("Ym");
            }
            
            if (!$this->checkTable($table, false)) continue;

            $data   =   $this->DB->pass()->query("SELECT $table.*, {$pre_account}name AS user_name
                                            FROM $table
                                            INNER JOIN $tbl_account ON log_account = {$pre_account}id
                                            WHERE $sql AND log_time BETWEEN $time_from AND $time_to
                                            ORDER BY log_time DESC, log_id DESC")
                                            ->toArray();
            foreach ($data as $row) {
                $data_return[]  =   $row;
            }
        }
        
        //dump($data_return);
        
        //Return mảng chứa log
        return $data_return;
    }

    /**
     * LogModel::getTableName()
     * Lấy ra tên bảng sẽ lưu log theo thời gian
     * @param mixed $record_id
     * @return string table name
     */
    function getTableName($time_stamp = 0)
    {
        global  $Auth;
        $type   =   '';
        if (isset($Auth)) {
            if ($Auth->envAdmin())  $type   =   'crm_';
        } else {
            $type   =   'auto_';
        }
        $table  =   $type . "log_" . ($time_stamp > 0 ? date('Ym', $time_stamp) : date('Ym'));

        return $table;
    }

    /**
     * LogModel::checkTable()
     * Check table xem da co chua, neu chua co thi tao moi
     * @param mixed $table
     * @return void
     */
    private function checkTable($table, $create = true)
    {
        if (empty($this->DB->pass()->query("SHOW TABLES LIKE '" . $table . "'")->getOne())) {
            
            if (!$create) return false;

            $this->DB->execute("CREATE TABLE `" . $table . "` (
                                  `log_id` int(11) NOT NULL AUTO_INCREMENT,
                                  `log_table` int(11) NOT NULL,
                                  `log_record` int(11) NOT NULL DEFAULT 0,
                                  `log_content` text DEFAULT NULL,
                                  `log_account` int(11) NOT NULL DEFAULT 0,
                                  `log_time` int(11) NOT NULL DEFAULT 0,
                                  `log_ip` varchar(255) NOT NULL,
                                  `log_url` text NOT NULL,
                                  `log_type` tinyint(3) NOT NULL DEFAULT 0,
                                  `log_data_old` text DEFAULT NULL,
                                  `log_data_new` text DEFAULT NULL,
                                  PRIMARY KEY (`log_id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }

        return true;
    }

    /**
     * LogModel::createLogAccess()
     * Lưu log Access của Admin
     * @return
     */
    function createLogAccess() {
        
        global  $Auth, $disable_save_log_access;
        if (!isset($Auth)) return false;
        if (isset($disable_save_log_access))    return false;

        //Ko lưu log của CTO
        if ($Auth->cto) {
            return false;
        }
        //Chỉ lưu log của các Admin mà cần phải lưu log để tránh bị nặng DB
        // Và nếu đang fake login thì không lưu log_acess
        if ($Auth->info['save_log_access'] != 1) {
            return false;
        }
        $fake_login = $_SESSION['fake_login'] ?? 0;
        if ($fake_login >= 1) {
            return false;
        }
        
        //IP address
        $log_ip     =   get_client_ip();
        
        //URL
        $log_url  =   isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        
        $table  =   'log_access_' . date('Ym');
        $this->checkTableLogAccess($table);
        
        $Query  =   new GenerateQuery($table);
        $Query->add('loga_admin', DATA_INTEGER, $Auth->id)
            ->add('loga_time', DATA_INTEGER, CURRENT_TIME)
            ->add('loga_ip', DATA_STRING, $log_ip)
            ->add('loga_url', DATA_STRING, $log_url)
            ->add('loga_data_post', DATA_STRING, !empty($_POST) ? json_encode($_POST) : '');
        
        //Ko remove html
        $Query->setRemoveHTML(false);
        
        if ($this->DB->pass()->execute($Query->generateQueryInsert()) > 0) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * LogModel::checkTableLogPage()
     * Check và tạo bảng Log các Page mà Admin truy cập
     * @param mixed $table
     * @param bool $create
     * @return
     */
    function checkTableLogAccess($table, $create = true) {
        $row    =   $this->DB->pass()->query("SHOW TABLES LIKE '" . $table . "'")->getOne();

        if (empty($row)) {
            if (!$create) return false;

            $this->DB->pass()->execute("CREATE TABLE `" . $table . "` (
                                  `loga_id` int(11) NOT NULL AUTO_INCREMENT,
                                  `loga_admin` int(11) NOT NULL DEFAULT 0,
                                  `loga_time` int(11) NOT NULL DEFAULT 0,
                                  `loga_ip` varchar(255) NOT NULL,
                                  `loga_url` text NOT NULL,
                                  `loga_data_post` text DEFAULT NULL,
                                  PRIMARY KEY (`loga_id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }

        return true;
    }
    
    /**
     * LogModel::getLogAccess()
     * 
     * @param mixed $admin_id
     * @param string $date_range
     * @return
     */
    function getLogAccess($admin_id, $date_range = '')
    {

        //Câu SQL
        $sql    =   " loga_admin = $admin_id";
        
        //Mặc định là lấy của ngày hôm nay
        $time_from  =   strtotime(date('m/d/Y', CURRENT_TIME));
        $time_to    =   CURRENT_TIME;
        //Nếu có lọc theo thời gian
        if ($date_range != '') {
            $time_range =   generate_time_from_date_range($date_range);
            $time_from  =   $time_range['from'];
            $time_to    =   $time_range['to'];
        }
        $sql    .=  " AND loga_time BETWEEN $time_from AND $time_to";
        
        /** Lấy ra các tháng để lấy ra tên bảng **/
        $arr_table  =   [];
        $data_return    =   [];
        
        $start      =   (new DateTime(date('Y-m-d', $time_from)))->modify('first day of this month');
        $end        =   (new DateTime(date('Y-m-d', $time_to)))->modify('first day of next month');
        $interval   =   DateInterval::createFromDateString('1 month');
        $period     =   new DatePeriod($start, $interval, $end);
        
        foreach ($period as $dt) {
            $table  =   'log_access_' . $dt->format("Ym");
            
            if (!$this->checkTableLogAccess($table, false)) continue;
            
            $data   =   $this->DB->pass()->query("SELECT *
                                            FROM $table
                                            WHERE $sql
                                            ORDER BY loga_time DESC, loga_id DESC")
                                            ->toArray();
            foreach ($data as $row) {
                $data_return[]  =   $row;
            }
        }
        
        //dump($data_return);
        
        //Return mảng chứa log
        return $data_return;
    }
}
