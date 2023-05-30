<?php
namespace Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration;

use Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Interfaces\LoaderInterface;
use Chamilo\Libraries\File\SystemPathBuilder;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads structure location configuration from the packages
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Loader implements LoaderInterface
{

    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(SystemPathBuilder $systemPathBuilder)
    {
        $this->systemPathBuilder = $systemPathBuilder;
    }

    /**
     * Loads the structure location configuration from the given packages
     *
     * @param array $packageNamespaces
     *
     * @return string[]
     */
    public function loadConfiguration($packageNamespaces = [])
    {
        $configurationDirectories = [];

        foreach ($packageNamespaces as $packageNamespace)
        {
            $packagePath = $this->systemPathBuilder->namespaceToFullPath($packageNamespace);
            $configurationDirectories[] = $packagePath . 'Resources/Configuration';
        }

        $locator = new FileLocator($configurationDirectories);

        try
        {
            $configurationFiles = $locator->locate('StructureLocations.yml', null, false);
        }
        catch (Exception $ex)
        {
            $configurationFiles = [];
        }

        $configuration = [];

        foreach ($configurationFiles as $configurationFile)
        {
            $configuration = array_merge_recursive($configuration, Yaml::parse(file_get_contents($configurationFile)));
        }

        return $configuration;
    }
}