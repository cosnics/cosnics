<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Factory;

use Chamilo\Configuration\Service\BaseConfigurationService;
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
     * @var \Chamilo\Configuration\Service\BaseConfigurationService
     */
    protected $baseConfigurationService;

    /**
     *
     * @param \Chamilo\Configuration\Service\BaseConfigurationService $baseConfigurationService
     */
    public function __construct(BaseConfigurationService $baseConfigurationService)
    {
        $this->baseConfigurationService = $baseConfigurationService;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\BaseConfigurationService
     */
    public function getBaseConfigurationService()
    {
        return $this->baseConfigurationService;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\BaseConfigurationService $baseConfigurationService
     */
    public function setBaseConfigurationService(BaseConfigurationService $baseConfigurationService)
    {
        $this->baseConfigurationService = $baseConfigurationService;
    }

    public function getDataSourceName()
    {
        $baseConfigurationService = $this->getBaseConfigurationService();

        return new DataSourceName(
            $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'driver')),
            $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'username')),
            $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'host')),
            $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'name')),
            $baseConfigurationService->getSetting(array('Chamilo\Configuration', 'database', 'password')));
    }
}
