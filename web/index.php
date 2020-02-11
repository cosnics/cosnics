<?php

use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

$autoloader = require realpath(__DIR__ . '/../') . '/vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();
$container->get($container->getParameter('chamilo.configuration.kernel.service'))->launch();