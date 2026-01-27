<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;

/**
 * @package Chamilo\Core\User
 */
abstract class Manager extends Application
{
    public const ACTION_ACCOUNT = 'Account';
    public const ACTION_ACTIVE = 'Active';
    public const ACTION_APPROVE_USER = 'UserApprove';
    public const ACTION_BROWSE = 'Browse';
    public const ACTION_CHANGE_PICTURE = 'Picture';
    public const ACTION_CREATE = 'Create';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_EMAIL = 'Emailer';
    public const ACTION_LANGUAGE = 'Language';
    public const ACTION_LOGIN_AS = 'LoginAs';
    public const ACTION_LOGOUT = 'Logout';
    public const ACTION_REGISTER = 'Register';
    public const ACTION_RESET_PASSWORD = 'ResetPassword';
    public const ACTION_RESET_PASSWORD_MULTI = 'MultiPasswordReset';
    public const ACTION_SETTINGS = 'Settings';
    public const ACTION_UPDATE = 'Updater';
    public const ACTION_USERS_FEED = 'UsersFeed';
    public const ACTION_USER_PICTURE = 'UserPicture';
    public const ACTION_VIEW = 'View';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTIVE = 'active';
    public const PARAM_LANGUAGE = 'language';
    public const PARAM_REFER = 'refer';
    public const PARAM_RESET_KEY = 'key';
    public const PARAM_USER_ID = 'user_id';

    /**
     * @param class-string<\Chamilo\Libraries\Mail\Mailer\MailerInterface> $className
     */
    protected function getActiveMailer(string $className = 'Chamilo\Libraries\Mail\Mailer\ActiveMailer'
    ): MailerInterface
    {
        return $this->getService($className);
    }

    protected function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->getService(AuthenticationValidator::class);
    }

    protected function getUserUrlGenerator(): UserUrlGenerator
    {
        return $this->getService(UserUrlGenerator::class);
    }
}
