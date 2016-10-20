<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Repository\ConfigurationRepository;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationService
{

    /**
     *
     * @var \Chamilo\Configuration\Repository\ConfigurationRepository
     */
    private $configurationRepository;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities;
     * @param \Chamilo\Configuration\Repository\ConfigurationRepository $configurationRepository
     */
    public function __construct(StringUtilities $stringUtilities, ConfigurationRepository $configurationRepository)
    {
        $this->stringUtilities = $stringUtilities;
        $this->configurationRepository = $configurationRepository;
        $this->configurationRepository->loadConfiguration();
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @return \Chamilo\Configuration\Repository\ConfigurationRepository
     */
    public function getConfigurationRepository()
    {
        return $this->configurationRepository;
    }

    /**
     *
     * @param \Chamilo\Configuration\Repository\ConfigurationRepository $configurationRepository
     */
    public function setConfigurationRepository($configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    public function getSettings()
    {
        return $this->getConfigurationRepository()->getSettings();
    }

    /**
     * Gets a parameter from the configuration.
     *
     * @param string[] $keys
     * @throws \Exception
     * @return mixed
     */
    public function getSetting($keys)
    {
        return $this->getConfigurationRepository()->getSetting($keys);
    }

    /**
     *
     * @param string[] $keys
     * @param mixed $value
     */
    public function setSetting($keys, $value)
    {
        $this->getConfigurationRepository()->setSetting($keys, $value);
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
     * @throws \Exception
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->getConfigurationRepository()->isAvailable();
    }

    public function isConnectable()
    {
        return $this->getConfigurationRepository()->isConnectable();
    }

    /**
     *
     * @return Registration[]
     */
    public function getRegistrations()
    {
        return $this->getConfigurationRepository()->getRegistrations();
    }

    /**
     *
     * @param string $context
     * @return Registration
     */
    public function getRegistrationForContext($context)
    {
        $registrations = $this->getRegistrations();
        return $registrations[ConfigurationRepository::REGISTRATION_CONTEXT][$context];
    }

    /**
     *
     * @return string[]
     */
    public function getRegistrationContexts()
    {
        $registrations = $this->getRegistrations();
        return array_keys($registrations[ConfigurationRepository::REGISTRATION_CONTEXT]);
    }

    /**
     *
     * @param string $type
     * @return \configuration\Registration[]
     */
    public function getRegistrationsByType($type)
    {
        $registrations = $this->getRegistrations();
        return $registrations[self::REGISTRATION_TYPE][$type];
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
        $integrationRegistrations = $registrations[self::REGISTRATION_INTEGRATION][$integration];

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

    public function getLanguages()
    {
        return $this->getConfigurationRepository()->getLanguages();
    }

    public function getLanguageNameFromIsocode($isocode)
    {
        $languages = $this->getLanguages();
        return $languages[$isocode];
    }

    /**
     * Trigger a reset of the entire configuration to force a reload from storage
     */
    public function reset()
    {
        return $this->getConfigurationRepository()->reset();
    }
}
