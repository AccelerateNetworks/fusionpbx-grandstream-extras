<?php
require_once "root.php";
require_once "resources/require.php";

function send_disabled() {
    require __DIR__."/xmlapps/disabled.php";
    app_render();
}

$sql = "SELECT device_uuid, domain_uuid FROM grandstream_devices WHERE token = :token";
$parameters['token'] = $_GET['token'];
$database = new database;
$device = $database->select($sql, $parameters, 'row');
unset($parameters);

if(!$device) {
    send_disabled();
    die();
}

$device_uuid = $device['device_uuid'];
require __DIR__."/xmlapps/index.php";

if($_GET['app']) {
    $app = $_GET['app'];
    if(!array_key_exists($app, $xmlapps)) {
        $app = "disabled";
    }
    require __DIR__."/xmlapps/".$app.".php";
} else {
    output_launcher_app();
}
