<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Configuration\Storage\DataClass\Language;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigurationCacheService extends DoctrinePhpFileCacheService
{
    const IDENTIFIER_SETTINGS = 'configuration.settings';
    const IDENTIFIER_REGISTRATIONS = 'configuration.registrations';
    const IDENTIFIER_LANGUAGES = 'configuration.languages';

    /**
     *
     * @see \Chamilo\Libraries\Cache\CacheServiceInterface::fillCache()
     */
    public function fillCache()
    {
        foreach ($this->getCacheIdentifiers() as $identifier)
        {
            if (! $this->fillCacheForIdentifier($identifier))
            {
                throw new \Exception('CacheError');
            }
        }

        return true;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\CacheServiceInterface::fillCacheForIdentifier()
     */
    public function fillCacheForIdentifier($identifier)
    {
        switch ($identifier)
        {
            case IDENTIFIER_SETTINGS :
                return $this->fillSettingsCache();
                break;
            case IDENTIFIER_REGISTRATIONS :
                return $this->fillRegistrationsCache();
                break;
            case IDENTIFIER_LANGUAGES :
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
        $settingObjects = DataManager :: retrieves(Setting :: class_name(), new DataClassRetrievesParameters());

        while ($setting = $settingObjects->next_result())
        {
            $settings[$setting->get_application()][$setting->get_variable()] = $setting->get_value();
        }

        return $this->getCacheProvider()->save($this->getCacheIdentifier(), $settings);
    }

    public function fillRegistrationsCache()
    {
        // TODO: Implement Registrations cache filling
    }

    /**
     *
     * @return boolean
     */
    public function fillLanguagesCache()
    {
        $languageObjects = DataManager :: retrieves(Language :: class_name(), new DataClassRetrievesParameters());

        while ($language = $languageObjects->next_result())
        {
            $languages[$language->get_isocode()] = $language->get_original_name();
        }

        return $this->getCacheProvider()->save('configuration.languages', $languages);
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
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCacheIdentifiers()
     */
    public function getCacheIdentifiers()
    {
        return array(self :: IDENTIFIER_SETTINGS, self :: IDENTIFIER_REGISTRATIONS, self :: IDENTIFIER_LANGUAGES);
    }
}