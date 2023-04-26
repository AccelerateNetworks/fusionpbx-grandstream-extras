<?php

function upsert_device_setting($device_uuid, $setting, $value) {
    global $domain_uuid;
    $database = new database;
    $sql = "SELECT device_setting_uuid FROM v_device_settings WHERE device_uuid = :device_uuid AND domain_uuid = :domain_uuid AND device_setting_subcategory = :setting";
    $parameters['device_uuid'] = $_POST['device_uuid'];
    $parameters['domain_uuid'] = $domain_uuid;
    $parameters['setting'] = $setting;
    $setting_uuid = $database->select($sql, $parameters, 'column');
    unset($parameters);

    if($setting_uuid) {
        $sql = "UPDATE v_device_settings SET device_setting_value = :url, device_setting_enabled = true WHERE device_setting_uuid = :setting_uuid";
        $parameters['url'] = $value;
        $parameters['setting_uuid'] = $setting_uuid;
    } else {
        $sql = "INSERT INTO v_device_settings (device_setting_uuid, device_uuid, domain_uuid, device_setting_subcategory, device_setting_value, device_setting_enabled) VALUES (:uuid, :device_uuid, :domain_uuid, :setting, :url, true)";
        $parameters['uuid'] = uuid();
        $parameters['device_uuid'] = $_POST['device_uuid'];
        $parameters['domain_uuid'] = $domain_uuid;
        $parameters['setting'] = $setting;
        $parameters['url'] = $value;
    }
    $database->execute($sql, $parameters);
    unset($parameters);
}

function ensure_device_xmlapp($device_uuid, $token, $app, $appname) {
    $xmlAppUrl = "https://".$_SERVER['HTTP_HOST']."/app/grandstream_extras/xmlapp.php?token=".$token."&app=".$app;
    upsert_device_setting($device_uuid, 'grandstream_xmlapp_url', $xmlAppUrl);
    upsert_device_setting($device_uuid, 'grandstream_xmlapp_name', $appname);    
}
