<?php

use Symfony\Component\Dotenv\Dotenv;
use src\Libs\Utils;

$dotenv = new Dotenv();
$dotenv->load(PATH_ROOT . '/.env');

date_default_timezone_set(Utils::env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'));

/** --- Domain --- **/
define('DOMAIN_CRM', Utils::env('APP_DOMAIN_CRM'));
define('DOMAIN_USER', Utils::env('APP_DOMAIN_USER'));
define('DOMAIN_STATIC', Utils::env('APP_DOMAIN_STATIC'));
define('BRAND_NAME', Utils::env('BRAND_NAME'));
define('BRAND_DOMAIN', Utils::env('BRAND_DOMAIN'));
define('DOMAIN_WEB', Utils::env('DOMAIN_WEB'));

Utils::cors();

// Thiet lap save log o day
/**
 * save_log().
 *
 * @param mixed $filename (Bao gom extension)
 * @param mixed $content
 *
 * @return void
 */
function save_log($filename, $content, $info = true) {
    $log_path   =  PATH_ROOT . '/storage/logs/';

    $file_path  =  $log_path . $filename;

    $handle  =   @fopen($file_path, "a");

    //Nếu 2 lần mà ko mở được thì exit
    if (!$handle) {
        die('Cannot save log!');
    }

    if ($info) {
        $content =  date("d/m/Y H:i:s") . " " . @$_SERVER['SERVER_NAME'] . @$_SERVER["REQUEST_URI"] . "\n" . "IP:" . @$_SERVER['REMOTE_ADDR'] . "\n" . $content;
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $content    .=  "\nReferer: " . $_SERVER['HTTP_REFERER'];
        }
    }

    fwrite($handle, $content . "\n=======================================================\n");
    fclose($handle);
}

// Thiết lập custom exception handler
if (Utils::isPro() || Utils::isDemo()) {
    /**
     * Custom exception handler để xử lý và ghi log ngoại lệ.
     *
     * @param Throwable $exception Đối tượng ngoại lệ
     */
    function customExceptionHandler($exception) {
        $errorMessage = sprintf(
            "[%s] Exception: %s in %s on line %d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        // Ghi log vào file hoặc hệ thống giám sát
        save_log('exceptions.log', $errorMessage);

        // Nếu là môi trường phát triển, hiển thị lỗi chi tiết
        if (Utils::isDev() === 'development') {
            echo nl2br($errorMessage); // Hiển thị lỗi dễ đọc hơn
        } else {
            // Trả về phản hồi hợp lý theo môi trường (Web hoặc API)
            if (php_sapi_name() !== 'cli') {
                http_response_code(500);
                echo '<p style="text-align:center">Hệ thống đang được bảo trì, vui lòng quay lại sau ít phút!</p>';
            } else {
                fwrite(STDERR, "Có lỗi xảy ra, kiểm tra logs để biết chi tiết.\n");
            }
        }
    }

    set_exception_handler('customExceptionHandler');
}
