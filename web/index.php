<?php
// use Chamilo\Libraries\Architecture\Kernel;
// /**
// * This script will load the requested application and launch it.
// */
// require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
// 'src/Chamilo/Libraries/Architecture/Bootstrap.php';

// $bootstrap = \Chamilo\Libraries\Architecture\Bootstrap::setup();
// $kernel = new Kernel($bootstrap->getRequest(), $bootstrap->getConfiguration());
// $kernel->launch();

// New launching code, don't activate this yet !!!
// /*
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

require_once realpath(__DIR__ . '/../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();
$container->get($container->getParameter('chamilo.configuration.kernel.service'))->launch();
// */