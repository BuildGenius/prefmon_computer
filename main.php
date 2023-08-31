<?php

require_once(dirname(__FILE__) . '/vendor/autoload.php');

use App\Http\Controller\CpuUsage_checkup\CPUUsage;
use App\Http\Controller\Diskspace_checkup\diskspace;
use App\Http\Controller\Request\Request;
use GO\Scheduler;
use Phoomin\PerformanceComputer\configuration\configuration;

$scheduler = new Scheduler;

date_default_timezone_set('Asia/Bangkok');
$today = date('Y-m-d_H_i_s');

$send_message = function($param) {
  $config = new configuration;

  $req = Request::init();
  $req->setUrl($config->getLineConnection('dev'))
  ->setRequestMethod('get')
  ->setParams($param)->send();
};

// HDD space
$scheduler->call(function () {
    $hdd = new diskspace;
    $drive = $hdd->check_disk_drive();

    $message = "";
    
    for ($i = 0;$i < count($drive);$i++) {
       $message .= $hdd->getDiskspace($drive[$i])['message'];
    }
    
    return $message;
})->output('./schedule/log/log-checkup-HDD-' . $today . '.txt')
->daily(9);

// Get CPU Usaged Average Schedule
$scheduler->call(function () {
  $cpu = new CPUUsage;
  $yesterday = date_modify(new DateTime, '-1 days');
  $YesterdaytoString = date_format($yesterday, 'Y-m-d H:i:s');
  
  $result = $cpu->getAverageCpu($YesterdaytoString);
  
  $message = "\r\n" . 'CPU check up : ' . $result[0]['serverName'] . "'s server"
  . "\r\n"
  . "cpu gen: " . $result[0]['cpu_name'] . '(' . $result[0]['description'] . ')'
  . "\r\n"
  . "cpu work load average percentage(%) : " . $result[0]['avg_percentage'] . '%';

  return $message;
})
->output('./schedule/log/log-average-cpu-' . $today . '.txt')
->daily(9);

// Set CPU Usaged Schedule
$scheduler->call(function () {
    $cpu = new CPUUsage;
    return $cpu->setCPUUsage();
})->output('./schedule/log/log-checkup-cpu-' . $today . '.txt')
->everyMinute(5);

$scheduler->work();

