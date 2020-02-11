<?php

use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use GuzzleHttp\Exception\ServerException;

require_once realpath(__DIR__ . '/../../../../../../../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();

$url =
    'http://localhost/connect/src/Chamilo/Application/Weblcms/Tool/Implementation/Ephorus/Webservice/ephorus_reporting_service.php';

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
