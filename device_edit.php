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
require_once("lib/v_devices.php");

if(!if_group('superadmin')) {
	echo "permission denied";
	require "footer.php";
	die();	
}

if($_POST['device_uuid']) { // edit an existing device
    $sql = "UPDATE grandstream_devices SET xmlapp = :xmlapp, model = :model, firmware_version = :firmware_version WHERE device_uuid = :device_uuid AND domain_uuid = :domain_uuid RETURNING token";
    $parameters['xmlapp'] = $_POST['xmlapp'];
    $parameters['model'] = $_POST['model'];
    $parameters['firmware_version'] = $_POST['firmware_version'];
    $parameters['device_uuid'] = $_POST['device_uuid'];
    $parameters['domain_uuid'] = $domain_uuid;
    $database = new database;
    $token = $database->select($sql, $parameters, 'column');
    unset($parameters);

    if(!$token) {
        echo "insert error";
        include "footer.php";
        die();
    }

    $app = $_POST['xmlapp'];

    if($app == "disabled") {
        // TODO: ensure device setting options are not set
    } else {
        $file = __DIR__."/xmlapps/".$app.".php";
        if(file_exists($file)) {
            require $file;
            upsert_device_setting($device_uuid, 'grandstream_xmlapp_url',  "https://".$_SERVER['HTTP_HOST']."/app/grandstream_extras/xmlapp.php?token=".$token."&app=".$app);
            upsert_device_setting($device_uuid, 'grandstream_xmlapp_name', app_name());
        }
    }

}

echo "<form method='post'>\n";

$sql = "SELECT * FROM grandstream_devices WHERE device_uuid = :device_uuid AND domain_uuid = :domain_uuid";
$parameters['device_uuid'] = $_GET['device_uuid'];
$parameters['domain_uuid'] = $domain_uuid;
$database = new database;
$device = $database->select($sql, $parameters, 'row');
unset($parameters);

if($device) {
    echo "<input type='hidden' name='device_uuid' value='".$device['device_uuid']."' />";
}

echo "<div class='action_bar' id='action_bar'>\n";
$title = $device ? "Edit Device" : "New Device";
echo "	<div class='heading'><b>".$title."</b></div>\n";
echo "	<div class='actions'>\n";
echo button::create(['type'=>'button','label'=>"new",'icon'=>$_SESSION['theme']['button_icon_add'],'id'=>'btn_add','name'=>'btn_add','link'=>'device_edit.php']);
echo button::create(['type'=>'button','label'=>"back",'icon'=>$_SESSION['theme']['button_icon_back'],'id'=>'btn_back','link'=>'index.php']);
if($device) {
    echo button::create(['type'=>'button','label'=>'device','icon'=>'fax','link'=>'/app/devices/device_edit.php?id='.$device['device_uuid']]);
}
echo button::create(['type'=>'submit','label'=>"save", 'icon'=>$_SESSION['theme']['button_icon_save'],'id'=>'btn_save']);
echo "	</div>\n";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
?>
    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">XML App</td>
        <td width="70%" class="vtable" align="left">
            <select name="xmlapp">
                <?php 
                foreach(scandir(__DIR__."/xmlapps/") as $filename) {
                    if(substr($filename, -4) != ".php") {
                        continue;
                    }
                    $app = substr($filename, 0, -4);
                    
                    $selected = "";
                    if($device['xmlapp'] == $app || (!$device['xmlapp'] && $app == "disabled")) {
                        $selected = "selected";
                    }
                    echo "<option value='".$app."' ".$selected.">".$app."</option>";
                }
                ?>
            </select>
        </td>
    </tr>

    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Model<br /><small>currently unused</small></td>
        <td width="70%" class="vtable" align="left"><input type="text" name="model" value="<?php echo $device['model']; ?>" /></td>
    </td>

    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Desired Firmware Version<br /><small>currently unused</small></td>
        <td width="70%" class="vtable" align="left"><input type="text" name="firmware_version" value="<?php echo $device['firmware_version']; ?>" /></td>
    </td>
</table>

</form>

<?php
require "footer.php";
