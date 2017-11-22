<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\DependencyInjection\Configuration\LibrariesConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Parser;

/**
 * Mapping driver factory based on a given set of packages.
 * Parses the configuration of the packages and creates
 * the mapping driver based on the configurations
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ORM
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackagesMappingDriverFactory
{

    /**
     * The mapping driver factory service
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\MappingDriverFactory
     */
    private $mappingDriverFactory;

    /**
     * The configuration processor
     *
     * @var \Symfony\Component\Config\Definition\Processor
     */
    private $processor;

    /**
     * The YAML Parser
     *
     * @var \Symfony\Component\Yaml\Parser
     */
    private $yamlParser;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\MappingDriverFactory $mappingDriverFactory
     * @param \Symfony\Component\Config\Definition\Processor $processor
     * @param \Symfony\Component\Yaml\Parser $yamlParser
     */
    public function __construct(MappingDriverFactory $mappingDriverFactory, Processor $processor, Parser $yamlParser)
    {
        $this->mappingDriverFactory = $mappingDriverFactory;
        $this->processor = $processor;
        $this->yamlParser = $yamlParser;
    }

    /**
     * Creates the mapping driver for the given packages.
     * The packages are defined with the namespace and the location
     * of the config file.
     *
     * @example $packages = array(
     *          'application/weblcms' => Path::getInstance()->getBasePath() .
     *          'application/weblcms/resources/configuration/config.yml'
     *          )
     * @param string[] $packages
     * @return \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
     */
    public function createMappingDriverForPackages($packages = array())
    {
        if (empty($packages))
        {
            throw new \InvalidArgumentException('The given list of packages can not be empty');
        }

        $configurations = array();

        foreach ($packages as $package => $packageConfigFile)
        {
            if (! file_exists($packageConfigFile))
            {
                throw new \InvalidArgumentException(
                    sprintf(
                        'There is no valid configuration for the package %s at location %s',
                        $package,
                        $packageConfigFile));
            }

            $packageConfiguration = $this->yamlParser->parse(file_get_contents($packageConfigFile));

            if (array_key_exists('chamilo.libraries', $packageConfiguration))
            {
                $configurations[] = $packageConfiguration['chamilo.libraries'];
            }
        }

        if (empty($configurations))
        {
            throw new \RuntimeException('There is no mapping driver configuration available in the given packages');
        }

        $librariesConfiguration = new LibrariesConfiguration();
        $packageConfiguration = $this->processor->processConfiguration($librariesConfiguration, $configurations);

        if (! array_key_exists('doctrine', $packageConfiguration) ||
             ! array_key_exists('orm', $packageConfiguration['doctrine']) ||
             ! array_key_exists('mappings', $packageConfiguration['doctrine']['orm']))
        {
            throw new \RuntimeException('There is no mapping driver configuration available in the given packages 2');
        }

        $mappingDriver = $this->mappingDriverFactory->createMappingDriver(
            $packageConfiguration['doctrine']['orm']['mappings']);

        return $mappingDriver;
    }
}