<?php 

namespace src\Libs\PHPMailer;
use src\Libs\Utils;
use src\Services\CommonService;

class Mailer {

    public $mail;

    public function __construct()
    {
        //Create an instance; passing `true` enables exceptions
        $this->mail = new PHPMailer(true);

        //Server settings
        $this->mail->SMTPDebug  = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $this->mail->CharSet = 'UTF-8';
        $this->mail->isSMTP();                                            //Send using SMTP
        $this->mail->Host       = Utils::env('MAIL_HOST');                     //Set the SMTP server to send through
        $this->mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $this->mail->Username   = Utils::env('MAIL_USERNAME');           //SMTP username
        $this->mail->Password   = Utils::env('MAIL_PASSWORD');                     //SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $this->mail->Port       = Utils::env('MAIL_PORT');       
    }

    public function setFrom($email, $fullname) {
        $this->mail->setFrom($email, $fullname);
        return $this;
    }
    
    public function addAddress($email, $fullname='') {
        // Dev thì gửi về đây 
        // Không có email cũng gửi về đây nốt
        if (Utils::isDev() || Utils::isLocal() || empty($email)) {
            foreach (CommonService::explode(Utils::env('MAIL_RECEIVER_TEST', ''), ',', 'trim') as $email) {
                $this->mail->addAddress($email, $fullname);
            }
        } else {
            $this->mail->addAddress($email, $fullname);
        }
        return $this;
    }
    
    public function addReplyTo($email, $fullname='') {
        $this->mail->addReplyTo($email, $fullname);
        return $this;
    }

    public function setSubject($content) {
        $this->mail->Subject = $content;
        return $this;
    }

    public function addCC($email, $fullname = '') {
        // Dev thì gửi về đây 
        // Không có email cũng gửi về đây nốt
        if (Utils::isDev() || Utils::isLocal()) {
            foreach (CommonService::explode(Utils::env('MAIL_RECEIVER_TEST', ''), ',', 'trim') as $email) {
                $this->mail->addCC($email, $fullname);
            }
        } else {
            $this->mail->addCC($email, $fullname);
        }
        return $this;
    }

    public function setBody($content) {
        $this->mail->isHTML(true);
        $this->mail->Body = $content;
        return $this;
    }

    public function setAltBody($content) {
        $this->mail->AltBody = $content;
        return $this;
    }
    
    // Trả về lỗi nếu có, nếu k sẽ là true
    public function send($callback=null) {
        if ($callback !== null) {
            $callback($this->mail);
        }
        
        try {
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            $errorMessage = sprintf(
                "[%s] Exception: %s in %s on line %d\nStack trace:\n%s\n\n",
                date('Y-m-d H:i:s'),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
            save_log('exceptions.log', $errorMessage);
            return false;  
            // return "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
}