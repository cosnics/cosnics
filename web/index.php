<?php

use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\Architecture\Bootstrap\Kernel;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

require realpath(__DIR__ . '/../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();
$container->get(Kernel::class)->launch();