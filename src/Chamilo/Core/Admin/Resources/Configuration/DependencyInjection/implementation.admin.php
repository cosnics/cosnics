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
            ),
            '$userRights' => [
                'changeEmail' => '%cosnics.application.user.rights.changeEmail%',
                'changeGivenName' => '%cosnics.application.user.rights.changeGivenName%',
                'changeSurname' => '%cosnics.application.user.rights.changeSurname%',
                'changeOfficialCode' => '%cosnics.application.user.rights.changeOfficialCode%',
                'changePassword' => '%cosnics.application.user.rights.changePassword%',
                'changeUserPicture' => '%cosnics.application.user.rights.changeUserPicture%',
                'changeUsername' => '%cosnics.application.user.rights.changeUsername%',
                'retrievePassword' => '%cosnics.application.user.rights.retrievePassword%',
                'register' => '%cosnics.application.user.rights.register%',
                'changeLanguage' => '%cosnics.application.user.rights.changeLanguage%',
                'changeTimezone' => '%cosnics.application.user.rights.changeTimezone%'
            ]
        ]
    )->tag(SettingsConnectorInterface::class);
};
