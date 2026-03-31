<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Architecture\Domain\ActionProviderRegistry;
use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorRegistry;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ActionProviderRegistry::class)->args(['$twigFormEnvironment' => service('Twig\Environment\Form')]);
    $services->set(SettingsConnectorRegistry::class);
};
