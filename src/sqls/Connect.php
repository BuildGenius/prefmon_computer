<?php

namespace Phoomin\PerformanceComputer\sqls;

use Exception;
use PDO;

class Connect {
    public static function Connect($connection_string) {
        try {
            $conn = new PDO($connection_string[0]
            , $connection_string[1]
            , $connection_string[2]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (Exception $ex) {
            die(print_r($ex->getMessage()));
        }
    }
}