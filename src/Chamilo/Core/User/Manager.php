<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;

/**
 * @package Chamilo\Core\User
 */
abstract class Manager extends Application
{
    public const string CONTEXT = __NAMESPACE__;
    public const string PARAM_ACTIVE = 'active';
    public const string PARAM_LANGUAGE = 'language';
    public const string PARAM_REFER = 'refer';
    public const string PARAM_RESET_KEY = 'key';
    public const string PARAM_USER_ID = 'user_id';

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
        return ActionEnum::getActionValue(static::class);
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
        return ActionEnum::BROWSE->value;
    }

    protected function getUserUrlGenerator(): UserUrlGenerator
    {
        return $this->getService(UserUrlGenerator::class);
    }
}
