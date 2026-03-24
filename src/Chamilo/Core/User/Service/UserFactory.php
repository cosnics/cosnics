<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class UserFactory
{
    protected bool $canChangeLanguage;

    protected bool $canChangeTimezone;

    protected UserSettingsService $userSettingsService;

    private SessionInterface $session;

    private ThemePathBuilder $themeSystemPathBuilder;

    private ThemePathBuilder $themeWebPathBuilder;

    private Translator $translator;

    private UserService $userService;

    public function __construct(
        SessionInterface $session, UserService $userService, ThemePathBuilder $themeWebPathBuilder,
        ThemePathBuilder $themeSystemPathBuilder, Translator $translator, UserSettingsService $userSettingsService,
        bool $canChangeLanguage = true, bool $canChangeTimezone = true
    )
    {
        $this->session = $session;
        $this->userService = $userService;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
        $this->themeSystemPathBuilder = $themeSystemPathBuilder;
        $this->translator = $translator;
        $this->userSettingsService = $userSettingsService;
        $this->canChangeLanguage = $canChangeLanguage;
        $this->canChangeTimezone = $canChangeTimezone;
    }

    public function canChangeLanguage(): bool
    {
        return $this->canChangeLanguage;
    }

    public function canChangeTimezone(): bool
    {
        return $this->canChangeTimezone;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getThemeSystemPathBuilder(): ThemePathBuilder
    {
        return $this->themeSystemPathBuilder;
    }

    public function getThemeWebPathBuilder(): ThemePathBuilder
    {
        return $this->themeWebPathBuilder;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUser(): ?User
    {
        $userIdentifier = $this->getSession()->get(AuthenticationValidator::SESSION_USER_ID);

        if ($userIdentifier) {
            try {
                $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

                if ($user instanceof User) {
                    if ($this->canChangeLanguage()) {
                        $userLanguage = $this->getUserSettingsService()->findUserSetting(
                            $user, 'cosnics.libraries.userInterface.translation.language.default'
                        );

                        if ($userLanguage) {
                            $this->getTranslator()->setLocale($userLanguage);
                        }
                    }

                    if ($this->canChangeTimezone()) {
                        $userTimezone = $this->getUserSettingsService()->findUserSetting(
                            $user, 'cosnics.libraries.calendar.timezone'
                        );

                        if ($userTimezone) {
                            date_default_timezone_set($userTimezone);
                        }
                    }
                }

                return $user;
            }
            catch (Throwable) {
                return null;
            }
        }

        return null;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getUserSettingsService(): UserSettingsService
    {
        return $this->userSettingsService;
    }
}

