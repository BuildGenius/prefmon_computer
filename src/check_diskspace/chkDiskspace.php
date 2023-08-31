<?php 

namespace Phoomin\PerformanceComputer\check_diskspace;

use Phoomin\PerformanceComputer\configuration\configuration;

class chkDiskspace {
    function __construct() {
        $config = new configuration;
        $this->unit_pow = [
            "KB" => 1,
            "MB" => 2,
            "GB" => 3,
            "TB" => 4
        ];

        $this->set_criteria_disk($config->phpConfig['criteria_disk_space_percent']);

        return $this;
    }
    function get_disk_health($drivename) {
        $this->drivename = $drivename;
        $freespace = $this->get_freespace($drivename);
        $totalspace = $this->get_totalspace($drivename);

        $freespace = round($this->convert_unit($freespace, 'GB'),2);
        $totalspace = round($this->convert_unit($totalspace, 'GB'), 2);
        $freespace_percent = round($this->transToPercentage($freespace, $totalspace), 2);

        return $this->output_formatted($this->get_freespace($drivename), $this->get_totalspace($drivename), $freespace_percent);
    }

    function get_totalspace($drivename = null) {
        return disk_free_space($drivename);
    }

    function get_freespace($drivename = null) {
        return disk_total_space($drivename);
    }

    function convert_unit($space, $unit) {
        $space = round($space / pow(1024, $this->unit_pow[$unit]), 0);
        return $space;
    }

    function auto_convert_unit($space) {
        $report = "";
        foreach ($this->unit_pow as $key => $val) {
            $unit = $this->convert_unit($space, $key);
            if (strlen($unit) <= 3) {
                $report .= $unit . " " . $key;
                break;
            }
        }

        return $report;
    }

    function set_criteria_disk($criteria) {
        $this->criteria = $criteria;
        $this->criteria_warnning = $criteria + 10;
        return $this;
    }

    function get_criteria_disk() {
        return $this->criteria;
    }

    function get_criteria_warnning() {
        return $this->criteria_warnning;
    }

    function transToPercentage($free, $total) {
        return ($total/$free) * 100;
    }

    function danger_alert() {

    }

    function warnning_alert() {

    }

    function output_formatted($free, $total, $percent) {
        $report = [];
        $emergency_alert = false;
        $status = "not critical";

        if ($percent <= $this->get_criteria_disk()) {
            $emergency_alert = true;
        }

        if ($percent <= $this->get_criteria_warnning()) {
            $status = "critical";
        }

        $report_txt = <<<EOT
DRIVE {$this->drivename} free space: {$percent}%
free: {$this->auto_convert_unit($total)} 
total: {$this->auto_convert_unit($free)}
\r\n\n
EOT;
        $report['status'] = $status;
        $report['emergency'] = $emergency_alert;
        $report["message"] = $report_txt;
        return $report;
    }
}