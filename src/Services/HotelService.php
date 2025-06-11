<?php

namespace src\Services;

use src\Facades\DB;

class HotelService {
    /**
     * GeneralModel::getTablePartitionRoomPrice()
     * Tạo ra tên có thứ tự của table
     * @param mixed $name
     * @param mixed $id
     * @param mixed $time
     * @param int $partition
     * @return row
     */
    public static function getTablePartitionRoomPrice($name, $id = 0, $time = CURRENT_TIME, $partition = CFG_PARTITION_TABLE) {
        $suffixes   = '';

        // Nếu có thời gian thì nối vào name
        if(!empty($time)) {
            $suffixes .= date('ym', $time);
        }

        // Nếu có partition và id thì nối vào name
        if($partition > 0 && $id > 0) {
            $suffixes .= ($id%$partition)+1;
        }
        $table = empty($suffixes) ? $name : $name .'_'. $suffixes;
        return $table;
    }


    /**
     * GeneralModel::existTableRoomPrice()
     * Kiểm tra xem table có tồn tại k, và tạo bảng nếu yêu cầu
     * @param mixed $table
     * @param mixed $create_new
     * @return row
     */
    public static function existTableRoomPrice($table, $create_new = false) {
        $row = DB::pass()->query("SHOW TABLES LIKE '" . $table . "'")->getOne();

        // Nếu table không tồn tại thì tạo mới
        if (empty($row)) {
            if(!$create_new) return false; 
            DB::pass()->execute("CREATE TABLE `". $table ."` (
                `rop_id` int(11) NOT NULL AUTO_INCREMENT,
                `rop_price` double NOT NULL DEFAULT 0,
                `rop_currency_unit` tinyint(1) NOT NULL DEFAULT 1,
                `rop_type` tinyint(1) NOT NULL DEFAULT 0,
                `rop_room_id` int(11) NOT NULL DEFAULT 0,
                `rop_qty` int(11) NOT NULL DEFAULT 1,
                `rop_hotel_id` int(11) NOT NULL DEFAULT 0,
                `rop_day` int(11) NOT NULL DEFAULT 0,
                `rop_created_by` int(11) NOT NULL DEFAULT 0,
                `rop_created_at` int(11) NOT NULL DEFAULT 0,
                UNIQUE KEY (`rop_day`, `rop_room_id`, `rop_type`),
                PRIMARY KEY (`rop_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }
        return true;
    }

     /**
     * Lấy giá phòng cao nhất và số phòng trống nhỏ nhất trong khoảng ngày
     * @param integer $hotel_id
     * @param mixed $daterange (mảng hoặc chuỗi ngày)
     * @param integer $room_id
     * @return array [
     *   'min_qty' => số phòng trống nhỏ nhất,
     *   'total_price' => tổng giá trị của từng đêm trong khoảng
     * ]
     */
    public static function getRoomPriceAndAvailability($hotel_id, $daterange = null, $room_id = null) {
        if(empty($room_id)) return [
            'min_qty' => 0,
            'total_price' => 0
        ];
        $result = self::getTableRoomPrice($hotel_id, $daterange);
        $daterange_int = $result["daterange_int"];
        $tables = $result["tables"];
        if($daterange_int['to'] == $daterange_int['from']) {
            $daterange_int['to'] += 86400;
        }
        $min_qty = PHP_INT_MAX;
        $total_price = 0;
        foreach ($tables as $table) {
            if(!self::existTableRoomPrice($table)) continue;
            // Lấy số phòng trống nhỏ nhất và giá cao nhất trong khoảng ngày
            $from = $daterange_int['from'];
            $to = $daterange_int['to'] - 86400;
            
            $row = DB::query("SELECT *
                                FROM {$table} AS tbl
                                WHERE tbl.rop_hotel_id = $hotel_id 
                                  AND tbl.rop_room_id = $room_id 
                                  AND tbl.rop_day BETWEEN $from AND $to")
                                ->toArray();
            foreach ($row as $v) {
                $min_qty = min($min_qty, $v['rop_qty']);
                $total_price += $v['rop_price'];
            }
        }

        // Lấy số phòng đang giữ tạm
        $current_time = CURRENT_TIME;
        $time_from    = $daterange_int['from'] + 3600;
        $time_to      = $daterange_int['to'] + 3600;
        $room_hold_temp = DB::query("SELECT SUM(roht_qty) AS quantity
                                FROM room_hold_temp 
                                WHERE roht_hotel_id = $hotel_id 
                                  AND roht_room_id = $room_id 
                                  AND roht_checkin < {$time_to} AND roht_checkout > {$time_from} AND roht_day >= {$current_time}")
                                ->getOne();
        $room_hold_temp = $room_hold_temp['quantity'] ?? 0;
        $min_qty -= $room_hold_temp;
        if($min_qty < 0) $min_qty = 0;
        if($min_qty == PHP_INT_MAX) $min_qty = 0;

        return [
            'min_qty' => $min_qty,
            'total_price' => $total_price
        ];
    }

    static function getTableRoomPrice($hotel_id = 0, $daterange = null)
    {
        $daterange_int  = generate_time_from_date_range($daterange, false);
        // Lấy ra tất cả table chứa data của ks
        $tmp = [];
        $partition  = ($hotel_id%CFG_PARTITION_TABLE)+1;
        $num_nights = ($daterange_int['to'] - $daterange_int['from']) / 86400;
        for($i = 0; $i < $num_nights; $i++) {
            $day = $daterange_int['from'] + $i * 86400;
            if($hotel_id < 1) {
                for ($partition=1; $partition <= CFG_PARTITION_TABLE; $partition++) { 
                    $tmp[] = "room_price_". date('ym', $day) .$partition;
                }
            } else {
                $tmp[] = "room_price_". date('ym', $day) .$partition;
            }
        }
        $tmp = array_unique($tmp); // Loại bỏ các table trùng tên

        // Loại bỏ các table k tồn tại
        $tables     = [];
        foreach($tmp as $v) {
            if(self::existTableRoomPrice($v)) $tables[] = $v;
        }
        return compact('tables', 'daterange_int');
    }

    // Lấy giá theo tháng
    public static function getRoomPriceByMonth($hotel_id) {
        // Lấy ra dạng hot_price_m{tháng}
        $month = intval(date('m', CURRENT_TIME));
        if($month < 10) {
            $month = str_replace('0', '', $month);
        }
        $row = DB::query("SELECT hot_price_m{$month} as price FROM hotel WHERE hot_id = {$hotel_id}")->getOne();
        return $row['price'] ?? 0;
    }

}