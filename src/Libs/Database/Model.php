<?php

namespace src\Libs\Database;

use src\Facades\Connection;
use RuntimeException;
use src\Facades\DB;
use src\Libs\Utils;

/**
 * Các hàm cơ sở hỗ trợ query builder cho model dựa trên dibi opensource
 * 
 * Version: 1.0
 * User: TamPV
 * Email: tampv.work@gmail.com
 * Date: 2024-03-30 13:41:36
 */
class Model {

    private $ds; // Lưu đối tượng ExtraDataSource từ dibi
    private $table; // Lưu tên table được khai báo từ model con khi kế thừa
    private $company_key; // Lưu tên cột company id được khai báo từ model con khi kế thừa
    private $hotel_field; // Lưu tên cột hotel id được khai báo từ model con khi kế thừa
    private $where_fields = [];

    /**
     * __construct
     *
     * @param  string $table
     * @return void
     */
    function __construct(string $table, string $company_key = '', string $hotel_field = '') {
        $this->table    = $table;
        $this->company_key    = $company_key;
        $this->hotel_field    = $hotel_field;

        // Khởi tạo ds với kết nối db chính của hệ thống
        $this->ds       = new ExtraDataSource($this->table, Connection::main());
    }

    /**
     * Tắt kiểm tra mysql có thiếu không truyền company_id không
     *
     * @return object
     */
    function pass() {
        DB::pass();
        return $this;
    }

    /**
     * Trả về tên table của model hiện tại
     *
     * @return this
     */
    public function table() {
        return $this->table;
    }

    public function companyKey() {
        return $this->company_key;
    }
    
    public function hotelField() {
        return $this->hotel_field;
    }

    /**
     * Selects columns to query.
     * @param  string|array  $cols  Tên các trường cần select
     * @return this
     */
    public function select(...$cols) {
        if (is_array($cols[0])) {
            $cols = $cols[0];
        } else if (count($cols) === 1 && is_string($cols[0])) {
            $cols = explode(',', $cols[0]);
        }
        $cols = array_map('trim', $cols);

        foreach ($cols as $col) {
            $col = strtolower($col);
            $col = explode('as', $col);
            $col = array_map('trim', $col);

            if (count($col) == 2) {
                $this->ds->select($col[0], $col[1]);
            } else {
                $this->ds->select($col[0]);
            }
        }

        return $this;
    }

    /**
     * Xác định phạm vi bản ghi truy xuất từ cơ sở dữ liệu
     *
     * @param  int $limit Số lượng bản ghi lấy ra
     * @param  ?int $offset Bắt đầu lấy từ bản ghi
     * @return void
     */
    function limit(int $limit, ?int $offset = null) {
        $this->ds->applyLimit($limit, $offset);
        return $this;
    }

    /**
     * Xác định điều kiện sắp xếp của dữ liệu trả về
     *
     * @param  string|array $cols
     * @param  string $direction
     * @return static
     */
    public function orderBy(string|array $cols, string $direction = 'ASC'): static {
        $this->ds->orderBy($cols, $direction);
        return $this;
    }

    public function whereRaw($sql, $value = []) {
        $this->ds->where($sql, $value);
        return $this;
    }

    /**
     * Adds conditions to query.
     */
    public function where(...$conds) {
        if (is_array($conds[0])) {
            foreach ($conds[0] as $field => $value) {
                if (is_integer($value)) {
                    $this->ds->where("`$field` = %i", $value);
                } else if (is_float($value)) {
                    $this->ds->where("`$field` = %f", $value);
                } else {
                    $this->ds->where("`$field` = %s", $value);
                }
                $this->where_fields[] = $field;
            }
            return $this;
        }

        if (count($conds) > 3 || count($conds) < 2) {
            dd('Hàm where yêu cầu > 1 tham số');
        }

        if (count($conds) === 3) {
            $field      = $conds[0];
            $operator   = $conds[1];
            $value      = $conds[2];
        } else {
            $field      = $conds[0];
            $operator   = '=';
            $value      = $conds[1];
        }

        if (is_integer($value)) {
            $this->ds->where("`$field` $operator %i", $value);
        } else if (is_float($value)) {
            $this->ds->where("`$field` $operator %f", $value);
        } else {
            $this->ds->where("`$field` $operator %s", $value);
        }
        $this->where_fields[] = $field;
        return $this;
    }

