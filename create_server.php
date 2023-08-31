<?php
require_once(dirname(__FILE__) . '/config.php');
use App\Models\serverRegisteration;

$regis = new serverRegisteration;
date_default_timezone_set('Asia/Bangkok');
$today = date('Y-m-d H:i:s');

$server = readline("Enter your server name: ");
if (!$regis->checkDuplicated('serverName', $server)) {
    $server_id = $regis->add('serverName', $server, 'status', 1, 'createat', $today);
    if ($server_id['inserted']) {
        $id = $server_id['insertedId'];
        echo "Ok! setup this server id is $id in the name of " . $server;
    }

    $json = file_get_contents('configuration.json');
    $phparr = json_decode($json, true);

    $phparr['computer_name'] = $server;

    file_put_contents('configuration.json', json_encode($phparr, JSON_PRETTY_PRINT));
    exec('php main.php');
} else {
    echo "server name is duplicated. please try again.";
}
