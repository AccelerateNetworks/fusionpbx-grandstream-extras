<?php

$xmlapps = array(
    'voicemail' => array('title' => 'Voicemail'),
    'fake' => array('title' => 'Fake App Do Not Click', 'subtitle' => 'bad'),
);

$baseURL = "https://".$_SERVER['HTTP_HOST'].$_SERVER['DOCUMENT_URI']."?token=".$_GET['token'];


function output_launcher_app() {
    global $baseURL;
    global $xmlapps;

    $out = new XMLWriter();
    $out->openMemory();
    $out->startDocument('1.0','UTF-8');
    $out->startElement("xmlapp");
    $out->writeAttribute('title', "Apps");

    $out->startElement('view');

    $out->startElement('section');

    foreach($xmlapps as $slug => $app) {
        $out->startElement('command');
        $out->writeAttribute('label', $app['title']);
        if($app['subtitle']) {
            $out->writeAttribute('text', $app['subtitle']);
        }
        $out->writeAttribute('action', $baseURL.'&app='.$slug);
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