    public function join($table, $field_left, $field_right) {
        $this->ds->join($table, $field_left, $field_right);
        return $this;
    }

    public function leftJoin($table, $field_left, $field_right) {
        $this->ds->leftJoin($table, $field_left, $field_right);
        return $this;
    }

    public function in($col, $values, $format = '') {
        if (empty($values)) {
            dd('Hàm [in] yêu cầu tham số thứ 2 phải có giá trị!');
        }
        $this->where_fields[] = $col;
        $this->ds->in($col, $values, $format);
        return $this;
    }

    public function count() {
        return (int) $this->ds->count();
    }

    function pluck(string $k, string $v = null) {
        return DB::query($this->sql())->pluck($k, $v);
    }

    function toArray() {
        return DB::query($this->sql())->toArray();
    }

    function getOne() {
        return DB::query($this->sql())->getOne();
    }

    /**
     * Thực hiện cập nhật data theo điều kiện chỉ định và trả về số bản ghi thay đổi
     *
     * @param  array $data
     * @param  array $conds
     * @return int
     */
    public function update(array $data, array $conds = [1]) {
        if (!empty($this->company_key) && DB::isCheckFieldRequired() && !in_array($this->company_key, $conds)) {
            $conds[$this->company_key] = Utils::companyID();
        }

        // Kiểm tra xem có cần phải thêm hotel id vào sql không
        if (
            !empty($this->hotel_field)
            && DB::isCheckFieldRequired('hotel')
            && Utils::checkRequiredFieldHotel()
            && !in_array($this->hotel_field, $conds)
        ) {
            $conds[$this->hotel_field] = Utils::hotelID();
        }

        return DB::update($this->table, $data, $conds);
    }

    /**
     * Thực hiện thêm mới data và trả về id bản ghi
     *
     * @param  string $table
     * @param  array $data
     * @param  array $conds
     * @return int
     */
    public function insert(array $data) {
        return DB::insert($this->table, $data);
    }

    /**
     * Thực hiện xoá bản ghi theo điều kiện chỉ định và trả về số bản ghi bị xoá
     *
     * @param  string $table
     * @param  array $conds
     * @return int
     */
    public function delete(array $conds = [1]) {
        return DB::delete($this->table, $conds);
    }

    public function company($field) {
        $this->where($field, Utils::companyID());
        return $this;
    }

    public function hotel($field) {
        $this->where($field, Utils::hotelID());
        return $this;
    }

    /**
     * Trả về câu truy vấn hiện tại
     *
     * @return string
     */
    function sql() {
        if (!empty($this->company_key) && DB::isCheckFieldRequired() && !in_array($this->company_key, $this->where_fields)) {
            $this->company($this->company_key);
        }

        // Kiểm tra xem có cần phải thêm hotel id vào sql không
        if (
            !empty($this->hotel_field)
            && DB::isCheckFieldRequired('hotel')
            && Utils::checkRequiredFieldHotel()
            && !in_array($this->hotel_field, $this->where_fields)
        ) {
            $this->hotel($this->hotel_field);
        }

        $this->where_fields = [];
        return $this->ds->__toString();
    }

    /**
     * Thực hiện in ra thông tin của câu truy vấn hiện tại để debug
     *
     * @param  bool $return_data chỉ định xem có hiển thị cả kết quả của truy vấn không
     * @return void
     */
    function dump($return_data = false) {
        $data = [
            'params' => $this->ds->params(),
            'sql' => $this->sql(),
        ];

        if ($return_data) {
            $data['output'] = $this->toArray();
        }
        dd($data);
    }
}
