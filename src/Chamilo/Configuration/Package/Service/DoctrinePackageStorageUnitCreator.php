<?php

namespace Chamilo\Configuration\Package\Service;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\PackagesMappingDriverFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Class DoctrinePackageStorageCreator
 * @package Chamilo\Configuration\Package\Service
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class DoctrinePackageStorageUnitCreator
{
    /**
     * @var ConfigurablePathBuilder
     */
    protected $pathBuilder;

    /**
     * @var PackagesMappingDriverFactory
     */
    protected $packagesMappingDriverFactory;

    /**
     * @var SchemaTool
     */
    protected $schemaTool;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * DoctrinePackageStorageCreator constructor.
     *
     * @param ConfigurablePathBuilder $pathBuilder
     * @param PackagesMappingDriverFactory $packagesMappingDriverFactory
     * @param SchemaTool $schemaTool
     * @param EntityManager $entityManager
     */
    public function __construct(
        ConfigurablePathBuilder $pathBuilder, PackagesMappingDriverFactory $packagesMappingDriverFactory,
        SchemaTool $schemaTool, EntityManager $entityManager
    )
    {
        $this->pathBuilder = $pathBuilder;
        $this->packagesMappingDriverFactory = $packagesMappingDriverFactory;
        $this->schemaTool = $schemaTool;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $package
     * @param string[] $excludedEntityClasses
     *
     * @return string[] - The created table names
     */
    public function createStorageUnitsForPackage(
        string $package, array $excludedEntityClasses = []
    )
    {
        $packagesMappingDriverFactory = $this->packagesMappingDriverFactory;
        $schemaTool = $this->schemaTool;

        $classesMetadata = array();

        $classNameUtilities = ClassnameUtilities::getInstance();
        $package = $classNameUtilities->getNamespaceParent($package);

        $configFile = Path::getInstance()->namespaceToFullPath($package) . 'Resources/Configuration/Config.yml';
        if (!file_exists($configFile))
        {
            return [];
        }

        $packages = [$package => $configFile];

        try
        {
            $mappingDriver = $packagesMappingDriverFactory->createMappingDriverForPackages($packages);
        }
        catch (\Exception $ex)
        {
            return [];
        }

        $tableNames = [];

        $entityClasses = $mappingDriver->getAllClassNames();
        foreach ($entityClasses as $entityClass)
        {
            if (in_array($entityClass, $excludedEntityClasses))
            {
                continue;
            }

            $classMetadata = $this->entityManager->getClassMetadata($entityClass);
            $classesMetadata[] = $classMetadata;
            $tableNames[] = $classMetadata->getTableName();
        }

        $schemaTool->updateSchema($classesMetadata, true);

        return $tableNames;
    }
}
