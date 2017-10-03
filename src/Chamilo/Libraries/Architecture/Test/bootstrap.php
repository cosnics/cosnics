<?php

/**
 * Bootstrap for test file Require config.inc.php and set the include path to the original include path.
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
$original_include_path = get_include_path();

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

require_once realpath(__DIR__ . '/../../../../../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

ini_set('include_path', get_include_path() . PATH_SEPARATOR . $original_include_path);
