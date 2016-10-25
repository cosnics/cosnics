<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataSourceNameFactory
{

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationService
     */
    protected $configurationService;

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationService
     */
    public function getConfigurationService()
    {
        return $this->configurationService;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationService $configurationService
     */
    public function setConfigurationService(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    public function getDataSourceName()
    {
        $configurationService = $this->getConfigurationService();

        return new DataSourceName(
            $configurationService->getSetting(array('Chamilo\Configuration', 'database', 'driver')),
            $configurationService->getSetting(array('Chamilo\Configuration', 'database', 'username')),
            $configurationService->getSetting(array('Chamilo\Configuration', 'database', 'host')),
            $configurationService->getSetting(array('Chamilo\Configuration', 'database', 'name')),
            $configurationService->getSetting(array('Chamilo\Configuration', 'database', 'password')));
    }
}
