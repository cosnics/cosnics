<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Cache\ConfigurationCache;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class OldConfigurationService
{

    /**
     *
     * @var string[]
     */
    private $settings;

    /**
     *
     * @var string[]
     */
    private $registrations;

    /**
     *
     * @var string[]
     */
    private $languages;

    /**
     *
     * @var \Chamilo\Configuration\Cache\ConfigurationCache
     */
    private $configurationCache;

    /**
     *
     * @param \Chamilo\Configuration\Cache\ConfigurationCache $configurationCache
     */
    public function __construct(ConfigurationCache $configurationCache)
    {
        $this->configurationCache = $configurationCache;
    }

    /**
     *
     * @return \Chamilo\Configuration\Cache\ConfigurationCache
     */
    protected function getConfigurationCache()
    {
        return $this->configurationCache;
    }

    /**
     *
     * @param \Chamilo\Configuration\Cache\ConfigurationCache $configurationCache
     */
    protected function setConfigurationCache(ConfigurationCache $configurationCache)
    {
        $this->configurationCache = $configurationCache;
    }

    /**
     *
     * @return string[]
     */
    protected function getRegistrations()
    {
        if (! isset($this->registrations))
        {
            $this->registrations = $this->getConfigurationCache()->getRegistrationsCache();
        }

        return $this->registrations;
    }

    /**
     *
     * @return string[]
     */
    public function getSettings()
    {
        if (! isset($this->settings))
        {
            $this->settings = $this->getConfigurationCache()->getSettingsCache();
        }

        return $this->settings;
    }

    /**
     *
     * @return string[]
     */
    public function getLanguages()
    {
        if (! isset($this->languages))
        {
            $this->languages = $this->getConfigurationCache()->getLanguagesCache();
        }

        return $this->languages;
    }

    /**
     * Gets a parameter from the configuration.
     *
     * @param string[] $keys
     * @throws \Exception
     * @return string
     */
    /**
     *
     * @param string[] $keys
     * @return string
     */
    public function getSetting($keys)
    {
        $variables = $keys;
        $values = $this->getSettings();

        while (count($variables) > 0)
        {
            $key = array_shift($variables);

            if (! isset($values[$key]))
            {
                if ($this->getConfigurationCache()->isAvailable())
                {
                    return null;
                }
                else
                {
                    echo 'The requested variable is not available in an unconfigured environment (' .
                         implode(' > ', $keys) . ')';
                    exit();
                }
            }
            else
            {
                $values = $values[$key];
            }
        }

        return $values;
    }

    /**
     *
     * @param string[] $keys
     * @param string $value
     */
    protected function setSetting($keys, $value)
    {
        $variables = $keys;
        $values = &$this->getSettings();

        while (count($variables) > 0)
        {
            $key = array_shift($variables);

            if (! isset($values[$key]))
            {
                $values[$key] = null;
                $values = &$values[$key];
            }
            else
            {
                $values = &$values[$key];
            }
        }

        $values = $value;
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function hasSettingsForContext($context)
    {
        $settings = $this->getSettings();
        return isset($settings[$context]);
    }

    /**
     *
     * @param string $context
     * @return Registration
     */
    public function getRegistrationForContext($context)
    {
        $registrations = $this->getRegistrations();
        return $registrations[ConfigurationLoader::REGISTRATION_CONTEXT][$context];
    }

    /**
     *
     * @return string[]
     */
    public function getRegistrationContexts()
    {
        $registrations = $this->getRegistrations();
        return array_keys($registrations[ConfigurationLoader::REGISTRATION_CONTEXT]);
    }

    /**
     *
     * @param string $type
     * @return \configuration\Registration[]
     */
    public function getRegistrationsByType($type)
    {
        $registrations = $this->getRegistrations();
        return $registrations[ConfigurationLoader::REGISTRATION_TYPE][$type];
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function isContextRegistered($context)
    {
        $registration = $this->getRegistration($context);
        return ! empty($registration);
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function isContextRegisteredAndActive($context)
    {
        $registration = $this->getRegistration($context);
        return $this->isRegistered($context) &&
             $registration[Registration::PROPERTY_STATUS] == Registration::STATUS_ACTIVE;
    }

    /**
     *
     * @param string $integration
     * @param string $root
     * @return \Chamilo\Configuration\Storage\DataClass\Registration[]
     */
    public function getIntegrationRegistrations($integration, $root = null)
    {
        $registrations = $this->getRegistrations();
        $integrationRegistrations = $registrations[ConfigurationLoader::REGISTRATION_INTEGRATION][$integration];

        if ($root)
        {
            $rootIntegrationRegistrations = array();

            foreach ($integrationRegistrations as $rootContext => $registration)
            {
                $rootContextStringUtilities = $this->getStringUtilities()->createString($rootContext);

                if ($rootContextStringUtilities->startsWith($root))
                {
                    $rootIntegrationRegistrations[$rootContext] = $registration;
                }
            }

            return $rootIntegrationRegistrations;
        }
        else
        {
            return $integrationRegistrations;
        }
    }

    public function getLanguageNameFromIsocode($isocode)
    {
        $languages = $this->getLanguages();
        return $languages[$isocode];
    }
}
