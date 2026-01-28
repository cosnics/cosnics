<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\Admin\Implementation\Admin\ActionProvider;
use Chamilo\Core\Admin\Implementation\Admin\SettingsConnector;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ActionProvider::class)->tag(ActionProviderInterface::class);

    $services->set(SettingsConnector::class)->args(
        [
            '$themeSystemPathBuilder' => service(
                'Chamilo\Libraries\UserInterface\Theme\Service\ThemeSystemPathBuilder'
            )
        ]
    )->tag(SettingsConnectorInterface::class);
};
