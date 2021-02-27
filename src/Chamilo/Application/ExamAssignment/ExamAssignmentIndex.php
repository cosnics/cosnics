<?php
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

$autoloader = require realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

try
{
    $requestValidator = $container->get(\Chamilo\Application\ExamAssignment\Service\Kernel\RequestValidator::class);
    $requestValidator->validateRequest();

    $container->get($container->getParameter('chamilo.configuration.kernel.service'))->launch();
}
catch(\Chamilo\Libraries\Architecture\Exceptions\NotAllowedException $ex)
{
    $notAllowedResponseGenerator =
        $container->get(\Chamilo\Application\ExamAssignment\Service\Kernel\NotAllowedResponseGenerator::class);

    $notAllowedResponseGenerator->getNotAllowedResponse()->send();
}
