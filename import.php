<?php
/*
	GNU Public License
	Version: GPL 3
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
require_once "resources/header.php";
require_once "resources/paging.php";

if(!if_group('superadmin')) {
	echo "permission denied";
	require "footer.php";
	die();	
}

if($_POST['action'] == "import") {
	$sql = "SELECT * FROM v_devices WHERE domain_uuid = :domain_uuid AND device_uuid = :device_uuid";
	$parameters['domain_uuid'] = $domain_uuid;
	$parameters['device_uuid'] = $_POST['device_uuid'];
	$database = new database;
	$device = $database->select($sql, $parameters, 'row');
	unset($parameters);	

	if(!$device) {
		echo "cannot find device";
		require "footer.php";
		die();
	}

	$token = generate_password(20, 3);

	$sql = "INSERT INTO grandstream_devices (device_uuid, domain_uuid, token, model, firmware_version) VALUES (:device_uuid, :domain_uuid, :token, :model, :firmware_version)";
	$parameters['domain_uuid'] = $domain_uuid;
	$parameters['device_uuid'] = $device['device_uuid'];
	$parameters['token'] = $token;
	$parameters['model'] = null;
	$parameters['firmware_version'] = null;

	if (preg_match('/Grandstream Model HW (?P<hw>\w+) SW (?P<sw>[\d+\.]+) DevId (?P<mac>\w\w\w\w\w\w\w\w\w\w\w\w)/', $device['device_provisioned_agent'], $ua)) {
		$parameters['model'] = $ua['hw'];
		$parameters['firmware_version'] = $ua['sw'];
	}

	$database->execute($sql, $parameters);
	unset($parameters);
}

echo "<div class='action_bar' id='action_bar'>\n";
echo "	<div class='heading'><b>Grandstream Device Management</b><br />import devices from FusionPBX provisioning</div>\n";
echo "	<div class='actions'>\n";
echo button::create(['type'=>'button','label'=>"back",'icon'=>$_SESSION['theme']['button_icon_back'],'id'=>'btn_back','style'=>'margin-right: 15px;','link'=>'index.php']);
echo "	</div>\n";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";
echo "<table class='table'>";

echo "<tr>";
echo "<th>mac</th>";
echo "<th>label</th>";
echo "<th>description</th>";
echo "<th>UA</th>";
echo "<th>actions</th>";
echo "</tr>";

$sql = "SELECT * FROM v_devices WHERE domain_uuid = :domain_uuid AND device_vendor = 'grandstream' AND device_uuid NOT IN (SELECT device_uuid FROM grandstream_devices WHERE domain_uuid = :domain_uuid)";
$parameters['domain_uuid'] = $domain_uuid;
$database = new database;
$devices = $database->select($sql, $parameters, 'all');
unset($parameters);

foreach($devices as $device) {
	echo "<tr>";
	echo "<td>".$device['device_mac_address']."</td>";
	echo "<td>".$device['device_label']."</td>";
	echo "<td>".$device['device_description']."</td>";
	echo "<td>".$device['device_provisioned_agent']."</td>";
	echo "<td><form method='post'>";
	echo "<input type='hidden' name='action' value='import' />";
	echo "<input type='hidden' name='device_uuid' value='".$device['device_uuid']."' />";
	echo button::create(['type'=>'submit','label'=>"Import",'id'=>'btn_import','name'=>'btn_import']);
	echo "</form>";
	echo "</td>";
	echo "</tr>";
}

echo "</table>";

require "footer.php";
