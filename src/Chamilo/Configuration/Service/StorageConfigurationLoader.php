<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Repository\ConfigurationRepository;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class StorageConfigurationLoader implements CacheableDataLoaderInterface
{

    /**
     *
     * @var \Chamilo\Configuration\Repository\ConfigurationRepository
     */
    private $configurationRepository;

    /**
     *
     * @param \Chamilo\Configuration\Repository\ConfigurationRepository $configurationRepository
     */
    public function __construct(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
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
     * @return string[]
     */
    public function getData()
    {
        $settings = array();
        $settingRecords = $this->getConfigurationRepository()->findSettingsAsRecords();

        foreach ($settingRecords as $settingRecord)
        {
            $settings[$settingRecord[Setting::PROPERTY_CONTEXT]][$settingRecord[Setting::PROPERTY_VARIABLE]] = $settingRecord[Setting::PROPERTY_VALUE];
        }

        return $settings;
    }

    /**
     *
     * @return string
     */
    public function getIdentifier()
    {
        return md5(__CLASS__);
    }
}
