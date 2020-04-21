<?php

/**
 * Bootstrap for test file Require config.inc.php and set the include path to the original include path.
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
$original_include_path = get_include_path();

use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;

$autoloader = require realpath(__DIR__ . '/../../../../../') . '/vendor/autoload.php';
AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();

ini_set('include_path', get_include_path() . PATH_SEPARATOR . $original_include_path);
