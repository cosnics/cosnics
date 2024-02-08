<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Filesystem\Filesystem;

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
        ConfigurationConsulter $fileConfigurationConsulter, ConfigurablePathBuilder $configurablePathBuilder,
        Filesystem $filesystem
    )
    {
        parent::__construct($configurablePathBuilder, $filesystem);

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
    public function initializeCache()
    {
        $dependencyInjectionContainerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $dependencyInjectionContainerBuilder->clearContainerInstance();
        $dependencyInjectionContainerBuilder->createContainer();
    }
}