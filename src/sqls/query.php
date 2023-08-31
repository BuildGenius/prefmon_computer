<?php
namespace Phoomin\PerformanceComputer\sqls;
require_once(dirname(__DIR__, 2) . '/config.php');

use PDO;
use Phoomin\PerformanceComputer\sqls\Connect as Connection;
use Phoomin\PerformanceComputer\configuration\configuration;

class query {
    public function __construct() {
        $this->events = '';
        $this->groupbyStmt = '';
        $this->join = [];
    }

    private static function Connect() {
        $config = new configuration;
        $conn = Connection::Connect($config->getMSSQLConnectionString(production));
        return $conn;
    }

    public static function init($table, $column) {
        $column_setup = [];
        $query = query::getInstance();

        foreach($column as $key => $val) {
            $options = '';
            if (isset($val['options'])) {
                foreach ($val['options'] as $vkey => $vval) {
                    if ($vkey == "primary") {
                        $options .= "PRIMARY KEY\t";
                    }

                    if ($vkey == "references") {
                        $options .= "REFERENCES " . $vval['table'] . "(" . $vval['fieldName'] . ")\t";
                    }

                    if ($vkey == "foreign") {
                        $options .= "FOREIGN KEY\t";
                    }

                    if ($vkey == "identity") {
                        $options .= "IDENTITY(" . implode(',', $vval) . ")\t";
                    }
                }
            }

            $typetxt = $val['type'];

            array_push($column_setup, "[$key]\t$typetxt\t$options");
        }
        $txtColumn = implode(",\r\n", $column_setup);

        $query->preparedStmt = <<<EOT
        CREATE TABLE $table (
        $txtColumn
        )
        EOT;

        $query->events = 'createTable';
        $query->exec();

        return;
    }

    public static function getInstance() {
        return new query;
    }

    public static function setTable($table) {
        $query = query::getInstance();
        $query->table = $table;

        return $query;
    }

    public static function search($selector = [], $table) {
        $query = query::setTable($table);
        $query->select($selector);

        return $query;
    }

    public function select($selector = []) {
        $table = $this->table;
        $selected = !empty($selector)?implode(", ", $selector):'*';

        $this->selectstmt = <<<EOT
        SELECT $selected FROM $table
        EOT;

        $this->events = 'select';

        return $this;
    }

    public function where() {
        $whereArr = func_get_args();
        $whereKey = '';
        $whereArrFormeted = [];
        $this->params = [];

        for ($i = 0;$i < count($whereArr);$i++) {
            if (($i % 2) != 0) {
                array_push($this->params, $whereArr[$i]);
                array_push($whereArrFormeted, $whereKey . '?');
            } else {
                $whereKey = $whereArr[$i] . ' = ';
            }
        }

        $whereTxt = implode(" AND ", $whereArrFormeted);

        $this->wherestmt = <<<EOT
        WHERE $whereTxt
        EOT;

        return $this;
    }

    public static function cast($fieldname, String $type) {
        $query = query::getInstance();

        if (is_array($fieldname)) {
            $query->cast = 'cast(' . implode('.', $fieldname) . ' as ' . $type . ')';
        }

        return $query->cast;
    }

    public static function average(String $fieldname) {
        $query = query::getInstance();
        $query->fieldname = "AVG(" . $fieldname . ")";

        return $query;
    }

    public function as(String $name) {
        if (isset($this->fieldname)) {
            $this->as = $this->fieldname . ' as [' . $name . ']';
        }

        return $this->as;
    }

    public function groupby () {
        $params = implode(', ', func_get_args());
        $this->groupbyStmt = "GROUP BY " . $params;

        return $this;
    }

    public function exec() {
        $conn = query::Connect();

        if ($this->events == 'select') {
            $stmt = $conn->prepare($this->selectstmt 
            . "\r\n"
            . implode("\r\n", $this->join)
            . "\r\n"
            . $this->wherestmt
            . "\r\n"
            . $this->groupbyStmt
        );

            $stmt->execute($this->params);

            $fetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($this->preparedStmt);
            $stmt->execute();

            $fetch = $conn;
        }

        return $fetch;
    }

    public function join($tableObject) {
        if (is_object($tableObject)) {
            array_push($this->join, 'JOIN ' . $tableObject->table);
        }

        return $this;
    }

    public function on() {
        $params = func_get_args();
        $lastNumber = count($this->join) - 1;
        $joinarr = [];

        for ($i = 0;$i < count($params);$i++) {
            if (($i % 2) == 0) {
                array_push($joinarr, $params[$i][0]->table.'.'.$params[$i][1]);
            } else {
                $joinarr[($i - 1)] .= ' = ' . $params[$i][0]->table.'.'.$params[$i][1];
            }
            
        }
        
        $this->join[$lastNumber] .= ' ON ' . implode(' AND ', $joinarr);

        return $this;
    }

    public function bindParams($params) {
        $this->paramsBind = [];
        $this->paramsKey = [];
        $this->paramsValue = [];

        for ($i = 0;$i < count($params);$i++) {
            if (($i % 2) !== 0) {
                array_push($this->paramsBind, '?');
                array_push($this->paramsValue, $params[$i]);
            } else {
                array_push($this->paramsKey, $params[$i]);
            }
        }

        return $this;
    }

    public function add() {
        $conn = query::Connect();
        $table = $this->table;

        $params = func_get_args();
        $this->bindParams($params[0]);
        $key = implode(",\r\n", $this->paramsKey);
        $value = implode(",\r\n", $this->paramsBind);

        $this->preparedStmt = <<<EOT
        INSERT INTO $table (
            $key
        ) VALUES (
            $value
        )
        EOT;

        $stmt = $conn->prepare($this->preparedStmt);
        $stmt->execute($this->paramsValue);

        return [
            'inserted' => $conn->lastInsertId() > 0 ? true:false,
            'insertedId' => $conn->lastInsertId()
        ];
    }

    public static function update($table = '', $conn = '') {

    }

    public static function delete($table = '') {

    }
}