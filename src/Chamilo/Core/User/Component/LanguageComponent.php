<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 */
class LanguageComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        protected readonly LanguageConsulter $languageConsulter, protected readonly bool $userCanChangeLanguage,
        protected readonly UserSettingsService $userSettingsService
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ChangeLanguage');

        if ($this->userCanChangeLanguage) {
            $choice = $this->getRequest()->query->get(self::PARAM_LANGUAGE);
            $languages = array_keys($this->getLanguages());

            if ($choice && in_array($choice, $languages)) {
                $this->userSettingsService->updateUserSetting(
                    $currentUser, 'cosnics.libraries.userInterface.translation.language.default', $choice, $currentUser
                );
            }
        }

        return new RedirectResponse(urldecode($this->getRequest()->query->get(self::PARAM_REFER)));
    }

    /**
     * @return string[]
     */
    private function getLanguages(): array
    {
        return $this->languageConsulter->getLanguages();
    }
}
