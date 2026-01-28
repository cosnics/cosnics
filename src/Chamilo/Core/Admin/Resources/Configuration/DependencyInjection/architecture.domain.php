<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Architecture\Domain\ActionProviderCollection;
use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorCollection;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ActionProviderCollection::class);
    $services->set(SettingsConnectorCollection::class);
};
