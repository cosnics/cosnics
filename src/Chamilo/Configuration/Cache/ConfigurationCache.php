<?php
namespace Chamilo\Configuration\Cache;

use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Configuration\Service\ConfigurationLoader;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigurationCache extends DoctrinePhpFileCacheService
{
    // Identifiers
    const IDENTIFIER_SETTINGS = 'settings';
    const IDENTIFIER_REGISTRATIONS = 'registrations';
    const IDENTIFIER_LANGUAGES = 'languages';

    // Registration cache types
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;
    const REGISTRATION_INTEGRATION = 3;

    /**
     *
     * @var ConfigurationLoader
     */
    private $configurationLoader;

    /**
     *
     * @param ConfigurationLoader $configurationLoader
     */
    public function __construct(ConfigurationLoader $configurationLoader)
    {
        $this->configurationLoader = $configurationLoader;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationLoader
     */
    protected function getConfigurationLoader()
    {
        return $this->configurationLoader;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationLoader $configurationLoader
     */
    protected function setConfigurationLoader(ConfigurationLoader $configurationLoader)
    {
        $this->configurationLoader = $configurationLoader;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        switch ($identifier)
        {
            case self::IDENTIFIER_SETTINGS :
                return $this->fillSettingsCache();
                break;
            case self::IDENTIFIER_REGISTRATIONS :
                return $this->fillRegistrationsCache();
                break;
            case self::IDENTIFIER_LANGUAGES :
                return $this->fillLanguagesCache();
                break;
        }
    }

    /**
     *
     * @return boolean
     */
    public function fillSettingsCache()
    {
        $settings = $this->getConfigurationLoader()->getSettings();
        return $this->getCacheProvider()->save(self::IDENTIFIER_SETTINGS, $settings);
    }

    /**
     *
     * @return boolean
     */
    public function fillRegistrationsCache()
    {
        $registrations = $this->getConfigurationLoader()->getRegistrations();
        return $this->getCacheProvider()->save(self::IDENTIFIER_REGISTRATIONS, $registrations);
    }

    /**
     *
     * @return boolean
     */
    public function fillLanguagesCache()
    {
        $languages = $this->getConfigurationLoader()->getLanguages();
        return $this->getCacheProvider()->save(self::IDENTIFIER_LANGUAGES, $languages);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Configuration';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array(self::IDENTIFIER_SETTINGS, self::IDENTIFIER_REGISTRATIONS, self::IDENTIFIER_LANGUAGES);
    }

    /**
     *
     * @return string[]
     */
    public function getSettingsCache()
    {
        return $this->getForIdentifier(self::IDENTIFIER_SETTINGS);
    }

    /**
     *
     * @return string[]
     */
    public function getRegistrationsCache()
    {
        return $this->getForIdentifier(self::IDENTIFIER_REGISTRATIONS);
    }

    /**
     *
     * @return string[]
     */
    public function getLanguagesCache()
    {
        return $this->getForIdentifier(self::IDENTIFIER_LANGUAGES);
    }
}