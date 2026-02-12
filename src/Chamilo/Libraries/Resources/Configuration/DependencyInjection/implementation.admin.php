<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Libraries\Implementation\Admin\SettingsConnector;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(SettingsConnector::class)->args(
        [
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
