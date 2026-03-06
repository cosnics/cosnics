<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Component\BrowseComponent;
use Chamilo\Core\Admin\Component\DiagnoseComponent;
use Chamilo\Core\Admin\Component\ViewOnlineComponent;
use Chamilo\Core\Admin\Component\ViewLogsComponent;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(BrowseComponent::class)->tag(ApplicationInterface::class);
    $services->set(DiagnoseComponent::class)->tag(ApplicationInterface::class);
    $services->set(ViewOnlineComponent::class)->tag(ApplicationInterface::class);
    $services->set(ViewLogsComponent::class)->tag(ApplicationInterface::class);
};
