<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Service\UserFactory;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Libraries\Storage\Factory\SymfonyCacheAdapterFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

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
        '$securityKey' => '%chamilo.configuration.general.security_key%',
    ]);

    $services->set(UserSettingService::class)->args(
        ['$userSettingsCacheAdapter' => service('Chamilo\Core\User\Service\Cache\UserSettingCacheService')]
    );

    $services->set('Chamilo\Core\User\Service\Cache\UserSettingCacheService', FilesystemAdapter::class)->args(
        ['$namespace' => 'Chamilo\Core\User\UserSetting']
    )->tag(AdapterInterface::class)->factory([service(SymfonyCacheAdapterFactory::class), 'createFilesystemAdapter']);

    $services->set(UserUrlGenerator::class);
};
