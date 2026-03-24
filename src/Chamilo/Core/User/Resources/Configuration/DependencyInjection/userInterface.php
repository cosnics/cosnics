<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\UserInterface\Form\AccountFormType;
use Chamilo\Core\User\UserInterface\Form\ConfigurationFormType;
use Chamilo\Core\User\UserInterface\Form\RegisterFormType;
use Chamilo\Core\User\UserInterface\Form\UserFormType;
use Chamilo\Core\User\UserInterface\Form\UserPictureUpdateFormType;
use Chamilo\Core\User\UserInterface\Table\UserTableRenderer;
use Symfony\Component\Form\FormTypeInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserTableRenderer::class)->args(['$currentUser' => service('Chamilo\Core\User\CurrentUser')]);

    $services->set(AccountFormType::class)->args([
        '$userRights' => [
            'cosnics.application.user.rights.changeGivenName' => '%cosnics.application.user.rights.changeGivenName%',
            'cosnics.application.user.rights.changeSurname' => '%cosnics.application.user.rights.changeSurname%',
            'cosnics.application.user.rights.changeUsername' => '%cosnics.application.user.rights.changeUsername%',
            'cosnics.application.user.rights.changeEmail' => '%cosnics.application.user.rights.changeEmail%',
            'cosnics.application.user.rights.changeOfficialCode' => '%cosnics.application.user.rights.changeOfficialCode%',
            'cosnics.application.user.rights.changePassword' => '%cosnics.application.user.rights.changePassword%',
            'cosnics.application.user.rights.changeUserPicture' => '%cosnics.application.user.rights.changeUserPicture%'
        ],
        '$userRequirements' => [
            'cosnics.application.user.require.email' => '%cosnics.application.user.require.email%',
            'cosnics.application.user.require.officialCode' => '%cosnics.application.user.require.officialCode%'
        ]

    ])->tag(FormTypeInterface::class);
    $services->set(UserPictureUpdateFormType::class)->args([
        '$userRights' => [
            'cosnics.application.user.rights.changeUserPicture' => '%cosnics.application.user.rights.changeUserPicture%'
        ]
    ])->tag(FormTypeInterface::class);

    $services->set(UserFormType::class)->args([
        '$userRights' => [
            'cosnics.application.user.rights.changeGivenName' => '%cosnics.application.user.rights.changeGivenName%',
            'cosnics.application.user.rights.changeSurname' => '%cosnics.application.user.rights.changeSurname%',
            'cosnics.application.user.rights.changeUsername' => '%cosnics.application.user.rights.changeUsername%',
            'cosnics.application.user.rights.changeEmail' => '%cosnics.application.user.rights.changeEmail%',
            'cosnics.application.user.rights.changeOfficialCode' => '%cosnics.application.user.rights.changeOfficialCode%',
            'cosnics.application.user.rights.changePassword' => '%cosnics.application.user.rights.changePassword%',
            'cosnics.application.user.rights.changeUserPicture' => '%cosnics.application.user.rights.changeUserPicture%'
        ],
        '$userRequirements' => [
            'cosnics.application.user.require.email' => '%cosnics.application.user.require.email%',
            'cosnics.application.user.require.officialCode' => '%cosnics.application.user.require.officialCode%'
        ]
    ])->tag(FormTypeInterface::class);

    $services->set(RegisterFormType::class)->args([
        '$userRights' => [
            'cosnics.application.user.rights.changeGivenName' => '%cosnics.application.user.rights.changeGivenName%',
            'cosnics.application.user.rights.changeSurname' => '%cosnics.application.user.rights.changeSurname%',
            'cosnics.application.user.rights.changeUsername' => '%cosnics.application.user.rights.changeUsername%',
            'cosnics.application.user.rights.changeEmail' => '%cosnics.application.user.rights.changeEmail%',
            'cosnics.application.user.rights.changeOfficialCode' => '%cosnics.application.user.rights.changeOfficialCode%',
            'cosnics.application.user.rights.changePassword' => '%cosnics.application.user.rights.changePassword%',
            'cosnics.application.user.rights.changeUserPicture' => '%cosnics.application.user.rights.changeUserPicture%'
        ],
        '$userRequirements' => [
            'cosnics.application.user.require.email' => '%cosnics.application.user.require.email%',
            'cosnics.application.user.require.officialCode' => '%cosnics.application.user.require.officialCode%'
        ]
    ])->tag(FormTypeInterface::class);

    $services->set(ConfigurationFormType::class)->tag(FormTypeInterface::class);
};
