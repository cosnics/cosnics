<?php
namespace Chamilo\Core\User;

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
    public const DEFAULT_ACTION = self::ACTION_BROWSE;
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

    protected function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->getService(AuthenticationValidator::class);
    }

    public function getContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultAction(): string
    {
        return self::DEFAULT_ACTION;
    }

    protected function getUserUrlGenerator(): UserUrlGenerator
    {
        return $this->getService(UserUrlGenerator::class);
    }
}
