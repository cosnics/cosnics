<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Configuration\Configuration;
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
     * @var \Chamilo\Configuration\Configuration
     */
    protected $configuration;

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @return \Chamilo\Configuration\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function getDataSourceName()
    {
        $configuration = $this->getConfiguration();

        return new DataSourceName(
            $configuration->get_setting(array('Chamilo\Configuration', 'database', 'driver')),
            $configuration->get_setting(array('Chamilo\Configuration', 'database', 'username')),
            $configuration->get_setting(array('Chamilo\Configuration', 'database', 'host')),
            $configuration->get_setting(array('Chamilo\Configuration', 'database', 'name')),
            $configuration->get_setting(array('Chamilo\Configuration', 'database', 'password')));
    }
}
