<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\User\Implementation\Admin\ActionProvider;
use Chamilo\Core\User\Implementation\Admin\SettingsConnector;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ActionProvider::class)->tag(ActionProviderInterface::class);
    $services->set(SettingsConnector::class)->tag(SettingsConnectorInterface::class);
};
