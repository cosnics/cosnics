<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Storage\Repository\ConfigurationRepository;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationService
{

    /**
     *
     * @var \Chamilo\Configuration\Storage\Repository\ConfigurationRepository
     */
    private $configurationRepository;

    /**
     *
     * @param \Chamilo\Configuration\Storage\Repository\ConfigurationRepository $configurationRepository
     */
    public function __construct(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    /**
     *
     * @return \Chamilo\Configuration\Storage\Repository\ConfigurationRepository
     */
    protected function getConfigurationRepository()
    {
        return $this->configurationRepository;
    }

    /**
     *
     * @param \Chamilo\Configuration\Storage\Repository\ConfigurationRepository $configurationRepository
     */
    protected function setConfigurationRepository(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }
}