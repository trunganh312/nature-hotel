<?
require_once($path_core . 'Classes/GenerateQuery.php');
require_once($path_core . 'Model/UserModel.php');
require_once($path_core . 'Classes/DataTable.php');

// Người giới thiệu phải thỏa mãn các yêu cầu
// Trạng thái đang hoạt động và đã đc xác thực
// Không phải id hiện tại và có số tiền thành công lớn hơn 0
// Tránh tình trạng spam tài khoản
$action =   getValue('action', GET_STRING, GET_POST, '');

$error_email_referral   =   [];
$email_referral         =   getValue('email_referral', GET_STRING, GET_POST, '', 1);
$email_referral_add_is  =   $User->info['use_completed_money'] > 0;

if ($email_referral_add_is && $action == 'email_referral') {
    
    if (!validate_email($email_referral)) {
        $error_email_referral[]   =   'Địa chỉ email không hợp lệ';
    }
    
    if ($email_referral == $User->email) {
        $error_email_referral[]   =   'Bạn không thể giới thiệu tới chính bạn';
    }
    
    //Check xem email đã có tk chưa
    $user_info  = $DB->query("SELECT use_id 
                                FROM user 
                                WHERE use_email = '{$email_referral}' LIMIT 1")
                                ->getOne();
    
    if (!empty($user_info)) {
        $error_email_referral[]   =   'Email này đã có tài khoản trên hệ thống Vietgoing.com';
    }
    
    //Check xem đã được giới thiệu hay chưa
    $email_referral_info  = $DB->query("SELECT urh_id 
                                        FROM user_referral_history 
                                        WHERE urh_email_referral = '{$email_referral}' LIMIT 1")
                                        ->getOne();
    if (!empty($email_referral_info)) {
        $error_email_referral[]   =   'Email này đã được bạn hoặc người khác giới thiệu';
    }

    if (empty($error_email_referral)) {
        // Tạo voucher cho tài khoản được giới thiệu
        // Voucher này chỉ đc tạo để giữ chỗ và giúp xác thực khi đặt đơn
        $voucher_code = (new UserModel)->createVoucherReferralB();

        // Tạo lịch sử giới thiệu và lưu lại mã voucher
        $Query  =   new GenerateQuery('user_referral_history');
        $Query->add('urh_user_id', DATA_INTEGER, $User->id)
            ->add('urh_email_referral', DATA_STRING, $email_referral)
            ->add('urh_referral_code', DATA_STRING, $voucher_code)
            ->add('urh_created_at', DATA_INTEGER, CURRENT_TIME)
            ->add('urh_status', DATA_INTEGER, STT_NEW)
            ->add('urh_commission', DATA_DOUBLE, $cfg_website['con_referral_discount_a'])
            ->add('urh_updated_at', DATA_INTEGER, CURRENT_TIME);
        
        $referral_id    =   $DB->executeReturn($Query->generateQueryInsert());
        
        if ($referral_id > 0) {
            
            //Gửi email cho người được giới thiệu
            $Mailer =   new Mailer;
            $Mailer->sendEmailReferral($referral_id, $voucher_code);
            
            set_session_toastr();
            $_SESSION['referral_result']    =   $voucher_code;
            redirect_url($_SERVER['REQUEST_URI']);
            
        } else {
            $error_email_referral[]   =   'Đã có lỗi xảy ra, vui lòng thử lại';
        }
        
    }
}

/** --- DataTable --- **/
$Table  =   new DataTable('user_referral_history', 'urh_id');
$Table->column('urh_email_referral', 'Người được giới thiệu', TAB_TEXT)
    ->column('urh_referral_code', 'Mã giới thiệu', TAB_TEXT)
    ->column('urh_created_at', 'Ngày giới thiệu', TAB_DATE)
    ->column('urh_commission', 'Hoa hồng (VNĐ)', TAB_NUMBER)
    ->column('urh_voucher_code', 'Trạng thái', TAB_TEXT)
    ->setShowTimeMinute(true);
$Table->addSQL("SELECT *
                FROM user_referral_history
                WHERE urh_user_id = {$User->id}
                ORDER BY urh_status ASC, urh_created_at DESC
            ");
?>