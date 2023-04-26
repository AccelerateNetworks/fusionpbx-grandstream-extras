<?php
require_once "root.php";
require_once "resources/require.php";


$sql = "SELECT device_uuid, domain_uuid FROM grandstream_devices WHERE token = :token";
$parameters['token'] = $_GET['token'];
$database = new database;
$device = $database->select($sql, $parameters, 'row');
unset($parameters);

if(!$device) {
    require __DIR__."/xmlapps/disabled.php";
    die();
}

$device_uuid = $device['device_uuid'];

$app = strtolower($_GET['app']);
$file = __DIR__."/xmlapps/".$app.".php";
if(!file_exists($file)) {
    echo "unknown app";
    die();
}

require $file;

app_render();
