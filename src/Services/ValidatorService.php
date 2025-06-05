<?php

namespace src\Services;

use Rakit\Validation\Validator;

// https://github.com/rakit/validation
class ValidatorService {

    static private $validation;

    /**
     * Khởi tạo đối tượng
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return void
     */
    static public function make(array $data, array $rules, array $messages=[]) {
        $validator  = new Validator([
            'required' => 'Vui lòng nhập :attribute',
            'email' => 'Email :attribute không hợp lệ',
            'max' => ':attribute vượt quá độ dài cho phép (:max)'
        ]);
        static::$validation = $validator->make($data, $rules, $messages);
    }

    /**
     * Thiết lập tên hiển thị trong msg cho trường dữ liệu
     *
     * @param array $aliases
     * @return void
     */
    static public function setAliases(array $aliases) {
        static::$validation->setAliases($aliases);
    }
    
    /**
     * Thiết lập thông báo tuỳ chỉnh
     *
     * @param array $messages
     * @return void
     */
    static public function setMessages(array $messages) {
        static::$validation->setMessages($messages);
    }

    /**
     * Chạy kiểm tra và trả về kết quả
     *
     * @return boolean
     */
    static public function validate() : bool {
        static::$validation->validate();
        return static::$validation->passes();
    }

    /**
     * Lấy ds lỗi
     *
     * @return array
     */
    static public function errors() : array {
        return static::$validation->errors()->all();
    }


}