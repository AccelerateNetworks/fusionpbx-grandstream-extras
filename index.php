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

echo "<div class='action_bar' id='action_bar'>\n";
echo "	<div class='heading'><b>Grandstream Devices</b></div>\n";
echo "	<div class='actions'>\n";
echo button::create(['type'=>'button','label'=>"New",'icon'=>$_SESSION['theme']['button_icon_add'],'id'=>'btn_add','name'=>'btn_add','link'=>'device_add.php']);
echo button::create(['type'=>'button','label'=>"Import",'icon'=>'arrow-down','id'=>'btn_add','name'=>'btn_add','link'=>'import.php']);
echo "	</div>\n";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";
echo "<table class='table'>";

echo "<tr>";
echo "<th>mac</th>";
echo "<th>model</th>";
echo "<th>label</th>";
echo "<th>description</th>";
echo "<th>UA</th>";
echo "<th>actions</th>";
echo "</tr>";

$sql = "SELECT * FROM grandstream_devices, v_devices WHERE grandstream_devices.domain_uuid = :domain_uuid AND v_devices.device_uuid = grandstream_devices.device_uuid";
$parameters['domain_uuid'] = $domain_uuid;
$database = new database;
$devices = $database->select($sql, $parameters, 'all');
unset($parameters);

foreach($devices as $device) {
	echo "<tr>";
	echo "<td>".$device['device_mac_address']."</td>";
	echo "<td>".$device['model']."</td>";
	echo "<td>".$device['device_label']."</td>";
	echo "<td>".$device['device_description']."</td>";
	echo "<td>".$device['device_provisioned_agent']."</td>";

	echo "<td>";
	echo button::create(['type'=>'button','icon'=>'pencil-alt','link'=>'device_edit.php?device_uuid='.$device['device_uuid']]);
	echo button::create(['type'=>'button','icon'=>'fax','link'=>'/app/devices/device_edit.php?id='.$device['device_uuid']]);
	echo "</td>";
	// echo "<td><pre>".print_r($device, true)."</pre></td>";

	echo "</tr>";
}

echo "</table>";


require "footer.php";
