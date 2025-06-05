<?php 

namespace src\Libs\Database;

use Dibi\Connection as DibiConnection;
use Dibi\DriverException;
use src\Libs\Utils;

class Connection {

    private $conn;

    public function __construct() {
        try {
            $this->conn = new DibiConnection(Utils::parseDatabaseUri(Utils::env('DATABASE_MAIN')));
        } catch (DriverException $e) {
            save_log("error_connect.log", $e->getMessage());
            throw $e;
        }
    }

    public function main() {
        return $this->conn;
    }
}