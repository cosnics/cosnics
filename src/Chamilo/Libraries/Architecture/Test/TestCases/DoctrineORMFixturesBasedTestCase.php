<?php

namespace Chamilo\Libraries\Architecture\Test\TestCases;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Test\Fixtures\ChamiloFixtureLoader;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\PackagesMappingDriverFactory;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * TestCase that recreates the database (partially) and installs fixture in the database for this test case
 *
 * @package common\libraries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DoctrineORMFixturesBasedTestCase extends FixturesBasedTestCase
{
    /**
     * @var string[]
     */
    protected $entityClassNames;

    /**
     * @var ClassMetadata[]
     */
    protected $classMetadata;

    /**
     * Resets the database
     */
    protected function createStorageUnits()
    {
        $entityManager = $this->getTestEntityManager();
        $schemaTool = $this->getTestSchemaTool();
        $packagesMappingDriverFactory = $this->getPackagesMappingDriverFactory();
        $classNameUtilities = $this->getClassNameUtilities();

        $packageConfigurations = [];
        $entitiesToCreate = [];

        $packages = $this->getStorageUnitsToCreate();
        foreach($packages as $packageContext => $entitiesToCreateForPackage)
        {
            $configurationPath = Path::getInstance()->namespaceToFullPath($packageContext) .
                'Resources/Configuration/Config.yml';

            if(file_exists($configurationPath))
            {
                $packageConfigurations[] = $configurationPath;
                $entitiesToCreate = array_merge($entitiesToCreate, $entitiesToCreateForPackage);
            }
        }

        $mappingDriver = $packagesMappingDriverFactory->createMappingDriverForPackages($packageConfigurations);
        $allClassNames = $mappingDriver->getAllClassNames();

        $this->classMetadata = array();

        foreach ($allClassNames as $fullyQualifiedClassName)
        {
            $className = $classNameUtilities->getClassnameFromNamespace($fullyQualifiedClassName);
            if(in_array($className, $entitiesToCreate))
            {
                $this->classMetadata[] = $entityManager->getClassMetadata($fullyQualifiedClassName);
                $this->entityClassNames[] = $fullyQualifiedClassName;
            }
        }

        $schemaTool->dropSchema($this->classMetadata);
        $schemaTool->createSchema($this->classMetadata);
    }

    /**
     * Drops the storage units that are required for the tests
     */
    protected function dropStorageUnits()
    {
        $schemaTool = $this->getTestSchemaTool();
        $schemaTool->dropSchema($this->classMetadata);
    }

    /**
     * Loads the fixtures
     */
    protected function createFixtureData()
    {
        $entityManager = $this->getTestEntityManager();
        $chamiloFixtureLoader = new ChamiloFixtureLoader();

        $objects = $chamiloFixtureLoader->loadFixturesFromPackages($this->getFixtureFiles());
        foreach($objects as $object)
        {
            if(!in_array(get_class($object), $this->entityClassNames))
            {
                continue;
            }

            $entityManager->persist($object);
        }

        $entityManager->flush();
    }

    /**
     * @return object | PackagesMappingDriverFactory
     */
    protected function getPackagesMappingDriverFactory()
    {
        return $this->getService('doctrine.orm.packages_mapping_driver_factory');
    }

    /**
     * Returns the test entity manager from the dependency injection container
     *
     * @return object | \Doctrine\ORM\Tools\SchemaTool
     */
    public function getTestSchemaTool()
    {
        return $this->getService('doctrine.orm.test.schema_tool');
    }

    /**
     * @return object | ClassnameUtilities
     */
    public function getClassNameUtilities()
    {
        return $this->getService('chamilo.libraries.architecture.classname_utilities');
    }
}