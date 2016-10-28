<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataSourceNameFactoryInterface;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataSourceNameFactory implements DataSourceNameFactoryInterface
{

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->configurationConsulter;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    public function getDataSourceName()
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        return new DataSourceName(
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'database', 'driver')),
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'database', 'username')),
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'database', 'host')),
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'database', 'name')),
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'database', 'password')));
    }
}
