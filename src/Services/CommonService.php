<?php

namespace src\Services;

use DateTime;
use src\Libs\Utils;

class CommonService {

    static public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    static public function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    static public function decode($data, $base64 = false): array {
        $data = empty($data) ? '' : $data;
        if ($base64) {
            $data = base64_decode($data);
        }
        $data = json_decode($data, true);
        $data = empty($data) || !is_array($data) ? [] : $data;
        return $data;
    }

    static public function encode($data, $base64 = false): string {
        if (!is_array($data) && !is_object($data)) return '';

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($base64) {
            $data = base64_encode($data);
        }
        return $data;
    }

    static public function implode($data, $char = ',', $format = 'intval') {
        $data = array_map($format, $data);
        return implode($char, $data);
    }
    
    static public function explode($text, $char = ',', $format = 'intval') {
        $data = explode($char, $text);
        $data = array_map($format, $data);
        return $data;
    }

    static public function cleaningArrInt($data) {
        foreach ($data as $i => $v) {
            $data[$i] = (int) $v;
        }
        return $data;
    }

    static public function resJson(array $data = [], int $status=1, string $msg = 'Successfuly!') {
        if (Utils::isPro() || Utils::isDemo()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        echo self::encode([
            "success" => $status, // 0 | 1
            "data" => $data,
            "msg" => $msg
        ]);
        exit;
    }

    static public function timeToTextRange(array $data) {
        return date('d/m/Y', $data[0]) . ' - ' . date('d/m/Y', $data[1]);
    }

    static public function numberDay(int $from, int $to) {
        return (int) (new DateTime("@$from"))->diff(new DateTime("@$to"))->format('%a');
    }

    static public function resApp(array $data = []) {
        $data = self::encode($data);
        echo "<script>window.appData = {$data};</script>";
    }

    public static function getInput($field) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($input) && array_key_exists($field, $input)) {
            return $input[$field];
        }
        
        return null;
    }
}
