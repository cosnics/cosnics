<?php

namespace Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration;

use Chamilo\Configuration\Service\ConfigurationService;

/**
 * Synchronizes configuration files where default structure locations and roles are defined with the database
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocationConfigurationSynchronizer
{
    /**
     * @var StructureLocationConfigurationLoader
     */
    protected $structureLocationConfigurationLoader;

    /**
     * @var ConfigurationService
     */
    protected $configuration;

    /**
     * StructureLocationConfigurationSynchronizer constructor.
     *
     * @param StructureLocationConfigurationLoader $structureLocationConfigurationLoader
     * @param ConfigurationService $configuration
     */
    public function __construct(
        StructureLocationConfigurationLoader $structureLocationConfigurationLoader, ConfigurationService $configuration
    )
    {
        $this->structureLocationConfigurationLoader = $structureLocationConfigurationLoader;
        $this->configuration = $configuration;
    }

    /**
     * Synchronizes configuration files where default structure locations and roles are defined with the database
     */
    public function synchronize()
    {
        $configuration = $this->structureLocationConfigurationLoader->loadConfiguration(
            $this->configuration->getRegistrationContexts()
        );

        var_dump($configuration);
    }
}