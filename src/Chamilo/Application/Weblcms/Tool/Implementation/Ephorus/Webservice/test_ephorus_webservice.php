<?php

$url = 'http://edison.hogent.be/ephorus_test/application/weblcms/tool/ephorus/' .
    'php/webservice/ephorus_reporting_service.php';

$contents = file_get_contents(__DIR__ . '/sample.xml');

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt(
    $ch, CURLOPT_HTTPHEADER,
    array(
        'Content-Type: text/xml; charset=utf-8',
        'Content-Length: ' . strlen($contents)
    )
);

$result = curl_exec($ch);

echo '<pre>';

curl_close($ch);
