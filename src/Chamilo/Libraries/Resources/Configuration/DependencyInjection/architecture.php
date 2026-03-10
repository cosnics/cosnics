<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Service\Bootstrap\ApplicationFactory;
use Chamilo\Libraries\Service\Bootstrap\Bootstrap;
use Chamilo\Libraries\Service\Bootstrap\Kernel;
use Chamilo\Libraries\Service\Resource\ResourceGenerator;
use Chamilo\Libraries\Service\Routing\DataClassUrlGenerator;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ActionResultRenderer;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(Bootstrap::class)->args(['$showErrors' => '%cosnics.libraries.protocol.error.show%']);
    $services->set(Kernel::class)->args(
        [
            '$user' => service('Chamilo\Core\User\CurrentUser'),
            '$timezone' => '%cosnics.libraries.calendar.timezone%',
            '$maintenanceMode' => '%cosnics.libraries.service.maintenanceMode%'
        ]
    );
    $services->set(ApplicationFactory::class);

    $services->set(ChamiloRequest::class)->factory([ChamiloRequest::class, 'createFromGlobals']);

    $services->set(EventDispatcher::class);
    $services->alias(EventDispatcherInterface::class, EventDispatcher::class);

    $services->set(ClassnameUtilities::class);
    $services->set(DataClassUrlGenerator::class);
    $services->set(UrlGenerator::class);
    $services->set(ResourceGenerator::class);
    $services->set(ActionResultRenderer::class);
};
