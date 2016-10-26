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
class ConfigurationLoader
{
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;
    const REGISTRATION_INTEGRATION = 3;

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

    public function getRegistrations()
    {
        $baseConfigurationService = $this->getBaseConfigurationService();

        if ($baseConfigurationService->isAvailable())
        {
            $registrationRecords = $this->getConfigurationRepository()->findRegistrations();
            $registrations = array();

            foreach ($registrationRecords as $registrationRecord)
            {
                $registrations[self::REGISTRATION_TYPE][$registrationRecord[Registration::PROPERTY_TYPE]][$registrationRecord[Registration::PROPERTY_CONTEXT]] = $registrationRecord;
                $registrations[self::REGISTRATION_CONTEXT][$registrationRecord[Registration::PROPERTY_CONTEXT]] = $registrationRecord;

                $contextStringUtilities = $this->getStringUtilities()->createString(
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

                    $registrations[self::REGISTRATION_INTEGRATION][$integrationContext][$rootContext] = $registrationRecord;
                }
            }
        }

        return $registrations;
    }

    /**
     *
     * @return string[]
     */
    public function getSettings()
    {
        $settings = $this->getBaseConfigurationService()->getSettings();
        $settingRecords = $this->getConfigurationRepository()->findSettings();

        foreach ($settingRecords as $settingRecord)
        {
            $settings[$settingRecord[Setting::PROPERTY_CONTEXT]][$settingRecord[Setting::PROPERTY_VARIABLE]] = $settingRecord[Setting::PROPERTY_VALUE];
        }

        return $settings;
    }

    public function getLanguages()
    {
        $languages = array();
        $languageRecords = $this->getConfigurationRepository()->findLanguages();

        foreach ($languageRecords as $languageRecord)
        {
            $languages[$languageRecord[Language::PROPERTY_ISOCODE]] = $languageRecord[Language::PROPERTY_ORIGINAL_NAME];
        }

        return $languages;
    }
}
