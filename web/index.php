<?php
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

require_once realpath(__DIR__ . '/../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();
$container->get($container->getParameter('chamilo.configuration.kernel.service'))->launch();