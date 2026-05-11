<?php
namespace Chamilo\Core\User\Architecture\Enum;

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

/**
 * @package Chamilo\Core\User\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ActionEnum: string
{
    case ACCOUNT = 'Account';
    case ACTIVE = 'Active';
    case BROWSE = 'Browse';
    case CONFIGURE = 'Configure';
    case CREATE = 'Create';
    case DELETE = 'Delete';
    case DOWNLOAD_USER_PICTURE = 'DownloadUserPicture';
    case LANGUAGE = 'Language';
    case LEAVE = 'Leave';
    case LOGIN_AS = 'LoginAs';
    case LOGOUT = 'Logout';
    case REGISTER = 'Register';
    case RESET_PASSWORD = 'ResetPassword';
    case RESET_PASSWORD_MULTI = 'MultiPasswordReset';
    case UPDATE = 'Update';
    case UPDATE_USER_PICTURE = 'UpdateUserPicture';
    case USERS_FEED = 'UsersFeed';
    case VIEW = 'View';

    public static function getAction(string $className): ActionEnum
    {
        return match ($className) {
            MultiPasswordResetComponent::class => self::RESET_PASSWORD_MULTI,
            UpdateComponent::class => self::UPDATE,
            DownloadUserPictureComponent::class => self::DOWNLOAD_USER_PICTURE,
            DeleteComponent::class => self::DELETE,
            LeaveComponent::class => self::LEAVE,
            ConfigureComponent::class => self::CONFIGURE,
            LogoutComponent::class => self::LOGOUT,
            BrowseComponent::class => self::BROWSE,
            LoginAsComponent::class => self::LOGIN_AS,
            UsersFeedComponent::class => self::USERS_FEED,
            ResetPasswordComponent::class => self::RESET_PASSWORD,
            ActiveComponent::class => self::ACTIVE,
            UpdateUserPictureComponent::class => self::UPDATE_USER_PICTURE,
            CreateComponent::class => self::CREATE,
            LanguageComponent::class => self::LANGUAGE,
            RegisterComponent::class => self::REGISTER,
            ViewComponent::class => self::VIEW,
            AccountComponent::class => self::ACCOUNT
        };
    }

    public static function getActionValue(string $className): string
    {
        return self::getAction($className)->value;
    }
}
