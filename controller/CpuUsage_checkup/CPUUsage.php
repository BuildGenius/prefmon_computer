<?php

namespace App\Http\Controller\CpuUsage_checkup;

use App\Http\Controller\Basecontroller;
use App\Models\CpuUsageLogs;
use App\Models\serverRegisteration;
use Phoomin\PerformanceComputer\configuration\configuration;
use Phoomin\PerformanceComputer\sqls\query;

class CPUUsage extends Basecontroller {
    function __construct() {
        parent::__construct();
    }

    function setCPUUsage() {
        $config = new configuration;
        $today = date('Y-m-d H:i:s');

        // get cpu name
        exec('wmic cpu get Name', $cpuName);
        // get cpu description
        exec('wmic cpu get Description', $description);
        // get cpu manufacturer
        exec('wmic cpu get Manufacturer', $manufacturer);
        // get cpu usage loadpercentage
        exec('wmic cpu get loadpercentage', $loadpercentage);

        $cpuUsagedLog = new CpuUsageLogs;
        $Regissrv = new serverRegisteration;

        $res = $Regissrv->find('serverName', $config->phpConfig['computer_name']);

        $cpuUsagedLog->add(
            'serverId', $res[0]['id'],
            'manufacturer', $manufacturer[1],
            'cpu_name', $cpuName[1],
            'Description', $description[1],
            'percentage', $loadpercentage[1],
            'createat', $today
        );

        return [
            'serverId' => $res[0]['id'],
            'manufacturer' => $manufacturer[1],
            'cpu_name' => $cpuName[1],
            'Description' => $description[1],
            'percentage' => $loadpercentage[1],
            'createat' => $today
        ];
    }

    function getAverageCpu ($date) {
        $config = new configuration;
        $cpuUsagedLog = new CpuUsageLogs;
        $Regissrv = new serverRegisteration;

        $res = $Regissrv->find('serverName', $config->phpConfig['computer_name']);
        
        $result = query::setTable($cpuUsagedLog->table)
        ->select(['serverName', 'cpu_name', '[description]', query::average('[percentage]')->as('avg_percentage')])
        ->join($Regissrv)->on([$Regissrv, 'id'], [$cpuUsagedLog, 'serverId'])
        ->where('serverId', $res[0]['id'], query::cast([$cpuUsagedLog->table, 'createat'], 'date'), $date)
        ->groupby('serverName', 'cpu_name', '[Description]')
        ->exec();

        return $result;
    }
}