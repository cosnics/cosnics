<?php
namespace Chamilo\Core\User\Factory;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class UserFactory
{

    private ConfigurationConsulter $configurationConsulter;

    private SessionUtilities $sessionUtilities;

    private ThemePathBuilder $themeSystemPathBuilder;

    private ThemePathBuilder $themeWebPathBuilder;

    private Translator $translator;

    private UserService $userService;

    private UserSettingService $userSettingService;

    public function __construct(
        SessionUtilities $sessionUtilities, UserService $userService, ConfigurationConsulter $configurationConsulter,
        ThemePathBuilder $themeWebPathBuilder, ThemePathBuilder $themeSystemPathBuilder, Translator $translator,
        UserSettingService $userSettingService
    )
    {
        $this->sessionUtilities = $sessionUtilities;
        $this->userService = $userService;
        $this->configurationConsulter = $configurationConsulter;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
        $this->themeSystemPathBuilder = $themeSystemPathBuilder;
        $this->translator = $translator;
        $this->userSettingService = $userSettingService;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
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
        $userIdentifier = $this->getSessionUtilities()->getUserId();

        if ($userIdentifier)
        {
            $user = $this->getUserService()->findUserByIdentifier((string) $userIdentifier);

            if ($user instanceof User)
            {
                $themeSelectionAllowed = $this->getConfigurationConsulter()->getSetting(
                    ['Chamilo\Core\User', 'allow_user_theme_selection']
                );

                if ($themeSelectionAllowed)
                {
                    $theme = $this->getUserSettingService()->getSettingForUser($user, 'Chamilo\Core\Admin', 'theme');

                    $this->getThemeSystemPathBuilder()->setTheme($theme);
                    $this->getThemeWebPathBuilder()->setTheme($theme);
                }

                $languageSelectionAllowed = $this->getConfigurationConsulter()->getSetting(
                    ['Chamilo\Core\User', 'allow_user_change_platform_language']
                );

                if ($languageSelectionAllowed)
                {
                    $this->getTranslator()->setLocale(
                        $this->getUserSettingService()->getSettingForUser(
                            $user, 'Chamilo\Core\Admin', 'platform_language'
                        )
                    );
                }
            }

            return $user;
        }

        return null;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }
}

