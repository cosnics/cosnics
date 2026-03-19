<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Component\AccountComponent;
use Chamilo\Core\User\Component\ActiveComponent;
use Chamilo\Core\User\Component\BrowseComponent;
use Chamilo\Core\User\Component\ConfigureComponent;
use Chamilo\Core\User\Component\CreateComponent;
use Chamilo\Core\User\Component\DeleteComponent;
use Chamilo\Core\User\Component\DownloadUserPictureComponent;
use Chamilo\Core\User\Component\LanguageComponent;
use Chamilo\Core\User\Component\LeaveComponent;
use Chamilo\Core\User\Component\LoginAsComponent;
use Chamilo\Core\User\Component\LogoutComponent;
use Chamilo\Core\User\Component\MultiPasswordResetComponent;
use Chamilo\Core\User\Component\RegisterComponent;
use Chamilo\Core\User\Component\ResetPasswordComponent;
use Chamilo\Core\User\Component\UpdateComponent;
use Chamilo\Core\User\Component\UpdateUserPictureComponent;
use Chamilo\Core\User\Component\UsersFeedComponent;
use Chamilo\Core\User\Component\ViewComponent;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(MultiPasswordResetComponent::class)->tag(ApplicationInterface::class);
    $services->set(UpdateComponent::class)->tag(ApplicationInterface::class);
    $services->set(DownloadUserPictureComponent::class)->tag(ApplicationInterface::class);
    $services->set(DeleteComponent::class)->tag(ApplicationInterface::class);
    $services->set(LeaveComponent::class)->tag(ApplicationInterface::class);
    $services->set(ConfigureComponent::class)->args(
        ['$userCanChangePicture' => '%cosnics.application.user.rights.changeUserPicture%']
    )->tag(ApplicationInterface::class);
    $services->set(LogoutComponent::class)->tag(ApplicationInterface::class);
    $services->set(BrowseComponent::class)->tag(ApplicationInterface::class);
    $services->set(LoginAsComponent::class)->tag(ApplicationInterface::class);
    $services->set(UsersFeedComponent::class)->tag(ApplicationInterface::class);
    $services->set(ResetPasswordComponent::class)->args(
        ['$userCanRetrievePassword' => '%cosnics.application.user.rights.retrievePassword%']
    )->tag(ApplicationInterface::class);
    $services->set(ActiveComponent::class)->tag(ApplicationInterface::class);
    $services->set(UpdateUserPictureComponent::class)->args(
        ['$userCanChangePicture' => '%cosnics.application.user.rights.changeUserPicture%']
    )->tag(ApplicationInterface::class);
    $services->set(CreateComponent::class)->tag(ApplicationInterface::class);
    $services->set(LanguageComponent::class)->args(
        ['$userCanChangeLanguage' => '%cosnics.application.user.rights.changeLanguage%']
    )->tag(ApplicationInterface::class);
    $services->set(RegisterComponent::class)->args(['$userCanRegister' => '%cosnics.application.user.rights.register%'])
        ->tag(ApplicationInterface::class);
    $services->set(ViewComponent::class)->tag(ApplicationInterface::class);
    $services->set(AccountComponent::class)->args(
        [
            '$userCanChangePicture' => '%cosnics.application.user.rights.changeUserPicture%',
            '$twigFormEnvironment' => service('Twig\Environment\Form')
        ]
    )->tag(ApplicationInterface::class);
};
