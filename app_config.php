<?php
//application details
$apps[$x]['name'] = "Grandstream Extras";
$apps[$x]['uuid'] = "76404466-e461-11ed-9b2a-3b38e3f2dc83";
$apps[$x]['category'] = "App";
$apps[$x]['subcategory'] = "";
$apps[$x]['version'] = "0.1";
$apps[$x]['license'] = "GNU General Public License v3";
$apps[$x]['url'] = "https://github.com/AccelerateNetworks/fusionpbx-grandstream-extras";
$apps[$x]['description']['en-us'] = "Grandstream Extras";
$apps[$x]['description']['es-cl'] = "";
$apps[$x]['description']['de-de'] = "";
$apps[$x]['description']['de-ch'] = "";
$apps[$x]['description']['de-at'] = "";
$apps[$x]['description']['fr-fr'] = "";
$apps[$x]['description']['fr-ca'] = "";
$apps[$x]['description']['fr-ch'] = "";
$apps[$x]['description']['pt-pt'] = "";
$apps[$x]['description']['pt-br'] = "";


$y = 0;
$z = 0;

$apps[$x]['db'][$y]['table']['name'] = "grandstream_devices";
$apps[$x]['db'][$y]['table']['parent'] = "";


$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'device_uuid';
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_devices';
$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'device_uuid';
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_domains';
$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'domain_uuid';
$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'token';
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = 'the secret for authenticating';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'model';
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = 'model of this device';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'firmware_version';
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = 'the desired firmware version for this device';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'xmlapp';
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = 'name of the xmlapp this device is configured to run';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'serial';
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "longtext";
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "the device's serial number";
$z++;

$y=0;
$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "ea0fed96-e872-11ed-9896-437f185026f0";
$apps[$x]['default_settings'][$y]['default_setting_category'] = "grandstream";
$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "gdms_api_id";
$apps[$x]['default_settings'][$y]['default_setting_name'] = "string";
$apps[$x]['default_settings'][$y]['default_setting_value'] = "";
$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "false";
$apps[$x]['default_settings'][$y]['default_setting_description'] = "";
$y++;
$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "1a84a386-e873-11ed-85be-f7cc711e7f4c";
$apps[$x]['default_settings'][$y]['default_setting_category'] = "grandstream";
$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "gdms_api_secret";
$apps[$x]['default_settings'][$y]['default_setting_name'] = "string";
$apps[$x]['default_settings'][$y]['default_setting_value'] = "";
$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "false";
$apps[$x]['default_settings'][$y]['default_setting_description'] = "";
$y++;
$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "0dc68ace-e873-11ed-8022-d30541c5cd3f";
$apps[$x]['default_settings'][$y]['default_setting_category'] = "grandstream";
$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "gdms_refresh_token";
$apps[$x]['default_settings'][$y]['default_setting_name'] = "string";
$apps[$x]['default_settings'][$y]['default_setting_value'] = "";
$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "false";
$apps[$x]['default_settings'][$y]['default_setting_description'] = "";
$y++;
