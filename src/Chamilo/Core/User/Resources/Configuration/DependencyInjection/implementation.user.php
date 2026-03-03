<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Implementation\User\PlatformUserPictureProvider;
use Chamilo\Core\User\Implementation\User\UserDetailsRenderer;
use Chamilo\Core\User\Implementation\User\UserSettingsRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(PlatformUserPictureProvider::class)->args(
        [
            '$themeSystemPathBuilder' => service(
                'Chamilo\Libraries\UserInterface\Theme\Service\ThemeSystemPathBuilder'
            )
        ]
    )->tag(UserPictureProviderInterface::class);

    $services->set(UserDetailsRenderer::class)->tag(UserDetailsRendererInterface::class);
    $services->set(UserSettingsRenderer::class)->tag(UserDetailsRendererInterface::class);
};
