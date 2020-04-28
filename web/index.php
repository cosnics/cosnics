<?php

use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;

$autoloader = require realpath(__DIR__ . '/../') . '/vendor/autoload.php';
AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();
$container->get($container->getParameter('chamilo.configuration.kernel.service'))->launch();