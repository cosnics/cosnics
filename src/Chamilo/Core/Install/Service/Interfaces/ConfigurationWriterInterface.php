<?php
namespace Chamilo\Core\Install\Service\Interfaces;

use Chamilo\Core\Install\Configuration;

/**
 * Writes the installer configuration to a configuration file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ConfigurationWriterInterface
{
    /**
     * Writes the installer configuration to a configuration file
     *
     * @param Configuration $configuration
     * @param $outputFile
     */
    public function writeConfiguration(Configuration $configuration, $outputFile);
}