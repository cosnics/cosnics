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
        '$canChangeLanguage' => '%cosnics.application.user.rights.changeLanguage%',
        '$canChangeTimezone' => '%cosnics.application.user.rights.changeTimezone%',
    ]);

    $services->set(UserService::class)->args([
        '$activeMailer' => service('Chamilo\Libraries\Protocol\Mail\ActiveMailer'),
        '$securityKey' => '%cosnics.libraries.protocol.security.securityKey%',
        '$siteName' => '%cosnics.libraries.userInterface.layout.site.name%',
        '$administratorName' => '%cosnics.libraries.userInterface.layout.administrator.name%',
        '$administratorEmail' => '%cosnics.libraries.userInterface.layout.administrator.email%',
        '$allowRegistration' => '%cosnics.application.user.rights.register%'
    ]);

    $services->set(UserUrlGenerator::class);
};
