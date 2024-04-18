<?php
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

$autoloader = require realpath(__DIR__ . '/../') . '/vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();
$container->get(\Chamilo\Core\API\Service\Architecture\CustomAPIKernel::class)->launch();
