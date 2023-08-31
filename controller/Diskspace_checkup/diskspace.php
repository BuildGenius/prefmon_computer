<?php
namespace App\Http\Controller\Diskspace_checkup;

use App\Http\Controller\Basecontroller;
use Phoomin\PerformanceComputer\check_diskspace\chkDiskspace;

class diskspace extends Basecontroller {
    function __construct() {
        parent::__construct();
        $this->chk_diskspace = new chkDiskspace();

        return $this;
    }

    function check_disk_drive () {
        exec('wmic logicaldisk get deviceid', $output);

        for ($i = 1;$i < count($output);$i++) {
            if (!empty($output[$i])) {
                $this->diskdrive[] = $output[$i];
            }
        }

        return $this->diskdrive;
    }

    function getDiskspace($diskname) {
        $result = $this->chk_diskspace->get_disk_health($diskname);
        return $result;
    }
}