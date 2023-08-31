<?php

namespace App\Http\Controller;

use Phoomin\PerformanceComputer\configuration\configuration;

class Basecontroller {
    function __construct() {
        $this->conf = new configuration();

        return $this;
    }
}