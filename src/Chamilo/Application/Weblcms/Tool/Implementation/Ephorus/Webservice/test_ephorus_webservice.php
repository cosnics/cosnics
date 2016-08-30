<?php
use GuzzleHttp\Exception\ServerException;
require_once __DIR__ . '/../../../../../../Libraries/Architecture/Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap::getInstance()->setup();

$url = 'http://localhost/connect/src/Chamilo/Application/Weblcms/Tool/Implementation/Ephorus/Webservice/ephorus_reporting_service.php';

$client = new \GuzzleHttp\Client(['base_url' => $url]);

$contents = file_get_contents(__DIR__ . '/sample.xml');

$request = $client->createRequest('POST', '', array('body' => $contents));
$request->setHeader('SOAPAction', 'http://reporting.ephorus.org/');

try
{
    $response = $client->send($request);
    var_dump($response->getBody()->__toString());
}
catch (ServerException $exception)
{
    var_dump($exception->getResponse()->getBody()->__toString());
}
