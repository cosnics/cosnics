<?php
namespace Chamilo\Libraries\Storage\Factory;

use Chamilo\Libraries\DependencyInjection\Architecture\Domain\DoctrineORMMappingsConfiguration;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Factory class to create a mapping driver for doctrine with a given configuration array
 *
 * The configuration array should look like this
 *
 *  array(
 *      'default' => array(
 *          'mapping_path1', 'mapping_path2'
 *      ),
 *      'custom' => array(
 *          'custom_mapping_name1' => array(
 *              'type' => choose between ('yaml', 'xml', 'annotation', 'php', 'staticphp'),
 *              'namespace' => 'common\libraries'
 *              'paths' => array('mapping_path1', 'mapping_path2')
 *          )
 *      )
 *  )
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ORM
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineMappingDriverFactory
{
    public function __construct(
        protected Configuration $doctrineConfiguration, protected AdapterInterface $cacheAdapter,
        protected SystemPathBuilder $systemPathBuilder, protected ConfigurablePathBuilder $configurablePathBuilder,
        protected ?string $chamiloRootPath = null
    )
    {
        $this->chamiloRootPath =
            !is_null($chamiloRootPath) ? $chamiloRootPath : $this->systemPathBuilder->getBasePath();
    }

    /**
     * Helper function to create absolute mapping paths based on given relative mapping paths
     *
     * @param string $type
     * @param string[] $mappingPaths
     *
     * @return string[]
     */
    protected function createAbsoluteMappingPaths(string $type, array $mappingPaths): array
    {
        foreach ($mappingPaths as $index => $mappingPath) {
            $absoluteMappingPath = realpath($this->chamiloRootPath . $mappingPath);
            $mappingPaths[$index] = $absoluteMappingPath;

            if (!is_dir($absoluteMappingPath)) {
                throw new InvalidArgumentException(
                    'The given ' . $type . ' mapping path "' . $mappingPath . '" must be an existing directory'
                );
            }
        }

        return $mappingPaths;
    }

    /**
     * @param string[] $paths
     */
    protected function createAttributeDriver(array $paths): AttributeDriver
    {
        return new AttributeDriver($paths);
    }

    /**
     * Creates the mapping configuration based on a given configuration array.
     * The configuration array is
     * processed and validated with the configuration processor
     *
     * @param string[] $mappingConfiguration
     */
    public function createMappingDriver(array $mappingConfiguration = []): MappingDriver
    {
        $mappingConfiguration = $this->processConfiguration($mappingConfiguration);

        $defaultDriver = new MappingDriverChain();

        if (array_key_exists('default', $mappingConfiguration) && !empty($mappingConfiguration['default'])) {
            $annotationPaths = $this->createAbsoluteMappingPaths('annotation', $mappingConfiguration['default']);

            $defaultDriver = $this->createAttributeDriver($annotationPaths);
        }

        return $defaultDriver;
    }

    /**
     * Processes the given configuration
     *
     * @param string[] $mappingConfiguration
     *
     * @return string[][]
     */
    protected function processConfiguration(array $mappingConfiguration = []): array
    {
        $doctrineORMMappingsConfiguration = new DoctrineORMMappingsConfiguration();
        $treeNode = $doctrineORMMappingsConfiguration->getConfigTreeBuilder()->buildTree();

        return $treeNode->finalize($mappingConfiguration);
    }
}
