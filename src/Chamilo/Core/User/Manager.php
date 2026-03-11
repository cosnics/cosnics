<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\Translation\Translator;

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

    protected MailerInterface $activeMailer;

    protected AlertsManager $alertsManager;

    protected AuthenticationValidator $authenticationValidator;

    protected UserUrlGenerator $userUrlGenerator;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator);

        $this->authenticationValidator = $authenticationValidator;
        $this->userUrlGenerator = $userUrlGenerator;
        $this->activeMailer = $activeMailer;
        $this->alertsManager = $alertsManager;
    }

    protected function getActiveMailer(): MailerInterface
    {
        return $this->activeMailer;
    }

    public function getAlertsManager(): AlertsManager
    {
        return $this->alertsManager;
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
        return $this->authenticationValidator;
    }

    public function getDefaultApplicationAction(): string
    {
        return ActionEnum::BROWSE->value;
    }

    protected function getUserUrlGenerator(): UserUrlGenerator
    {
        return $this->userUrlGenerator;
    }
}
