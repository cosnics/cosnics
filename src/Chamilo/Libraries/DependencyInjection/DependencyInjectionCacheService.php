<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;

/**
 * Manages the cache for the symfony dependency injection
 *
 * @package Chamilo\Libraries\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionCacheService extends FileBasedCacheService
{

    protected ConfigurationConsulter $fileConfigurationConsulter;

    public function __construct(
        ConfigurationConsulter $fileConfigurationConsulter, ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        parent::__construct($configurablePathBuilder);

        $this->fileConfigurationConsulter = $fileConfigurationConsulter;
    }

    public function getCachePath(): string
    {
        return $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__);
    }

    public function getFileConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->fileConfigurationConsulter;
    }

    /**
     * @throws \Exception
     */
    public function preLoadCacheData()
    {
        $dependencyInjectionContainerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $dependencyInjectionContainerBuilder->clearContainerInstance();
        $dependencyInjectionContainerBuilder->createContainer();
    }
}