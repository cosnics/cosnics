<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
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

    private ConfigurationConsulter $configurationConsulter;

    private SessionInterface $session;

    private ThemePathBuilder $themeSystemPathBuilder;

    private ThemePathBuilder $themeWebPathBuilder;

    private Translator $translator;

    private UserService $userService;

    public function __construct(
        SessionInterface $session, UserService $userService, ConfigurationConsulter $configurationConsulter,
        ThemePathBuilder $themeWebPathBuilder, ThemePathBuilder $themeSystemPathBuilder, Translator $translator
    )
    {
        $this->session = $session;
        $this->userService = $userService;
        $this->configurationConsulter = $configurationConsulter;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
        $this->themeSystemPathBuilder = $themeSystemPathBuilder;
        $this->translator = $translator;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
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

        if ($userIdentifier)
        {
            try
            {
                $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

                if ($user instanceof User)
                {
                    $languageSelectionAllowed = $this->getConfigurationConsulter()->getSetting(
                        ['Chamilo\Core\User', 'allow_user_change_platform_language']
                    );

                    if ($languageSelectionAllowed)
                    {
                        $this->getTranslator()->setLocale(
                            $this->getUserService()->findUserSetting(
                                $user, 'Chamilo\Core\Admin', 'PlatformLanguage'
                            )
                        );

                        date_default_timezone_set(
                            $this->getUserService()->findUserSetting(
                                $user, 'Chamilo\Core\Admin', 'PlatformTimezone'
                            )
                        );
                    }
                }

                return $user;
            }
            catch (Throwable)
            {
                return null;
            }
        }

        return null;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}

