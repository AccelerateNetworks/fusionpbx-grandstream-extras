<?php

function render_list($token) {
    global $baseURL;

    $out = new XMLWriter();
    $out->openMemory();
    $out->startDocument('1.0','UTF-8');
    $out->startElement("xmlapp");
    $out->writeAttribute('title', "Voicemail");

    $out->startElement('view');

    $sql = "SELECT v_voicemail_messages.* FROM grandstream_devices, v_device_lines, v_voicemails, v_voicemail_messages WHERE grandstream_devices.token = :token AND v_device_lines.device_uuid = grandstream_devices.device_uuid AND v_voicemails.domain_uuid = grandstream_devices.domain_uuid AND v_voicemails.voicemail_id = v_device_lines.user_id AND v_voicemail_messages.voicemail_uuid = v_voicemails.voicemail_uuid ORDER BY created_epoch DESC LIMIT 50";
    $parameters['token'] = $_GET['token'];
    $database = new database;
    $messages = $database->select($sql, $parameters, 'all');
    unset($parameters);
    $out->startElement('section');
    foreach($messages as $message) {
        $out->startElement('command');
        $out->writeAttribute('label', $message['caller_id_name']);
        $out->writeAttribute('text', $message['created_epoch']);
        $out->writeAttribute('action', $baseURL.'&app=voicemail&voicemail_uuid='.$message['voicemail_message_uuid']);
        $out->endElement(); // </command>
    }

    // if we send an empty <section> the phone renders an error screen that cannot be exited
    if(sizeof($messages) < 2) {
        $out->startElement('command');
        $out->writeAttribute('label', 'no more voicemails');
        $out->endElement(); // </command>
    }
    $out->endElement(); // </section>

    $out->endElement(); // </view>

    // softkeys
    $out->startElement('Softkeys');

    $out->startElement('Softkey');
    $out->writeAttribute('action', 'QuitApp');
    $out->writeAttribute('label', 'Exit');
    $out->endElement(); // </softkey>

    $out->endElement(); // </softkeys>

    $out->endElement(); // </xmlapp>

    echo $out->outputMemory();
}

function render_message(string $token, string $messageID) {
    global $baseURL;

    $database = new database;

    if($_GET['action'] == "read") {
        $sql = "UPDATE v_voicemail_messages SET message_status = 'saved' WHERE voicemail_message_uuid = :message_uuid";
        $parameters['message_uuid'] = $messageID;
        $database->execute($sql, $parameters);
    }

    $sql = "SELECT * FROM v_voicemail_messages WHERE voicemail_message_uuid = :message_uuid";
    $parameters['message_uuid'] = $messageID;
    $message = $database->select($sql, $parameters, 'row');
    unset($parameters);


    $out = new XMLWriter();
    $out->openMemory();
    $out->startDocument('1.0','UTF-8');
    $out->startElement("xmlapp");
    $out->writeAttribute('title', "Voicemail from ".$message['caller_id_name']);

    $out->startElement('view');

    $out->startElement('section');
        
    $out->startElement('text');
    $out->writeAttribute('label', "From: ".$message['caller_id_number'].' Duration: '.$message['message_length']);
    $out->endElement();

    $out->startElement('text');
    $out->writeAttribute('label', "Transcript: \n\n".$message['message_transcription']);
    $out->endElement(); // </text>

    $out->endElement(); // </section>

    $out->endElement(); // </view>

    // softkeys
    $out->startElement('Softkeys');

    $out->startElement('Softkey');
    $out->writeAttribute('action', 'UseURL');
    $out->writeAttribute('commandArgs', $baseURL."&app=voicemail");
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

    if($message['message_status'] != "saved") {
        $out->startElement('SoftKey');
        $out->writeAttribute('action', 'UseURL');
        $out->writeAttribute('commandArgs', $baseURL."&app=voicemail&voicemail_uuid=".$messageID."&action=read");
        $out->writeAttribute('label', 'Mark Read');
        $out->endElement(); // </softkey>
    }

    $out->endElement(); // </softkeys>

    $out->endElement(); // </xmlapp>

    echo $out->outputMemory();
}

if($_GET['voicemail_uuid']) {
    render_message($_GET['token'], $_GET['voicemail_uuid']);
} else {
    render_list($_GET['token']);
}
