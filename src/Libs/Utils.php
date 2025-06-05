<?php

namespace src\Libs;

use Carbon\Carbon;
use Exception;
use PHPViet\NumberToWords\Transformer;

class Utils {

    static function env($k, $def=null) {
        return isset($_SERVER[$k]) ? $_SERVER[$k] : $def;
    }

    static function isPro()  {
        return self::env('APP_ENV') == 'pro';
    }
    
    static function isDev()  {
        return self::env('APP_ENV') == 'dev';
    }
    
    static function isLocal()  {
        return self::env('APP_ENV') == 'local';
    }


    static function isDemo()  {
        return self::env('APP_ENV') == 'demo';
    }

    /**
     *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
     *  origin.
     *
     *  In a production environment, you probably want to be more restrictive, but this gives you
     *  the general idea of what is involved.  For the nitty-gritty low-down, read:
     *
     *  - https://developer.mozilla.org/en/HTTP_access_control
     *  - https://fetch.spec.whatwg.org/#http-cors-protocol
     *
     */
    static function cors() {
        
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            // header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        
        // Access-Control headers are received during OPTIONS requests
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
            exit(0);
        }
    }

    static function auth() {
        global $Auth;
        return $Auth;
    }

    static function clientIP() {
        return array_get($_SERVER, 'REMOTE_ADDR', 'Unknown IP');
    }
    
    /**
     * Lấy giá trị từ các trường trong mảng đa chiều để hợp nhất nhất thành mảng một chiều và xoá các giá trị trùng lặp
     *
     * @param  array $data
     * @param  string $fields
     * @return array
     */
    static function arrayValues(array $data, string $fields) {
        $fields = explode(',', $fields);

        $values = [];
        foreach ($data as $row) {
            foreach ($fields as $k) {
                $values[] = $row[$k];
            }
        }

        return array_unique($values);
    }

    static function createFromTimestamp($timestamp=CURRENT_TIME) {
        return Carbon::createFromTimestamp($timestamp)->timezone(Utils::env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'));
    }

    static function date($timestamp) {
        return Utils::createFromTimestamp($timestamp)->format('d/m/Y');
    }
    
    static function datetime($timestamp) {
        return Utils::createFromTimestamp($timestamp)->format('d/m/Y H:i');
    }
    
    static function time($timestamp) {
        return Utils::createFromTimestamp($timestamp)->format('H:i');
    }

    /**
     * Chuyển chuỗi thành timestamp
     *
     * $date_string Chuỗi ngày tháng bất kỳ
     *
     * @return int
     */
    static function strtotime($date_string, $format='d/m/Y', $start_day = true) {
        $result = Carbon::createFromFormat($format, $date_string)->timezone(Utils::env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'));
        
        if ($start_day) return $result->startOfDay()->timestamp;
        return $result->endOfDay()->timestamp;
    }
    
    /**
     * Trả về hàm ghi log
     *
     * @return \LogModel
     */
    static function logger() : \LogModel {
        global  $Log;
        return $Log;
    }

    static function urlStatic($path) {
        return DOMAIN_STATIC . $path;
    }

    
    /**
     * Truncate the string to the given length.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    static function strLimit($value, $limit = 50, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
    }


    /**
     * pushMessageTelegram()
     * Push thong bao vao group Tele
     * @param mixed $msg
     * @return
     */
    static function pushMessageTelegram($msg, $chat_id='', $icon='🔔') {
        
        if (empty($chat_id)) return;
        $msg    =   'icon ' . $msg;
        $curl   =   curl_init();
        $params =   [
            'text'      =>  $msg,
            'chat_id'   =>  $chat_id,
            'parse_mode' => 'HTML',
        ];
        $params =   str_replace('icon', $icon, http_build_query($params));

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.telegram.org/bot'. self::env('TELEGRAM_BOT_TOKEN') .'/sendMessage?' . $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response   =   curl_exec($curl);
        curl_close($curl);
        // dd($response);
    }

    static function numberToWords($value) {
        $transformer = new Transformer();
        return ucfirst($transformer->toCurrency($value));
    }
    
    /**
     * Tạo mã QR cho thanh toán
     *
     * @param  mixed $value
     * @param  mixed $bank_bin
     * @param  mixed $account_id
     * @param  mixed $note
     * @return void
     */
    static function paymentContentQR($value, $bank_bin, $account_id, $note) {
        $qr = new QRPayment();
        $qr->set_transaction_amount($value)
           ->set_beneficiary_organization($bank_bin, $account_id)
           ->set_additional_data_field_template($note);
        
        return $qr->build();
    }
    
    /**
     * Kiểm tra xem có cần check hotel không
     *
     * @return boolean
     */
    static function checkRequiredFieldHotel() {
        if (empty(self::auth())) return false;

        // Nếu là hms thì cần kiểm tra thêm trường hotel_id trong truy vấn
        try {
            return self::auth()->isHMS();
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Trả về hotel id hiện tại
     *
     * @return int
     */
    static function hotelID() {
        if (defined('HOTEL_ID') && HOTEL_ID > 0) return HOTEL_ID;
        elseif (empty(self::auth())) return 0;
        return self::auth()->getHotelId();
    }

    /**
     * Trả về company id hiện tại
     *
     * @return int
     */
    static function companyID() {
        if (defined('COMPANY_ID') && COMPANY_ID > 0) return COMPANY_ID;
        elseif (empty(self::auth())) return 0;
        return self::auth()->getCompanyId();
    }


    /**
     * Phân tích URI kết nối cơ sở dữ liệu và trích xuất thông tin.
     *
     * @param string $uri Chuỗi URI của cơ sở dữ liệu (ví dụ: "mysqli://username:password@host:port/database")
     * @return array Mảng chứa thông tin kết nối (driver, username, password, host, port, database)
     * @throws Exception Nếu không thể phân tích chuỗi URI
     */
    static function parseDatabaseUri($uri) {
        // Phân tích chuỗi URI
        $parts = parse_url($uri);
    
        if ($parts === false) {
            throw new Exception("Không thể phân tích chuỗi URI Database.");
        }
    
        // Trích xuất các thông tin từ URI
        $databaseInfo = [
            'driver' => isset($parts['scheme']) ? $parts['scheme'] : null,
            'username' => isset($parts['user']) ? $parts['user'] : null,
            'password' => isset($parts['pass']) ? urldecode($parts['pass']) : null,
            'host' => isset($parts['host']) ? $parts['host'] : null,
            'port' => isset($parts['port']) ? $parts['port'] : null,
            'database' => isset($parts['path']) ? ltrim($parts['path'], '/') : null
        ];
    
        return $databaseInfo;
    }
}