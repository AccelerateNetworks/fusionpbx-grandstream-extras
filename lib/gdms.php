<?php
// utilities to interact with God Damn Management System's very well adjusted and normal API

function GDMSLogin(string $username, string $password) {
    $ch = curl_init("https://www.gdms.cloud/oapi/oauth/token");
    curl_setopt($ch, CURLOPT_POST, 1);   
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'username' => $username,
            'password' => hash('sha256', md5($password)),
            'grant_type' => "password",
            'client_id' => $_SESSION['grandstream']['gdms_api_id']['text'],
            'client_secret' => $_SESSION['grandstream']['gdms_api_secret']['text'],
        )));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $resp = json_decode(curl_exec($ch));
    
    curl_close($ch);

    $_SESSION['grandstream']['gdms_refresh_token']['text'] = $resp->refresh_token; // TODO: Save this to the database
    $_SESSION['grandstream']['gdms_access_token']['text'] = $resp->access_token;

    return $resp;
}

function GDMSAuthToken() {
    $expires = time() - $_SESSION['grandstream']['gdms_access_token_issued']['number'];
    if($_SESSION['grandstream']['gdms_access_token']['text'] && ($expires > 0 && $expires < 3600)) {
        return $_SESSION['grandstream']['gdms_access_token']['text'];
    }

    error_log("refreshing GDMS auth token");
    
    $ch = curl_init("https://www.gdms.cloud/oapi/oauth/token");
    curl_setopt($ch, CURLOPT_POST, 1);   
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'refresh_token' => $_SESSION['grandstream']['gdms_refresh_token']['text'],
            'grant_type' => "refresh_token",
            'client_id' => $_SESSION['grandstream']['gdms_api_id']['text'],
            'client_secret' => $_SESSION['grandstream']['gdms_api_secret']['text'],
        )));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $resp = json_decode(curl_exec($ch));
    
    curl_close($ch);

    if(!$resp->access_token) {
        error_log("error from GDMS: ".json_encode($resp)."\n");
        return null;
    }
    
    $_SESSION['grandstream']['gdms_access_token']['text'] = $resp->access_token;
    $_SESSION['grandstream']['gdms_access_token_issued']['number'] = time();

    return $resp->access_token;
}

function GDMSGet(string $url, array $params) {
    $params['access_token'] = GDMSAuthToken();
    $params['client_id'] = $_SESSION['grandstream']['gdms_api_id']['text'];
    $params['client_secret'] = $_SESSION['grandstream']['gdms_api_secret']['text'];
    $params['timestamp'] = time()."000";
    ksort($params);

    $params['signature'] = hash('sha256', "&".http_build_query($params)."&");
    
    unset($params['client_id']);
    unset($params['client_secret']);

    $ch = curl_init($url."?".http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $resp = json_decode(curl_exec($ch));
    curl_close($ch);

    return $resp;
}

function GDMSPost(string $url, array $params, array $body) {
    $params['access_token'] = GDMSAuthToken();
    $params['client_id'] = $_SESSION['grandstream']['gdms_api_id']['text'];
    $params['client_secret'] = $_SESSION['grandstream']['gdms_api_secret']['text'];
    $params['timestamp'] = time()."000";
    ksort($params);

    $bodyStr = json_encode($body);

    $bodyHash = "";
    if(sizeof($body) > 0) {
        $bodyHash = hash('sha256', $bodyStr)."&";
    }
    $params['signature'] = hash('sha256', "&".http_build_query($params)."&".$bodyHash);

    unset($params['client_id']);
    unset($params['client_secret']);

    $url .= "?".http_build_query($params);

    error_log("POSTing to GDMS URL: ".$url);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyStr);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $resp = json_decode(curl_exec($ch));
    curl_close($ch);

    return $resp;
}

function GDMSOrganizationList() {
    return GDMSGet("https://www.gdms.cloud/oapi/v1.0.0/org/list", array());
}

function GDMSSiteList(int $orgId) {
    return GDMSGet("https://www.gdms.cloud/oapi/v1.0.0/site/list", array('orgId' => $orgId));
}

function GDMSGetDeviceDetail(string $mac, int $isFirst) {
    return GDMSPost("https://www.gdms.cloud/oapi/v1.0.0/device/detail", array('mac' => $mac, 'isFirst' => $isFirst), array());
}

function GDMSAddDevice(int $orgId, int $siteId, string $mac, string $serial) {
    return GDMSPost("https://www.gdms.cloud/oapi/v1.0.0/device/add", array(), array(array(
        'orgId' => $orgId,
        'siteId' => $siteId,
        'mac' => $mac,
        'sn' => $serial,
    )));
}
