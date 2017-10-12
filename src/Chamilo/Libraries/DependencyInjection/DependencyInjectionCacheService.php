<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;

/**
 * Manages the cache for the symfony dependency injection
 *
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionCacheService extends FileBasedCacheService
{

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $fileConfigurationConsulter;

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $fileConfigurationConsulter
     */
    public function __construct(ConfigurationConsulter $fileConfigurationConsulter)
    {
        $this->fileConfigurationConsulter = $fileConfigurationConsulter;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getFileConfigurationConsulter()
    {
        return $this->fileConfigurationConsulter;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $fileConfigurationConsulter
     */
    public function setFileConfigurationConsulter(ConfigurationConsulter $fileConfigurationConsulter)
    {
        $this->fileConfigurationConsulter = $fileConfigurationConsulter;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::warmUp()
     */
    public function warmUp()
    {
        $dependencyInjectionContainerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $dependencyInjectionContainerBuilder->clearContainerInstance();
        $dependencyInjectionContainerBuilder->createContainer();

        return $this;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::getCachePath()
     */
    function getCachePath()
    {
        $configurablePathBuilder = new ConfigurablePathBuilder(
            $this->getFileConfigurationConsulter()->getSetting(array('Chamilo\Configuration', 'storage')));

        return $configurablePathBuilder->getCachePath(__NAMESPACE__);
    }
}