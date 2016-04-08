<?php
use Chamilo\Libraries\Architecture\Kernel;
/**
 * This script will load the requested application and launch it.
 */
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src/Chamilo/Libraries/Architecture/Bootstrap.php';

$bootstrap = \Chamilo\Libraries\Architecture\Bootstrap :: setup();
$kernel = new Kernel($bootstrap->getRequest(), $bootstrap->getConfiguration());
$kernel->launch();