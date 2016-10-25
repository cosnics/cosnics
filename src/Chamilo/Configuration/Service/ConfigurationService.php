<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Repository\ConfigurationRepository;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\DataClass\Language;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationService
{
    // Registration cache types
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;
    const REGISTRATION_INTEGRATION = 3;

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
     * @var BaseConfigurationService
     */
    private $baseConfigurationService;

    /**
     *
     * @var \Chamilo\Libraries\File\Path
     */
    private $pathUtilities;

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
     * @param BaseConfigurationService $baseConfigurationService
     * @param \Chamilo\Libraries\File\Path $pathUtilities
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities;
     * @param \Chamilo\Configuration\Repository\ConfigurationRepository $configurationRepository
     */
    public function __construct(BaseConfigurationService $baseConfigurationService, Path $pathUtilities,
        StringUtilities $stringUtilities, ConfigurationRepository $configurationRepository)
    {
        $this->baseConfigurationService = $baseConfigurationService;
        $this->pathUtilities = $pathUtilities;
        $this->stringUtilities = $stringUtilities;
        $this->configurationRepository = $configurationRepository;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\BaseConfigurationService
     */
    protected function getBaseConfigurationService()
    {
        return $this->baseConfigurationService;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\BaseConfigurationService $baseConfigurationService
     */
    protected function setBaseConfigurationService(BaseConfigurationService $baseConfigurationService)
    {
        $this->baseConfigurationService = $baseConfigurationService;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\Path
     */
    public function getPathUtilities()
    {
        return $this->pathUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\Path $pathUtilities
     */
    public function setPathUtilities(Path $pathUtilities)
    {
        $this->pathUtilities = $pathUtilities;
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

    /**
     *
     * @return boolean
     */
    public function isAvailable()
    {
        $baseConfigurationService = $this->getBaseConfigurationService();

        $driver = $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'driver'));
        $userName = $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'username'));
        $host = $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'host'));
        $name = $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'name'));
        $password = $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'password'));

        return $this->getConfigurationRepository()->isAvailable($driver, $userName, $host, $name, $password);
    }

    protected function getRegistrations()
    {
        if (! isset($this->registrations))
        {
            $baseConfigurationService = $this->getBaseConfigurationService();

            if ($baseConfigurationService->isAvailable())
            {
                $registrationRecords = $this->getConfigurationRepository()->findRegistrations();
                $this->registrations = array();

                foreach ($registrationRecords as $registrationRecord)
                {
                    $this->registrations[self::REGISTRATION_TYPE][$registrationRecord[Registration::PROPERTY_TYPE]][$registrationRecord[Registration::PROPERTY_CONTEXT]] = $registrationRecord;
                    $this->registrations[self::REGISTRATION_CONTEXT][$registrationRecord[Registration::PROPERTY_CONTEXT]] = $registrationRecord;

                    $contextStringUtilities = StringUtilities::getInstance()->createString(
                        $registrationRecord[Registration::PROPERTY_CONTEXT]);
                    $isIntegration = $contextStringUtilities->contains('\Integration\\');

                    if ($isIntegration)
                    {
                        /**
                         * Take last occurrence of integration instead of first
                         */
                        $lastIntegrationIndex = $contextStringUtilities->indexOfLast('\Integration\\');

                        $integrationContext = $contextStringUtilities->substr($lastIntegrationIndex + 13)->__toString();
                        $rootContext = $contextStringUtilities->substr(0, $lastIntegrationIndex)->__toString();

                        $this->registrations[self::REGISTRATION_INTEGRATION][$integrationContext][$rootContext] = $registrationRecord;
                    }
                }
            }
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
            $this->settings = $this->getBaseConfigurationService()->getSettings();
            $settingRecords = $this->getConfigurationRepository()->findSettings();

            foreach ($settingRecords as $settingRecord)
            {
                $this->settings[$settingRecord[Setting::PROPERTY_APPLICATION]][$settingRecord[Setting::PROPERTY_VARIABLE]] = $settingRecord[Setting::PROPERTY_VALUE];
            }
        }

        return $this->settings;
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
                if ($this->isAvailable())
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
        if (! isset($this->languages))
        {
            $languageRecords = $this->getConfigurationRepository()->findLanguages();

            foreach ($languageRecords as $languageRecord)
            {
                $this->languages[$languageRecord[Language::PROPERTY_ISOCODE]] = $languageRecord[Language::PROPERTY_ORIGINAL_NAME];
            }
        }

        return $this->languages;
    }

    public function getLanguageNameFromIsocode($isocode)
    {
        $languages = $this->getLanguages();
        return $languages[$isocode];
    }
}
