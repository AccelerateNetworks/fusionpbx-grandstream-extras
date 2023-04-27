<?php
function app_name() {
    return "Voicemail";
}

function app_render() {
    $baseURL = "https://".$_SERVER['HTTP_HOST'].$_SERVER['DOCUMENT_URI']."?app=voicemail&token=".$_GET['token'];

    if($_GET['voicemail_uuid']) {
        $sql = "SELECT * FROM v_voicemail_messages WHERE voicemail_uuid = :voicemail_box AND voicemail_message_uuid = :message_uuid LIMIT 1";
        $sql = "SELECT v_voicemail_messages.* FROM grandstream_devices, v_device_lines, v_voicemails, v_voicemail_messages WHERE grandstream_devices.token = :token AND v_device_lines.device_uuid = grandstream_devices.device_uuid AND v_voicemails.domain_uuid = grandstream_devices.domain_uuid AND v_voicemails.voicemail_id = v_device_lines.user_id AND v_voicemail_messages.voicemail_uuid = v_voicemails.voicemail_uuid AND voicemail_message_uuid = :message_uuid";
        $parameters['token'] = $_GET['token'];
        $parameters['message_uuid'] = $_GET['voicemail_uuid'];
        $database = new database;
        $message = $database->select($sql, $parameters, 'row');
        unset($parameters);
    }

    $out = new XMLWriter();
    $out->openMemory();
    $out->startDocument('1.0','UTF-8');
    $out->startElement("xmlapp");

    // title
    if($_GET['voicemail_uuid']) {
        $out->writeAttribute('title', "Voicemail from ".$message['caller_id_name']);
    } else {
        $out->writeAttribute('title', "Voicemail");
    }

    // view
    $out->startElement('view');
    if($_GET['voicemail_uuid']) {
        $out->startElement('section');
        
        $out->startElement('text');
        $out->writeAttribute('label', "From: ".$message['caller_id_number'].' Duration: '.$message['message_length']);
        $out->endElement();

        $out->startElement('text');
        $out->writeAttribute('label', "Transcript: \n\n".$message['message_transcription']);
        $out->endElement(); // </text>

        $out->endElement(); // </section>
    } else {
        $sql = "SELECT v_voicemail_messages.* FROM grandstream_devices, v_device_lines, v_voicemails, v_voicemail_messages WHERE grandstream_devices.token = :token AND v_device_lines.device_uuid = grandstream_devices.device_uuid AND v_voicemails.domain_uuid = grandstream_devices.domain_uuid AND v_voicemails.voicemail_id = v_device_lines.user_id AND v_voicemail_messages.voicemail_uuid = v_voicemails.voicemail_uuid ORDER BY created_epoch DESC LIMIT 50";
        $parameters['token'] = $_GET['token'];
        $database = new database;
        $messages = $database->select($sql, $parameters, 'all');
        unset($parameters);
        $out->startElement('section');
        if(sizeof($messages) == 0) {
            $out->startElement('command');
            $out->writeAttribute('label', 'no voicemails');
            $out->writeAttribute('action', $baseURL);
            $out->endElement(); // </command>
        }
        foreach($messages as $message) {
            $out->startElement('command');
            $out->writeAttribute('label', $message['caller_id_name']);
            $out->writeAttribute('text', $message['created_epoch']);
            $out->writeAttribute('action', $baseURL.'&voicemail_uuid='.$message['voicemail_message_uuid']);
            $out->endElement(); // </command>
        }
        $out->endElement(); // </section>
    }
    $out->endElement(); // </view>

    // softkeys
    $out->startElement('Softkeys');

    if($_GET['voicemail_uuid']) {
        $out->startElement('Softkey');
        $out->writeAttribute('action', 'UseURL');
        $out->writeAttribute('commandArgs', $baseURL);
        $out->writeAttribute('label', 'Back');
        $out->endElement(); // </softkey>

        // $out->startElement('Softkey');
        // $out->writeAttribute('action', 'Dial');
        // $out->writeAttribute('commandArgs', "listen_vm+".$message['voicemail_message_uuid']);
        // $out->writeAttribute('label', 'Listen');
        // $out->endElement(); // </softkey>

        $out->startElement('Softkey');
        $out->writeAttribute('action', 'Dial');
        $out->writeAttribute('commandArgs', $message['caller_id_number']);
        $out->writeAttribute('label', 'Call Back');
        $out->endElement(); // </softkey>

    } else {
        $out->startElement('Softkey');
        $out->writeAttribute('action', 'QuitApp');
        $out->writeAttribute('label', 'Exit');
        $out->endElement(); // </softkey>
    }

    $out->endElement(); // </softkeys>

    $out->endElement(); // </xmlapp>

    echo $out->outputMemory();
}
