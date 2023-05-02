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
require_once "lib/v_devices.php";
require_once "lib/gdms.php";

if(!if_group('superadmin')) {
	echo "permission denied";
	require "footer.php";
	die();	
}

if($_POST) {
    // check if device exists in v_devices, insert if needed
    $sql = "SELECT device_uuid, domain_uuid FROM v_devices WHERE device_mac_address = :device_mac_address";
    $parameters['device_mac_address'] = $_POST['mac'];
    $database = new database;
    $device = $database->select($sql, $parameters, 'row');
    unset($parameters);

    if(!$device) {
        $device['device_uuid'] = uuid();
        $device['domain_uuid'] = $domain_uuid;

        $array['devices'][] = array(
            'device_uuid' => $device['device_uuid'],
            'domain_uuid' => $domain_uuid,
            'device_mac_address' => $_POST['mac'],
            'device_vendor' => 'grandstream',
            'insert_user' => $_SESSION['user_uuid'],
        );
        $database->save($array);
        message::add("added device ".$_POST['mac']);
    } else {
        message::add("already in FusionPBX devices");
    }

    // insert device into grandstream_devices
    $sql = "INSERT INTO grandstream_devices (device_uuid, domain_uuid, serial) VALUES (:device_uuid, domain_uuid, :serial)";
    $parameters = $device; // gives us device_uuid and domain_uuid
    $parameters['serial'] = $_POST['serial'];
    $database->execute($sql, $parameters);
    unset($parameters);
    
    $orgsite = explode("-", $_POST['gdms_site']);
    if(sizeof($orgsite) != 2) {
        echo "invalid org/site";
        die();
    }

    $orgId = intval($orgsite[0]);
    $siteId = intval($orgsite[1]);

    // try to add device to gdms.cloud
    $mac = format_mac($_POST['mac'], ':', 'upper');
    error_log("formatted mac to: ".$mac);
    $gdms_result = GDMSAddDevice($orgId, $siteId, $mac, $_POST['serial']);
}

echo "<form method='post'>\n";

echo "<div class='action_bar' id='action_bar'>\n";
echo "	<div class='heading'><b>Add Grandstream Device</b></div>\n";
echo "	<div class='actions'>\n";
echo button::create(['type'=>'button','label'=>"back",'icon'=>$_SESSION['theme']['button_icon_back'],'id'=>'btn_back','link'=>'index.php']);
echo button::create(['type'=>'submit','label'=>"save", 'icon'=>$_SESSION['theme']['button_icon_save'],'id'=>'btn_save']);
echo "	</div>\n";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
?>
    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Site<br /><small>for GDMS</small></td>
        <td width="70%" class="vtable" align="left">
            <select name="gdms_site"><?php
            $orgs = GDMSOrganizationList();
            foreach($orgs->data->result as $org) {
                echo '<optgroup label="'.$org->organization.'">';
                foreach(GDMSSiteList($org->id)->data->result as $site) {
                    $selected = "";
                    if($org->id == 1585 && $site->id == 21758) {
                        $selected = " selected";
                    }
                    echo '<option value="'.$org->id."-".$site->id.'"'.$selected.'>'.$site->siteName.'</option>';
                }
                echo '</optgroup>';
            }
            echo '</ul>';
            ?></select>
        </td>
    </tr>

    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">MAC</td>
        <td width="70%" class="vtable" align="left"><input class="formfld" type="text" name="mac" /></td>
    </tr>

    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Serial</td>
        <td width="70%" class="vtable" align="left"><input class="formfld" type="text" name="serial" /></td>
    </tr>

    <?php if($gdms_result) { ?>
        <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">GDMS result</td>
        <td width="70%" class="vtable" align="left"><pre><?php
            print_r($gdms_result);
            ?></pre></td>
    </tr>
    <?php } ?>

    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Debug</td>
        <td width="70%" class="vtable" align="left"><pre><?php
            echo time() - $_SESSION['grandstream']['gdms_access_token_issued']['number'];
            echo "\n";
            echo $_SESSION['grandstream']['gdms_access_token']['text'];
            ?></pre></td>
    </tr>

    <tr><td></td><td><input type="submit" value="add" class="btn" /></td></tr>
</table>

</form>

<?php
require "footer.php";
