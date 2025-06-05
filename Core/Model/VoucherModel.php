<?
/** 
 * Class VoucherModel
 * @author NQH
 * 10-01-2019
 */

class VoucherModel extends Model {
    
    public  $error_code =   ''; //Lỗi báo khi nhập Voucher
    
    function __construct() {
        parent::__construct();
    }
    
    /**
     * VoucherModel::generateRandomCode()
     * Generate ra ma voucher_code random
     * @param integer $maxchar
     * @return voucher_code
     */
    function generateRandomCode($maxchar = 7) {
        //Một số ký tự ko nên cho vào mã để tránh viết nhầm
        $arr_decline    =   ['O', 'I'];
        
        $code   =   '';
        for ($i = 0; $i < $maxchar; $i++) {
            $code   .=  chr(rand(65, 90));
        }
        
        //Kiểm tra xem nếu mã bị trùng với mã khác thì đệ quy lại
        $data   =   $this->getVoucherInfoByCode($code, "vou_id");
        if (!empty($data)) {
            save_log('duplicate_voucher_code.cfn', $code);
            return $this->generateRandomCode($maxchar);
        }
        
        return $code;
    }
    
    /**
     * VoucherModel::getVoucherInfoByCode()
     * Lay thong tin cua Voucher theo ma
     * @param mixed $code
     * @param string $field
     * @return row
     */
    function getVoucherInfoByCode($code, $field = '*') {
        $data   =   $this->DB->query("SELECT " . $field . "
                                        FROM voucher
                                        WHERE vou_code = '" . clear_injection($code) . "'
                                        LIMIT 1")
                                        ->getOne();
        return $data;
    }
    
    
    /**
     * VoucherModel::getVoucherInfoByID()
     * Lay thong tin cua Voucher theo ID
     * @param mixed $id
     * @param string $field
     * @return row
     */
    function getVoucherInfoByID($id, $field = '*') {
        $data   =   $this->DB->query("SELECT " . $field . "
                                        FROM voucher
                                        WHERE vou_id = " . intval($id) . "
                                        LIMIT 1")
                                        ->getOne();
        return $data;
    }
    
    
    /**
     * VoucherModel::getMoneyDiscountByCode()
     * Tinh toan so tien duoc giam khi ap dung Voucher code
     * @param mixed $code
     * @param integer $total_money
     * @return money integer
     */
    function getMoneyDiscountByCode($code, $total_money = 0) {
        
        //Số tiền được giảm
        $money  =   0;
        
        //Upper
        $code   =   strtoupper($code);
        if (empty($code)) {
            return $money;
        }
        
        //Lấy thông tin của mã giảm giá
        $data   =   $this->getVoucherInfoByCode($code);
        
        //Nếu ko tồn tại hoặc chưa được kích hoạt
        if (empty($data) || $data['vou_active'] != 1) {
            $this->error_code   =   'Mã giảm giá không hợp lệ';
            return $money;
        }
        
        //Nếu số lượt sử dụng đã hết
        if ($data['vou_time_used'] >= $data['vou_quantity']) {
            $this->error_code   =   'Mã giảm giá này đã được sử dụng';
            return $money;
        }
        
        //Nếu hết hạn
        if ($data['vou_time_expire'] < CURRENT_TIME) {
            $this->error_code   =   'Mã giảm giá này đã hết hạn sử dụng';
            return $money;
        }
        
        //Nếu hợp lệ
        //Tính số tiền được giảm theo loại tương ứng của
        switch ($data['vou_type']) {
            //Giảm theo phần trăm
            case VOUCHER_TYPE_PERCENT:
                $money  =   round_number($total_money * $data['vou_value'] / 100);
                break;
            
            //Giảm theo số tiền
            case VOUCHER_TYPE_MONEY:
                $money  =   (double)$data['vou_value'];
                break;
        }
        
        return $money;
        
    }
    
    
    /**
     * VoucherModel::getVoucherDiscountAllProduct()
     * Lay thong tin cua voucher giam gia toan bo SP (Neu co, VD SN, BlackFriday...)
     * @return row
     */
    function getVoucherDiscountAllProduct() {
        $voucher    =   $this->DB->query("SELECT vou_id, vou_code, vou_type, vou_time_expire, vou_title, vou_message, vou_range_discount
                                            FROM voucher
                                            WHERE vou_active = 1 AND vou_type = " . VOUCHER_TYPE_RANGE . " AND vou_time_expire >= " . time() . "
                                            ORDER BY vou_time_create DESC
                                            LIMIT 1")
                                            ->getOne();
        return $voucher;
    }
    
    /**
     * VoucherModel::getRangeOfDiscount()
     * Lay ra cac khoang giam gia cua Voucher Sale all
     * @param mixed $voucher
     * @return array Range of discount [min, max, discount]
     */
    function getRangeOfDiscount($voucher) {
        
        $range_discount =   [];
        
        if (!empty($voucher) && $voucher['vou_range_discount'] != '') {
            $range_discount =   json_decode($voucher['vou_range_discount'], true);
        }
        
        return $range_discount;
    }
    
    
    /**
     * VoucherModel::updateCodeUsed()
     * Update lượt sử dụng cho Voucher khi Vouchẻ được áp dụng cho đơn hàng
     * @param mixed $code
     * @return boolean
     */
    function updateCodeUsed($code) {
        return $this->DB->execute("UPDATE voucher SET vou_time_used = vou_time_used + 1 WHERE vou_code = '" . $code . "' LIMIT 1");
    }
    
    /**
     * VoucherModel::updateCodeCancel()
     * Update lượt sử dụng cho Voucher khi Voucher được hủy hoặc đơn hàng bị hủy 
     * @param mixed $code
     * @return boolean
     */
    function updateCodeCancel($code) {
        return $this->DB->execute("UPDATE voucher SET vou_time_used = vou_time_used - 1 WHERE vou_code = '" . $code . "' LIMIT 1");
    }
    
    
    /**
     * VoucherModel::getMoneyBonusNextOrder()
     * Lay muc tien bonus dua tren gia tri cua don hang truoc
     * @param mixed $money: Tong tien cua don hang truoc
     * @return void
     */
    function getMoneyBonusNextOrder($money) {
        
        $bonus  =   100000;
        
        if ($money >= 7000000 && $money < 15000000) {
            $bonus  =   200000;
        } else if ($money >= 15000000 && $money < 25000000) {
            $bonus  =   300000;
        } else if ($money >= 25000000 && $money < 35000000) {
            $bonus  =   400000;
        } else if ($money >= 35000000) {
            $bonus  =   500000;
        }
        
        return $bonus;
    }
}
?>