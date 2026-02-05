<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Service\UserFactory;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserFactory::class)->args([
        '$themeWebPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder'),
        '$themeSystemPathBuilder' => service(
            'Chamilo\Libraries\UserInterface\Theme\Service\ThemeSystemPathBuilder'
        ),
    ]);

    $services->set(UserService::class)->args([
        '$userSettingsCacheAdapter' => service('Chamilo\Core\User\Service\Cache\UserSettingCacheService'),
        '$activeMailer' => service('Chamilo\Libraries\Protocol\Mail\ActiveMailer'),
        '$securityKey' => '%chamilo.configuration.general.securityKey%',
    ]);

    $services->set(UserUrlGenerator::class);
};
