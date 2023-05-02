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
echo "	<div class='heading'><b>GDMS test page</b>\n</div>";
echo "	<div class='actions'>\n";
// echo button::create(['type'=>'button','label'=>"back",'icon'=>$_SESSION['theme']['button_icon_back'],'id'=>'btn_back','style'=>'margin-right: 15px;','link'=>'index.php']);
echo "	</div>\n";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";

require_once "lib/gdms.php";

if($_SESSION['grandstream']['gdms_api_id'] && $_SESSION['grandstream']['gdms_api_secret']) {
	if($_POST['gdms_username'] && $_POST['gdms_password']) {
		echo "<pre>".print_r(GDMSLogin($_POST['gdms_username'], $_POST['gdms_password']), true)."</pre>";
	}

	if(!$_SESSION['grandstream']['gdms_refresh_token']) {
		echo "<form method='post'>";
		echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
		echo "<tr>";
			echo "<td width='30%' class='vncellreq' valign='top' align='left'>Username: </td>";
			echo "<td width='70%' class='vtable' align='left'><input type='text' name='gdms_username' /></td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td width='30%' class='vncellreq' valign='top' align='left'>Password: </td>";
			echo "<td width='70%' class='vtable' align='left'><input type='password' name='gdms_password' /></td></tr>";
		echo "<tr><td></td><td><input type='submit' value='Authenticate to GDMS' /></td></tr>";
		echo "</table>";
		echo "</form>";
		require "footer.php";
		die();
	}
	$orgs = GDMSOrganizationList();
	if(!$orgs->data->result) {
		echo "<pre>".print_r($orgs, true)."</pre>";
	}
	echo '<ul>';
	foreach($orgs->data->result as $org) {
		echo "<li>".$org->organization;
		echo '<ul>';
		foreach(GDMSSiteList($org->id)->data->result as $site) {
			echo '<li><pre>'.json_encode($site).'</pre></li>';
		}
		echo '</ul>';
		echo '</li>';
	}
	echo '</ul>';
}
require "footer.php";
