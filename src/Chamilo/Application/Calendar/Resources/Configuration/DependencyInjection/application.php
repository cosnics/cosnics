<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Component\AvailabilityComponent;
use Chamilo\Application\Calendar\Component\BrowseComponent;
use Chamilo\Application\Calendar\Component\ICalComponent;
use Chamilo\Application\Calendar\Component\PrintComponent;
use Chamilo\Application\Calendar\Component\VisibilityComponent;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(AvailabilityComponent::class)->tag(ApplicationInterface::class);
    $services->set(BrowseComponent::class)->args(
        ['$themeWebPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder')]
    )->tag(ApplicationInterface::class);
    $services->set(ICalComponent::class)->tag(ApplicationInterface::class);
    $services->set(PrintComponent::class)->args(
        ['$themeWebPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder')]
    )->tag(ApplicationInterface::class);
    $services->set(VisibilityComponent::class)->tag(ApplicationInterface::class);
};