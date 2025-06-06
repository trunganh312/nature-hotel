<?php 

namespace src\Libs\Database;

use Exception;
use src\Facades\Connection;
use src\Libs\Utils;

/**
 * Database
 * 
 * Thực hiện kết nối tới databse và cung cấp các hàm hỗ trợ tối ưu thời gian viết truy vấn và sử dụng
 * 
 * Version: 1.0
 * User: TamPV
 * Email: tampv.work@gmail.com
 * Date: 2024-03-30 11:28:16
 */
class Database {

    protected $result;
    protected $sql;

    /**
     * Thực thi câu truy vấn và lấy kết quả
     *
     * @param  string $sql Câu truy vấn
     * @return this
     */    
    function query(string $sql) {
        $this->sql      = $this->beautifySQL($sql);
        // Lưu kết quả để sử dụng cho các return function [toArray, getOne, pluck]
        $this->result   = Connection::main()->query($this->sql);
        return $this;
    }
    
    /**
     * Thực thi câu truy vấn và trả về số bản ghi bị thay đổi
     *
     * @param  string $sql
     * @return int
     */
    function execute(string $sql) {
        $this->sql    =   $this->beautifySQL($sql);
        Connection::main()->query($this->sql);
        return (int) Connection::main()->getAffectedRows();
    }

    /**
     * Thực thi câu truy vấn và trả về id bản ghi được insert gần nhất
     *
     * @param  string $sql
     * @return int
     */
    function executeReturn($sql) {
        $this->sql    =   $this->beautifySQL($sql);
        Connection::main()->query($this->sql);

        return (int) Connection::main()->getInsertId();
    }
    /**
     * Tắt kiểm tra mysql có thiếu không truyền company_id không
     *
     * @return void
     */
    function pass($company=null, $hotel=null) {

        // Nếu cả 2 null thì false
        if($company === null && $hotel === null) {
            $company = $hotel = false;
        } elseif ($company !== null && $hotel === null) {
            // Nếu arg1 khác null và arg2 === null
            $hotel = true;
        }
        return $this;
    }
    
    public function beautifySQL($sql) {
        $sql = preg_replace("/\n/", '', $sql);
        $sql = preg_replace("/\s+/", ' ', $sql);
        $sql = trim($sql);
        return $sql;
    }
    
    /**
     * Dựa vào result của hàm query để trả về tất cả các bản ghi dưới dạng danh sách
     *
     * @return array
     */
    function toArray() {
        if (!$this->result) return [];

        $rows = $this->result->fetchAll();
        $rows = array_map(function ($row) {
            return $row->toArray();
        }, $rows);

        return $rows;
    }
    
    /**
     * Dựa vào result của hàm query để trả về tất cả các bản ghi dưới dạng key và value chỉ định
     *
     * @param  string $k
     * @param  string $v
     * @return array
     */
    function pluck(string $k, string $v=null) {
        if (!$this->result) return [];

        $rows = $this->result->fetchPairs($k, (empty($v) ? $k : $v));

        if (empty($v)) {
            return array_values($rows);
        }
        return $rows;
    }
    
    /**
     * Dựa vào result của hàm query để trả về bản ghi đầu tiên dưới dạng danh sách
     *
     * @return void
     */
    function getOne() {
        if (!$this->result) return null;
        $row = $this->result->fetch();
        return $row ? $row->toArray() : null;
    }
    
    /**
     * Thực hiện cập nhật data vào table theo điều kiện chỉ định và trả về số bản ghi thay đổi
     *
     * @param  string $table
     * @param  array $data
     * @param  array $conds
     * @return int
     */
    public function update(string $table, array $data, array $conds = [1]) {
        Connection::main()->query("UPDATE `$table` SET %a", $data, 'WHERE %and', $conds);
        return (int) Connection::main()->getAffectedRows();
    }

    /**
     * Thực hiện xoá bản ghi trong table theo điều kiện chỉ định và trả về số bản ghi bị xoá
     *
     * @param  string $table
     * @param  array $conds
     * @return int
     */
    public function delete(string $table, array $conds = [1]) {
        Connection::main()->query("DELETE FROM `$table` WHERE %and", $conds);
        return (int) Connection::main()->getAffectedRows();
    }

    /**
     * Thực hiện thêm mới data vào table và trả về id bản ghi
     *
     * @param  string $table
     * @param  array $data
     * @param  array $conds
     * @return int
     */
    public function insert(string $table, array $data) {
        Connection::main()->query("INSERT INTO `$table` %v", $data);
        return (int) Connection::main()->getInsertId();
    }

    /**
     * DBConnect::count()
     * Count record: SELECT COUNT(id) AS total...
     * @param mixed $query
     * @return number
     */
    function count($query)
    {
        $this->query($query);

        if (!$this->result) return 0;
        $row = $this->result->fetch();
        return $row ? (int) array_get($row->toArray(), 'total', 0) : 0;
    }
    
    /**
     * Thực hiện in ra thông tin của câu truy vấn hiện tại để debug
     *
     * @param  bool $return_data chỉ định xem có hiển thị cả kết quả của truy vấn không
     * @return void
     */
    function dump($return_data = false) {
        $data = [
            'sql'=> $this->sql,
        ];

        if ($return_data) {
            $data['output'] = $this->toArray();
        }
        dd($data);
    }
}