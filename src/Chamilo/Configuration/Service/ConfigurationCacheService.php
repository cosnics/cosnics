<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
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
    const IDENTIFIER_LANGUAGES = 'languages';
    const IDENTIFIER_REGISTRATIONS = 'registrations';
    const IDENTIFIER_SETTINGS = 'settings';

    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_INTEGRATION = 3;
    const REGISTRATION_TYPE = 2;

    /**
     *
     * @return boolean
     */
    public function fillLanguagesCache()
    {
        $languages = [];
        $languageObjects = DataManager::records(
            Language::class, new RecordRetrievesParameters(
                new DataClassProperties(array(new PropertiesConditionVariable(Language::class)))
            )
        );

        foreach($languageObjects as $language)
        {
            $languages[$language[Language::PROPERTY_ISOCODE]] = $language[Language::PROPERTY_ORIGINAL_NAME];
        }

        return $this->getCacheProvider()->save(self::IDENTIFIER_LANGUAGES, $languages);
    }

    /**
     *
     * @return boolean
     */
    public function fillRegistrationsCache()
    {
        $registrations = [];
        $registrationsObjects = DataManager::records(
            Registration::class, new RecordRetrievesParameters(
                new DataClassProperties(array(new PropertiesConditionVariable(Registration::class)))
            )
        );

        foreach($registrationsObjects as $registration)
        {
            $registrations[self::REGISTRATION_TYPE][$registration[Registration::PROPERTY_TYPE]][$registration[Registration::PROPERTY_CONTEXT]] =
                $registration;
            $registrations[self::REGISTRATION_CONTEXT][$registration[Registration::PROPERTY_CONTEXT]] = $registration;

            $contextStringUtilities = StringUtilities::getInstance()->createString(
                $registration[Registration::PROPERTY_CONTEXT]
            );
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
    public function fillSettingsCache()
    {
        $settings = $this->getConfigurationFileSettings();

        $settingObjects = DataManager::records(
            Setting::class, new RecordRetrievesParameters(
                new DataClassProperties(array(new PropertiesConditionVariable(Setting::class)))
            )
        );

        foreach($settingObjects as $setting)
        {
            $settings[$setting[Setting::PROPERTY_APPLICATION]][$setting[Setting::PROPERTY_VARIABLE]] =
                $setting[Setting::PROPERTY_VALUE];
        }

        return $this->getCacheProvider()->save(self::IDENTIFIER_SETTINGS, $settings);
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
     * @return string
     */
    public function getConfigurationFilePath()
    {
        return Path::getInstance()->getStoragePath() . 'configuration/configuration.ini';
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
    public function getLanguagesCache()
    {
        return $this->getForIdentifier(self::IDENTIFIER_LANGUAGES);
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
    public function getSettingsCache()
    {
        return $this->getForIdentifier(self::IDENTIFIER_SETTINGS);
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
}