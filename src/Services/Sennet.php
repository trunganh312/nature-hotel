<?php
namespace src\Services;

use Exception;
class Sennet
{
    private static $key = 'V65hMKh3qb1q6RpK2PYw0';
    private static $app_id = '4738773754589267877';
    private static $base_url = 'http://hub.sennet.local';

    private static function request($path, $option = [], $signature = '')
    {
        extract($option);

        if (!isset($method)) {
            $method = 'GET';
        }
        $ch = curl_init(self::$base_url . $path);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-APP-ID: ' . self::$app_id,
            'X-SIGNATURE: ' . $signature,
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            if (isset($post_data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Lỗi cURL: ' . $error);
        }
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    /**
     * Gửi dữ liệu booking sang Sennet để giữ phòng
     * @param array $data Thông tin booking
     * @return array Kết quả từ API Sennet
     */
    public static function requestHoldRoom($data)
    {
        $data['type'] = 'hold';
        $json_data = json_encode($data);
        $data_encode = base64_encode($json_data);
        $signature = hash_hmac('sha256', $data_encode, self::$key);
        $response = self::request('/api/web/hold_room', [
            'method' => 'POST',
            'post_data' => $data_encode
        ], $signature);
        error_log($response);
        return json_decode($response, true) ?: ['error' => 1, 'message' => 'Phản hồi không hợp lệ'];
    }

    /**
     * Gửi dữ liệu booking sang Sennet
     * @param array $data Thông tin booking
     * @return array Kết quả từ API Sennet
     */
    public static function sendBookingSennet($data)
    {
        $json_data = json_encode($data);
        $data_encode = base64_encode($json_data);
        $signature = hash_hmac('sha256', $data_encode, self::$key);
        $response = self::request('/api/web/create_booking', [
            'method' => 'POST',
            'post_data' => $data_encode
        ], $signature);
        error_log($response);
        return json_decode($response, true) ?: ['success' => false, 'message' => 'Phản hồi không hợp lệ'];
    }

    /**
     * Gửi dữ liệu booking sang Sennet để giữ phòng
     * @param array $data Thông tin booking
     * @return array Kết quả từ API Sennet
     */
    public static function requestUnHoldRoom($data)
    {
        $data['type'] = 'unhold';
        $json_data = json_encode($data);
        $data_encode = base64_encode($json_data);
        $signature = hash_hmac('sha256', $data_encode, self::$key);
        $response = self::request('/api/web/hold_room', [
            'method' => 'POST',
            'post_data' => $data_encode
        ], $signature);
        error_log($response);
        return json_decode($response, true) ?: ['error' => 1, 'message' => 'Phản hồi không hợp lệ'];
    }
    
}