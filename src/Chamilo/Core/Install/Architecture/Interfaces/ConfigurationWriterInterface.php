<?php
namespace Chamilo\Core\Install\Architecture\Interfaces;

/**
 * Writes the installer configuration to a configuration file
 *
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ConfigurationWriterInterface
{
    /**
     * Writes the installer configuration to a configuration file
     *
     * @param string[][] $configurationValues
     */
    public function writeConfiguration(array $configurationValues, string $outputFile): void;
}