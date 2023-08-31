<?php

namespace App\Models;

use App\Models\Basemodel;
use Phoomin\PerformanceComputer\sqls\query;

class serverRegisteration extends Basemodel {
    function __construct() {
        parent::__construct();
        $this->table = 'serverRegisteration';
        $this->column = [
            'id' => [
                'type' => 'int',
                'options' => [
                    'primary' => true,
                    'identity' => [1, 1]
                ]
            ],
            'serverName' => [
                'type' => 'nvarchar(25)'
            ],
            'status' => [
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