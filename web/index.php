<?php

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Service\Bootstrap\Bootstrap;
use Chamilo\Libraries\Service\Bootstrap\Kernel;

require realpath(__DIR__ . '/../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();
$container->get(Kernel::class)->launch();