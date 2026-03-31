<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Component\AvailabilityComponent;
use Chamilo\Application\Calendar\Component\BrowseComponent;
use Chamilo\Application\Calendar\Component\ICalComponent;
use Chamilo\Application\Calendar\Component\PrintComponent;
use Chamilo\Application\Calendar\Component\VisibilityComponent;
use Chamilo\Application\Calendar\Service\VisibilityService;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(AvailabilityComponent::class)->args(['$twigFormEnvironment' => service('Twig\Environment\Form')])
        ->tag(ApplicationInterface::class);
    $services->set(BrowseComponent::class)->args(
        [
            '$defaultView' => '%cosnics.libraries.calendar.defaultView%',
            '$theme' => '%cosnics.libraries.userInterface.theme%'
        ]
    )->tag(ApplicationInterface::class);
    $services->set(ICalComponent::class)->tag(ApplicationInterface::class);
    $services->set(PrintComponent::class)->args(
        [
            '$defaultView' => '%cosnics.libraries.calendar.defaultView%',
            '$theme' => '%cosnics.libraries.userInterface.theme%'
        ]
    )->tag(ApplicationInterface::class);
    $services->set(VisibilityComponent::class)->args(['$visibilityService' => service(VisibilityService::class)])->tag(
        ApplicationInterface::class
    );
};