<?php

namespace Phoomin\PerformanceComputer\configuration;

class configuration {
    function __construct() {
        $this->phpConfig = [];

        $file = dirname(__DIR__, 2) . '/configuration.json';
        $json_string = file_get_contents($file);
        $this->phpConfig = json_decode($json_string, true);

        return $this;
    }

    function getLineConnection ($env = 'Production') {
        return $this->phpConfig['lineconnection'][$env];
    }
    function getMSSQLConnectionString($env) {
        $connectionParam = $this->phpConfig['sqlConnection'][$env];
        $connectionStr = "sqlsrv:Server=" . $connectionParam['host'] . ";"
        . "Database=" . $connectionParam['dbname'] . ";";

        return [
            $connectionStr,
            $connectionParam['username'],
            $connectionParam['password']
        ];
    }
}