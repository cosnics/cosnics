<?php
namespace Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration;

use Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Interfaces\LoaderInterface;
use Chamilo\Libraries\File\Path;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads structure location configuration from the packages
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Loader implements LoaderInterface
{

    /**
     *
     * @var Path
     */
    protected $pathUtilities;

    /**
     * StructureLocationConfigurationLoader constructor.
     * 
     * @param Path $pathUtilities
     */
    public function __construct(Path $pathUtilities)
    {
        $this->pathUtilities = $pathUtilities;
    }

    /**
     * Loads the structure location configuration from the given packages
     * 
     * @param array $packageNamespaces
     *
     * @return string[]
     */
    public function loadConfiguration($packageNamespaces = array())
    {
        $configurationDirectories = array();
        
        foreach ($packageNamespaces as $packageNamespace)
        {
            $packagePath = $this->pathUtilities->namespaceToFullPath($packageNamespace);
            $configurationDirectories[] = $packagePath . 'Resources/Configuration';
        }
        
        $locator = new FileLocator($configurationDirectories);
        
        try
        {
            $configurationFiles = $locator->locate('StructureLocations.yml', null, false);
        }
        catch (\Exception $ex)
        {
            $configurationFiles = array();
        }
        
        $configuration = array();
        
        foreach ($configurationFiles as $configurationFile)
        {
            $configuration = array_merge_recursive($configuration, Yaml::parse($configurationFile));
        }
        
        return $configuration;
    }
}