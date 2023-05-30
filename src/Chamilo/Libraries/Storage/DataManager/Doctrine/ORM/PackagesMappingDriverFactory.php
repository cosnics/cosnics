<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\DependencyInjection\Configuration\LibrariesConfiguration;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use InvalidArgumentException;
use RuntimeException;
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

    private MappingDriverFactory $mappingDriverFactory;

    private Processor $processor;

    private Parser $yamlParser;

    public function __construct(MappingDriverFactory $mappingDriverFactory, Processor $processor, Parser $yamlParser)
    {
        $this->mappingDriverFactory = $mappingDriverFactory;
        $this->processor = $processor;
        $this->yamlParser = $yamlParser;
    }

    /**
     * @param string[] $packages
     *
     * @example $packages = array(
     *          'application/weblcms' => $systemPathBuilder->getBasePath() .
     *          'application/weblcms/resources/configuration/config.yml'
     *          )
     */
    public function createMappingDriverForPackages(array $packages = []): MappingDriver
    {
        if (empty($packages))
        {
            throw new InvalidArgumentException('The given list of packages can not be empty');
        }

        $configurations = [];

        foreach ($packages as $package => $packageConfigFile)
        {
            if (!file_exists($packageConfigFile))
            {
                throw new InvalidArgumentException(
                    sprintf(
                        'There is no valid configuration for the package %s at location %s', $package,
                        $packageConfigFile
                    )
                );
            }

            $packageConfiguration = $this->yamlParser->parse(file_get_contents($packageConfigFile));

            if (array_key_exists('chamilo.libraries', $packageConfiguration))
            {
                $configurations[] = $packageConfiguration['chamilo.libraries'];
            }
        }

        if (empty($configurations))
        {
            throw new RuntimeException('There is no mapping driver configuration available in the given packages');
        }

        $librariesConfiguration = new LibrariesConfiguration();
        $packageConfiguration = $this->processor->processConfiguration($librariesConfiguration, $configurations);

        if (!array_key_exists('doctrine', $packageConfiguration) ||
            !array_key_exists('orm', $packageConfiguration['doctrine']) ||
            !array_key_exists('mappings', $packageConfiguration['doctrine']['orm']))
        {
            throw new RuntimeException('There is no mapping driver configuration available in the given packages 2');
        }

        return $this->mappingDriverFactory->createMappingDriver(
            $packageConfiguration['doctrine']['orm']['mappings']
        );
    }
}