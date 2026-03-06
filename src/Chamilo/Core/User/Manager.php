<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Component\AccountComponent;
use Chamilo\Core\User\Component\ActiveComponent;
use Chamilo\Core\User\Component\ConfigureComponent;
use Chamilo\Core\User\Component\DownloadUserPictureComponent;
use Chamilo\Core\User\Component\LanguageComponent;
use Chamilo\Core\User\Component\LeaveComponent;
use Chamilo\Core\User\Component\LoginAsComponent;
use Chamilo\Core\User\Component\LogoutComponent;
use Chamilo\Core\User\Component\MultiPasswordResetComponent;
use Chamilo\Core\User\Component\RegisterComponent;
use Chamilo\Core\User\Component\ResetPasswordComponent;
use Chamilo\Core\User\Component\UpdateUserPictureComponent;
use Chamilo\Core\User\Component\UsersFeedComponent;
use Chamilo\Core\User\Component\ViewComponent;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;

/**
 * @package Chamilo\Core\User
 */
abstract class Manager extends Application
{
    public const ACTION_ACCOUNT = 'Account';
    public const ACTION_ACTIVE = 'Active';
    public const ACTION_BROWSE = 'Browse';
    public const ACTION_CONFIGURE = 'Configure';
    public const ACTION_CREATE = 'Create';
    public const ACTION_DELETE = 'Delete';
    public const ACTION_DOWNLOAD_USER_PICTURE = 'DownloadUserPicture';
    public const ACTION_LANGUAGE = 'Language';
    public const ACTION_LEAVE = 'LEAVE';
    public const ACTION_LOGIN_AS = 'LoginAs';
    public const ACTION_LOGOUT = 'Logout';
    public const ACTION_REGISTER = 'Register';
    public const ACTION_RESET_PASSWORD = 'ResetPassword';
    public const ACTION_RESET_PASSWORD_MULTI = 'MultiPasswordReset';
    public const ACTION_UPDATE = 'Update';
    public const ACTION_UPDATE_USER_PICTURE = 'UpdateUserPicture';
    public const ACTION_USERS_FEED = 'UsersFeed';
    public const ACTION_VIEW = 'View';
    public const CONTEXT = __NAMESPACE__;
    public const PARAM_ACTIVE = 'active';
    public const PARAM_LANGUAGE = 'language';
    public const PARAM_REFER = 'refer';
    public const PARAM_RESET_KEY = 'key';
    public const PARAM_USER_ID = 'user_id';

    /**
     * @param class-string<\Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface> $className
     */
    protected function getActiveMailer(string $className = 'Chamilo\Libraries\Mail\Mailer\ActiveMailer'
    ): MailerInterface
    {
        return $this->getService($className);
    }

    public function getApplicationAction(): string
    {
        return match (static::class) {
            MultiPasswordResetComponent::class => self::ACTION_RESET_PASSWORD_MULTI,
            Component\UpdateComponent::class => self::ACTION_UPDATE,
            DownloadUserPictureComponent::class => self::ACTION_DOWNLOAD_USER_PICTURE,
            Component\DeleteComponent::class => self::ACTION_DELETE,
            LeaveComponent::class => self::ACTION_LEAVE,
            ConfigureComponent::class => self::ACTION_CONFIGURE,
            LogoutComponent::class => self::ACTION_LOGOUT,
            Component\BrowseComponent::class => self::ACTION_BROWSE,
            LoginAsComponent::class => self::ACTION_LOGIN_AS,
            UsersFeedComponent::class => self::ACTION_USERS_FEED,
            ResetPasswordComponent::class => self::ACTION_RESET_PASSWORD,
            ActiveComponent::class => self::ACTION_ACTIVE,
            UpdateUserPictureComponent::class => self::ACTION_UPDATE_USER_PICTURE,
            Component\CreateComponent::class => self::ACTION_CREATE,
            LanguageComponent::class => self::ACTION_LANGUAGE,
            RegisterComponent::class => self::ACTION_REGISTER,
            ViewComponent::class => self::ACTION_VIEW,
            AccountComponent::class => self::ACTION_ACCOUNT
        };
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    protected function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->getService(AuthenticationValidator::class);
    }

    public function getDefaultApplicationAction(): string
    {
        return self::ACTION_BROWSE;
    }

    protected function getUserUrlGenerator(): UserUrlGenerator
    {
        return $this->getService(UserUrlGenerator::class);
    }
}
