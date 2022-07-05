<?php
namespace Chamilo\Core\User\Factory;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\User\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class UserFactory
{

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    private $localSettingUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    private $sessionUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    private $themePathBuilder;

    /**
     *
     * @var \Chamilo\Libraries\Translation\Translation
     */
    private $translationUtilities;

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     * @param \Chamilo\Libraries\Translation\Translation $translationUtilities
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSettingUtilities
     */
    public function __construct(
        SessionUtilities $sessionUtilities, UserService $userService, ConfigurationConsulter $configurationConsulter,
        ThemePathBuilder $themePathBuilder, Translation $translationUtilities, LocalSetting $localSettingUtilities
    )
    {
        $this->sessionUtilities = $sessionUtilities;
        $this->userService = $userService;
        $this->configurationConsulter = $configurationConsulter;
        $this->themePathBuilder = $themePathBuilder;
        $this->translationUtilities = $translationUtilities;
        $this->localSettingUtilities = $localSettingUtilities;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->configurationConsulter;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    public function getLocalSettingUtilities()
    {
        return $this->localSettingUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSettingUtilities
     */
    public function setLocalSettingUtilities(LocalSetting $localSettingUtilities)
    {
        $this->localSettingUtilities = $localSettingUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    public function getSessionUtilities()
    {
        return $this->sessionUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function setSessionUtilities(SessionUtilities $sessionUtilities)
    {
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public function getThemePathBuilder()
    {
        return $this->themePathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     */
    public function setThemePathBuilder(ThemePathBuilder $themePathBuilder)
    {
        $this->themePathBuilder = $themePathBuilder;
    }

    /**
     *
     * @return \Chamilo\Libraries\Translation\Translation
     */
    public function getTranslationUtilities()
    {
        return $this->translationUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Translation\Translation $translationUtilities
     */
    public function setTranslationUtilities(Translation $translationUtilities)
    {
        $this->translationUtilities = $translationUtilities;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        $userIdentifier = $this->getSessionUtilities()->getUserId();

        if ($userIdentifier)
        {
            $user = $this->getUserService()->findUserByIdentifier($userIdentifier);
        }

        if ($user instanceof User)
        {
            $themeSelectionAllowed = $this->getConfigurationConsulter()->getSetting(
                array('Chamilo\Core\User', 'allow_user_theme_selection')
            );

            if ($themeSelectionAllowed)
            {
                $this->getThemePathBuilder()->setTheme($this->getLocalSettingUtilities()->get('theme'));
            }

            $languageSelectionAllowed = $this->getConfigurationConsulter()->getSetting(
                array('Chamilo\Core\User', 'allow_user_change_platform_language')
            );

            if ($languageSelectionAllowed)
            {
                $this->getTranslationUtilities()->setLanguageIsocode(
                    $this->getLocalSettingUtilities()->get('platform_language')
                );
            }
        }

        return $user;
    }

    /**
     *
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }
}

