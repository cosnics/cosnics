<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Superseded by the DataCacheLoader
 */
class ConfigurationCacheService extends DoctrinePhpFileCacheService
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
     * @return string
     */
    public function getConfigurationFilePath()
    {
        return \Chamilo\Libraries\File\Path::getInstance()->getStoragePath() . 'configuration/configuration.ini';
    }

    /**
     *
     * @return boolean
     */
    public function fillSettingsCache()
    {
        $settings = $this->getConfigurationFileSettings();

        $settingObjects = DataManager::records(Setting::class_name(), new RecordRetrievesParameters());

        while ($setting = $settingObjects->next_result())
        {
            $settings[$setting[Setting::PROPERTY_APPLICATION]][$setting[Setting::PROPERTY_VARIABLE]] = $setting[Setting::PROPERTY_VALUE];
        }

        return $this->getCacheProvider()->save(self::IDENTIFIER_SETTINGS, $settings);
    }

    /**
     *
     * @return string[]
     */
    public function getConfigurationFileSettings()
    {
        return array($this->getCachePathNamespace() => parse_ini_file($this->getConfigurationFilePath(), true));
    }

    /**
     *
     * @return boolean
     */
    public function fillRegistrationsCache()
    {
        $registrations = array();
        $registrationsObjects = DataManager::records(Registration::class_name(), new RecordRetrievesParameters());

        while ($registration = $registrationsObjects->next_result())
        {
            $registrations[self::REGISTRATION_TYPE][$registration[Registration::PROPERTY_TYPE]][$registration[Registration::PROPERTY_CONTEXT]] = $registration;
            $registrations[self::REGISTRATION_CONTEXT][$registration[Registration::PROPERTY_CONTEXT]] = $registration;

            $contextStringUtilities = StringUtilities::getInstance()->createString(
                $registration[Registration::PROPERTY_CONTEXT]);
            $isIntegration = $contextStringUtilities->contains('\Integration\\');

            if ($isIntegration)
            {
                /**
                 * Take last occurrence of integration instead of first
                 */
                $lastIntegrationIndex = $contextStringUtilities->indexOfLast('\Integration\\');

                $integrationContext = $contextStringUtilities->substr($lastIntegrationIndex + 13)->__toString();
                $rootContext = $contextStringUtilities->substr(0, $lastIntegrationIndex)->__toString();

                $registrations[self::REGISTRATION_INTEGRATION][$integrationContext][$rootContext] = $registration;
            }
        }

        return $this->getCacheProvider()->save(self::IDENTIFIER_REGISTRATIONS, $registrations);
    }

    /**
     *
     * @return boolean
     */
    public function fillLanguagesCache()
    {
        $languages = array();
        $languageObjects = DataManager::records(Language::class_name(), new RecordRetrievesParameters());

        while ($language = $languageObjects->next_result())
        {
            $languages[$language[Language::PROPERTY_ISOCODE]] = $language[Language::PROPERTY_ORIGINAL_NAME];
        }

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