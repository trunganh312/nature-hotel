<?

use src\Libs\Utils;
use src\Models\Customer;

/**
 * Class CustomerModel
 * Version 1.0
 */

class CustomerModel extends Model {
    
    function __construct() {
        parent::__construct();
    }
    
    /**
     * CustomerModel::createCustomer()
     * 
     * @param mixed $data
     * @return
     */
    function createCustomer($data) {
        global $Auth;

        $phone  =   convert_phone_number($data['phone']);
        
        if (!validate_phone($phone)) {
            $this->addError('Số điện thoại không hợp lệ!');
            return 0;
        }
        
        //Check xem có data này hay chưa
        $check  =   $this->getCustomerByPhone($phone, 'cus_id');
        if (!empty($check)) {
            return $check['cus_id'];   //Nếu có rồi thì return luôn ID của Customer
        }
        
        $Query  =   new GenerateQuery('customer');
        $Query->setFieldDisableForm(['cus_company_id', 'cus_hotel_id']);
        $Query->add('cus_company_id', DATA_INTEGER, COMPANY_ID)
            ->add('cus_phone', DATA_STRING, $phone)
            ->add('cus_phone_other', DATA_STRING, !empty($data['other']) ? $data['other'] : '')
            ->add('cus_name', DATA_STRING, !empty($data['name']) ? $data['name'] : '')
            ->add('cus_email', DATA_STRING, !empty($data['email']) ? $data['email'] : '')
            ->add('cus_city', DATA_INTEGER, !empty($data['city']) ? $data['city'] : 0)
            ->add('cus_time_create', DATA_INTEGER, CURRENT_TIME)
            ->add('cus_last_update', DATA_INTEGER, CURRENT_TIME)
            ->add('cus_user_create', DATA_INTEGER, ACCOUNT_ID)
            ->add('cus_birthday', DATA_INTEGER, array_get($data, 'birthday', 0))
            ->add('cus_source', DATA_INTEGER, !empty($data['source']) ? $data['source'] : 0);

        //Nếu là HMS thì phải lưu thêm HOTEL_ID
        $is_required_hotel = false;
        if ($Auth->isHMS()) {
            $Query->add('cus_hotel_id', DATA_INTEGER, HOTEL_ID);
            $is_required_hotel = true;
        }
            
        if ($Query->validate()) {
            
            $customer_id    =   $this->DB->pass(true, $is_required_hotel)->executeReturn($Query->generateQueryInsert());
            
            if ($customer_id > 0) {

                $this->createHistory([
                    'id'        =>  $customer_id,
                    'content'   =>  'Tạo mới khách hàng'
                ]);
                
                /** Lưu log **/
                global  $Log;
                $data_new   =   $this->DB->pass(false)->query("SELECT * FROM customer WHERE cus_id = $customer_id". sql_hotel('cus_'))->getOne();
                $Log->setTable('customer')->setDataNew($data_new)->setContent('Tạo mới khách hàng')->createLog($customer_id, LOG_CREATE);
                
                return $customer_id;
                
            } else {
                save_log('error_create_customer.cfn', 'Data: ' . json_encode($data) . '. Error: Có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
            }
            
        } else {
            save_log('error_create_customer.cfn', 'Data: ' . json_encode($data) . '. Error: ' . json_encode($Query->getError()));
        }

        //Mặc định là return 0
        return 0;
    }

     /**
     * CustomerModel::createCustomerHMS()
     * Hàm sử dụng cho việc xử lý tạo customer khi gửi từ bên TA sang
     * 
     * @param mixed $data
     * @return
     */
    function createCustomerHMS($data) {
        $phone  =   convert_phone_number($data['phone']);
        
        if (!validate_phone($phone)) {
            $this->addError('Số điện thoại không hợp lệ!');
            return 0;
        }

        //Check xem có data này hay chưa
        $check  =   Customer::pass()->select('cus_id')
                                            ->where(['cus_phone' => $phone, 'cus_hotel_id' => $data['hotel_id'], 'cus_company_id'=> $data['company_id']])
                                            ->getOne();
        if (!empty($check)) {
            return $check['cus_id'];   //Nếu có rồi thì return luôn ID của Customer
        }
        
        $Query  =   new GenerateQuery('customer');
        $Query->setFieldDisableForm(['cus_company_id', 'cus_hotel_id']);
        $Query->add('cus_company_id', DATA_INTEGER, $data['company_id'])
            ->add('cus_phone', DATA_STRING, $phone)
            ->add('cus_phone_other', DATA_STRING, !empty($data['other']) ? $data['other'] : '')
            ->add('cus_name', DATA_STRING, !empty($data['name']) ? $data['name'] : '')
            ->add('cus_email', DATA_STRING, !empty($data['email']) ? $data['email'] : '')
            ->add('cus_city', DATA_INTEGER, !empty($data['city']) ? $data['city'] : 0)
            ->add('cus_time_create', DATA_INTEGER, CURRENT_TIME)
            ->add('cus_last_update', DATA_INTEGER, CURRENT_TIME)
            ->add('cus_user_create', DATA_INTEGER, ACCOUNT_ID)
            ->add('cus_birthday', DATA_INTEGER, array_get($data, 'birthday', 0))
            ->add('cus_hotel_id', DATA_INTEGER, $data['hotel_id'])
            ->add('cus_source', DATA_INTEGER, !empty($data['source']) ? $data['source'] : 0);

        //Nếu là HMS thì phải lưu thêm HOTEL_ID
        $is_required_hotel = false;
        if ($Query->validate()) {
            
            $customer_id    =   $this->DB->pass(true, $is_required_hotel)->executeReturn($Query->generateQueryInsert());
            
            if ($customer_id > 0) {

                $this->createHistory([
                    'id'        =>  $customer_id,
                    'content'   =>  'Tạo mới khách hàng'
                ]);
                
                /** Lưu log **/
                global  $Log;
                $data_new = $this->DB->pass(false)
                                    ->query("SELECT * FROM customer WHERE cus_id = $customer_id AND cus_hotel_id = " 
                                            . $data['hotel_id'] . " AND cus_company_id = " . $data['company_id'])
                                    ->getOne();

                $Log->setTable('customer')->setDataNew($data_new)->setContent('Tạo mới khách hàng')->createLog($customer_id, LOG_CREATE);
                
                return $customer_id;
                
            } else {
                save_log('error_create_customer.cfn', 'Data: ' . json_encode($data) . '. Error: Có lỗi xảy ra khi xử lý dữ liệu, vui lòng thử lại!');
            }
            
        } else {
            save_log('error_create_customer.cfn', 'Data: ' . json_encode($data) . '. Error: ' . json_encode($Query->getError()));
        }

        //Mặc định là return 0
        return 0;
    }
    
    /**
     * CustomerModel::updateCustomer()
     * Update các thông tin cơ bản của Data
     * @param mixed $data[id, name, phone, other, note]
     * @return bool
     */
    function updateCustomer($data) {
            
        //Lấy thông tin bản ghi để ghi log
        $record_info    =   $this->DB->query("SELECT * FROM customer WHERE cus_id = " . $data['id'] . sql_company('cus_') . sql_hotel('cus_'))->getOne();
        if (empty($record_info)) {
            $this->addError('Dữ liệu không tồn tại');
            return false;
        }
        
        //Chỉ update các thông tin: Họ tên, Số ĐT, Tỉnh/TP...
        $Query  =   new GenerateQuery('customer');
        $Query->add('cus_name', DATA_STRING, $data['name']);
        $Query->setPrefixSqlCompany('cus_');
        
        if (!empty($data['phone'])) {
            //Convert về chuẩn format
            $data['phone']  =   convert_phone_number($data['phone']);
            $_POST['cus_phone']    =   $data['phone'];
            
            $Query->add('cus_phone', DATA_STRING, $data['phone'], '', 'Số điện thoại này đã tồn tại trên hệ thống Data khách hàng');
        }
        
        if (!empty($data['other'])) {
            $Query->add('cus_phone_other', DATA_STRING, $data['other']);
        }
        
        if (!empty($data['note'])) {
            $Query->add('cus_note', DATA_STRING, $data['note']);
        }
        
        if (!empty($data['birthday'])) {
            $Query->add('cus_birthday', DATA_INTEGER, $data['birthday']);
        }

        if (Utils::checkRequiredFieldHotel()) {
            $Query->setPrefixSqlHotel('cus_');
        }
        
        if ($Query->validate('cus_id', $data['id'])) {
            
            if ($this->DB->pass()->execute($Query->generateQueryUpdate('cus_id', $data['id'])) >= 0) {
                
                /** Lưu log **/
                global  $Log;
                $data_new   =   $this->DB->pass(false, true)->query("SELECT * FROM customer WHERE cus_id = " . $data['id'] . sql_hotel('cus_'))->getOne();
                $Log->setTable('customer')->genContent($record_info, $data_new)->createLog($data['id']);
                
                return true;
                
            }
        }
        
        //Default return
        save_log('error_update_customer.cfn', 'Data: ' . json_encode($data) . '. Error: ' . json_encode($Query->getError()));
        return false;
    }
    
    /**
     * CustomerModel::getCustomerByPhone()
     * Check xem Data (Số ĐT) đã tồn tại hay chưa
     * @param mixed $phone
     * @return bool
     */
    function getCustomerByPhone($phone, $field = '*') {
        
        //Đưa về chuẩn format để select
        $phone  =   convert_phone_number($phone);
        
        return  $this->DB->query("SELECT $field FROM customer WHERE cus_phone = '$phone'" . sql_company('cus_') . sql_hotel('cus_'))->getOne();
        
    }

    /**
     * Check số ĐT khi sửa các request/booking xem có bị trùng với số ĐT của KH khác rồi hay ko
     */
    function validatePhoneEdit($phone, $customer_id) {

        //Check xem đã có Customer nào có số ĐT trùng với số ĐT mới sửa ko
        $check  =   $this->DB->query("SELECT cus_id
                                        FROM customer
                                        WHERE cus_id <> $customer_id AND cus_phone = '" . convert_phone_number($phone) . "'" . sql_company('cus_') . sql_hotel('cus_') . "
                                        LIMIT 1")->getOne();
        if (empty($check)) {
            return  true;
        }
        return false;
    }
    
    /**
     * CustomerModel::updateCustomerNew()
     * Cập nhật thông tin KH khi có request mới (Bao gồm request, booking)
     * @param array $data [phone, name, content, phone_other]
     * @param string $table [customer_request, booking_hotel, booking_tour, booking_ticket]
     * @param integer $id: ID của Request hoặc Booking
     * @return bool
     */
    function updateCustomerNew($data, $table, $id) {
        
        //Mảng lưu tất cả các bảng dữ liệu
        global  $cfg_table_request_all;
        
        if (!isset($cfg_table_request_all[$table])) {
            return false;
        }
        $prefix =   $cfg_table_request_all[$table];
        $result =   false;
        $error  =   [];
        
        /** Check xem số ĐT này đã có trong bảng Customer chưa, nếu chưa thì create new Customer **/
        $data_info  =   $this->getCustomerByPhone($data['phone'], '*');
        
        if (empty($data_info)) {
            
            //Nếu chưa có thì tạo mới Customer
            $customer_id    =   $this->createCustomer($data);

            //Update lại Data ID vào bảng của đối tượng
            if ($this->DB->execute("UPDATE $table SET {$prefix}customer_id = $customer_id WHERE {$prefix}id = $id " . sql_company($prefix) . " LIMIT 1") > 0) {
                //Gán return để bên dưới còn update history
                $result =   true;
            } else {
                $error[]    =   $this->DB->sql;
            }
            
        } else {
            
            $customer_id    =   $data_info['cus_id'];
            $data['id'] =   $customer_id;
            
            //Update Data ID và Return vào bảng của Request/Booking
            if ($this->DB->execute("UPDATE $table
                                    SET {$prefix}customer_id = " . $customer_id . ($data_info['cus_total_booking'] > 0 ? ", {$prefix}source = " . SOURCE_RETURN : "") . "
                                    WHERE {$prefix}id = $id " . sql_company($prefix) . "
                                    LIMIT 1") >= 0) {
                //Lưu ý: Check >= 0 vì nếu BK tạo từ Admin thì customer_id và return đã được update ngay từ lúc tạo rồi, ở đâu update lại sẽ affect = 0
                
                //Nếu là tạo request mới, hoặc booking đặt từ website thì mới cộng thêm số lượt request cho Customer
                $update_more    =   true;
                if ($table != 'customer_request') {
                    $bk_info    =   $this->DB->query("SELECT {$prefix}request_id FROM $table WHERE {$prefix}id = $id" . sql_company($prefix))->getOne();
                    if ($bk_info[$prefix . 'request_id'] > 0) {
                        $update_more    =   false;
                    }
                }
                if ($update_more) {
                    $this->DB->execute("UPDATE customer
                                        SET cus_total_request = cus_total_request + 1, cus_last_request = " . CURRENT_TIME . "
                                        WHERE cus_id = $customer_id " . sql_company('cus_') . " LIMIT 1");
                }
                
                //Gán return để bên dưới còn update history
                $result =   true;
                
            } else {
                $error[]    =   $this->DB->sql;
            }
            
            //Update các thông tin: Số ĐT khác, họ tên
            //Có một số trường hợp như khách tự đặt ở ngoài website thì ko cho sửa thông tin, vì có thể khách điền sai tên, email... mà update thì thành sai luôn.
            if (!empty($data['modify'])) {
                $this->updateCustomer($data);
            }
        }
        
        //Check return ở đây để còn insert history
        if ($result) {
            
            /** Nếu có nội dung thì sẽ update vào history **/
            if (!empty($data['content'])) {
                $this->createHistory([
                    'id'        =>  $customer_id,
                    'content'   =>  $data['content']
                ]);
            }
            
            //Return
            return true;
        }
        
        //Return default
        save_log('error_update_data.cfn', 'Data: ' . json_encode($data) . '. Error: ' . json_encode($error));
        return false;
        
    }
    
    /**
     * CustomerModel::createHistory()
     * Tạo lịch sử cho Customer Data
     * @param mixed $data[id, content, [time]]
     * @return bool
     */
    function createHistory($data) {
        
        //Lấy User ID
        if (isset($data['user'])) {
            $user_id    =   $data['user'];
        } else {
            $user_id    =   ACCOUNT_ID;
        }
        
        $Query  =   new GenerateQuery('customer_history');
        $Query->add('cuhi_customer_id', DATA_INTEGER, $data['id'])
                ->add('cuhi_content', DATA_STRING, $data['content'])
                ->add('cuhi_user_id', DATA_INTEGER, $user_id)
                ->add('cuhi_time_create', DATA_INTEGER, !empty($data['time']) ? $data['time'] : CURRENT_TIME)
                ->add('cuhi_type', DATA_INTEGER, !empty($data['type']) ? $data['type'] : 0)
                ->add('cuhi_ip', DATA_STRING, get_client_ip())
                ->add('cuhi_url', DATA_STRING, $_SERVER['REQUEST_URI']);
        if ($Query->validate()) {
            if ($this->DB->pass()->execute($Query->generateQueryInsert()) > 0) {
                return true;
            }
        }
        
        //Return
        save_log('error_update_data_history.cfn', 'Data: ' . json_encode($data) . '. Error: ' . json_encode($Query->getError()));
        return false;
    }

    /**
     * Update thêm số lượng 1 request cho Customer
     */
    function updateTotalRequest($customer_id) {

        if ($this->DB->execute("UPDATE customer
                                SET cus_total_request = cus_total_request + 1,
                                    cus_last_request = " . CURRENT_TIME . ",
                                    cus_last_cskh = " . CURRENT_TIME . ",
                                    cus_last_user_process = " . ACCOUNT_ID . "
                                WHERE cus_id = $customer_id " . sql_company('cus_') . sql_hotel('cus_') . "
                                LIMIT 1") > 0) {
            return true;
        }

        return false;
    }
    
    /**
     * CustomerModel::updateTotalBooking()
     * Update thêm 01 Booking hoàn thành của Data và tổng số tiền đã đặt
     * @param int $customer_id
     * @param double $money
     * @return bool
     */
    function updateTotalBooking($customer_id, $money) {
        
        $data_info  =   $this->DB->query("SELECT cus_total_booking, cus_total_money FROM customer WHERE cus_id = $customer_id" . sql_company('cus_') . sql_hotel('cus_'))->getOne();
        if (empty($data_info)) {
            // Bỏ log đi vì có đơn khách k để SĐT thì đang gán vào USER_SENNET
            // save_log('data_not_found.cfn', 'Data ID: ' . $customer_id);
            return false;
        }
        
        if ($this->DB->execute("UPDATE customer
                                SET cus_total_booking = cus_total_booking + 1,
                                    cus_total_money = cus_total_money + $money,
                                    cus_last_booking = " . CURRENT_TIME . "
                                WHERE cus_id = $customer_id " . sql_company('cus_') . " " . sql_hotel('cus_') . "
                                LIMIT 1") > 0) {
            
            /** Lưu log **/
            global  $Log;
            //Phải check isset vì nếu tạo từ ngoài website sẽ ko có class Log
            if (isset($Log)) {
                $data_new   =   $this->DB->query("SELECT cus_total_booking, cus_total_money FROM customer WHERE cus_id = $customer_id" . sql_company('cus_') . sql_hotel('cus_'))->getOne();
                $Log->setTable('customer')->genContent($data_info, $data_new)->createLog($customer_id);
            }
            
            return true;
        }
        
        //Default return
        return false;
    }
    
    /**
     * CustomerModel::updateLastCSKH()
     * 
     * @param mixed $customer_id
     * @return
     */
    function updateLastCSKH($customer_id) {
        return $this->DB->execute("UPDATE customer SET cus_last_cskh = " . CURRENT_TIME . ", cus_last_user_process = " . ACCOUNT_ID . " WHERE cus_id = $customer_id" . sql_company('cus_') . sql_hotel('cus_'). " LIMIT 1");
    }
    
    /**
     * CustomerModel::updateLastUserProcess()
     * 
     * @param mixed $customer_id
     * @return
     */
    function updateLastUserProcess($customer_id, $user_id = 0) {

        if ($user_id == 0) $user_id =   ACCOUNT_ID;
        
        return $this->DB->execute("UPDATE customer SET cus_last_user_process = $user_id WHERE cus_id = $customer_id " . sql_company('cus_') . sql_hotel('cus_'). " LIMIT 1");
    }
    
}

?>