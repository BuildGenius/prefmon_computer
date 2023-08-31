<?php

namespace App\Models;

use App\Models\Basemodel;
use Phoomin\PerformanceComputer\sqls\query;

class CpuUsageLogs extends Basemodel {
    function __construct() {
        parent::__construct();
        $this->table = 'CpuUsageLogs';
        $this->column = [
            'id' => [
                'type' => 'int',
                'options' => [
                    'primary' => true,
                    'identity' => [1, 1]
                ]
            ],
            'serverId' => [
                'type' => 'int',
                'options' => [
                    'foreign' => true,
                    'references' => [
                        'table' => 'serverRegisteration',
                        'fieldName' => 'id'
                    ]
                ]
            ],
            'manufacturer' => [
                'type' => 'nvarchar(150)'
            ],
            'cpu_name' => [
                'type' => 'nvarchar(150)'
            ],
            'Description' => [
                'type' => 'nvarchar(MAX)'
            ],
            'percentage' => [
                'type' => 'int'
            ],
            'createat' => [
                'type' => 'datetime'
            ]
        ];

        if (!$this->isDuplicatedTable()) {
            query::init($this->table, $this->column);
        }
    }
}