<?php

namespace App\Models;

use Phoomin\PerformanceComputer\sqls\query;

class Basemodel {
    function __construct() {
        $this->table = '';
        $this->column = [];
    }

    function isDuplicatedTable() {
        $isDup = false;
        $table = 'INFORMATION_SCHEMA.TABLES';
        $res = query::setTable($table)->select(['1'])
        ->where('TABLE_TYPE', 'BASE TABLE', 'TABLE_NAME', $this->table)
        ->exec();

        return empty($res)?$isDup:true;
    }

    function checkDuplicated() {
        $isDup = false;
        $query = query::setTable($this->table)->select(['1']);
        $query = call_user_func_array([$query, 'where'], func_get_args());

        $res = $query->exec();
        
        return empty($res)? $isDup:true;
    }

    function add() {
        $params = func_get_args();

        $query = query::getInstance();
        $inserted = $query->setTable($this->table)
        ->add($params);

        return $inserted;
    }

    function find() {
        $params = func_get_args();

        $query = query::setTable($this->table);
        $query->select();
        call_user_func_array([$query, 'where'], $params);
        $result = $query->exec();

        return $result;
    }
}