<?php
namespace Chamilo\Core\User\Factory;

use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Core\User\Storage\DataClass\User;

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
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    private $sessionUtilities;

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme
     */
    private $themeUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Translation
     */
    private $translationUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    private $localSettingUtilities;

    /**
     *
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     * @param \Chamilo\Libraries\Platform\Translation $translationUtilities
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSettingUtilities
     */
    public function __construct(SessionUtilities $sessionUtilities, UserService $userService, 
        ConfigurationConsulter $configurationConsulter, Theme $themeUtilities, Translation $translationUtilities, 
        LocalSetting $localSettingUtilities)
    {
        $this->sessionUtilities = $sessionUtilities;
        $this->userService = $userService;
        $this->configurationConsulter = $configurationConsulter;
        $this->themeUtilities = $themeUtilities;
        $this->translationUtilities = $translationUtilities;
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
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities()
    {
        return $this->themeUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function setThemeUtilities(Theme $themeUtilities)
    {
        $this->themeUtilities = $themeUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    public function getTranslationUtilities()
    {
        return $this->translationUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\Translation $translationUtilities
     */
    public function setTranslationUtilities(Translation $translationUtilities)
    {
        $this->translationUtilities = $translationUtilities;
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
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        $userIdentifier = $this->getSessionUtilities()->get_user_id();
        
        if ($userIdentifier)
        {
            $user = $this->getUserService()->findUserByIdentifier($userIdentifier);
        }
        
        if ($user instanceof User)
        {
            $themeSelectionAllowed = $this->getConfigurationConsulter()->getSetting(
                array('Chamilo\Core\User', 'allow_user_theme_selection'));
            
            if ($themeSelectionAllowed)
            {
                $this->getThemeUtilities()->setTheme($this->getLocalSettingUtilities()->get('theme'));
            }
            
            $languageSelectionAllowed = $this->getConfigurationConsulter()->getSetting(
                array('Chamilo\Core\User', 'allow_user_change_platform_language'));
            
            if ($languageSelectionAllowed)
            {
                $this->getTranslationUtilities()->setLanguageIsocode(
                    $this->getLocalSettingUtilities()->get('platform_language'));
            }
        }
        
        return $user;
    }
}

