<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper;

use Chamilo\Libraries\File\PathBuilder;

/**
 * Base for test class that uses data fixtures
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class FixtureBasedTest extends DependencyInjectionBasedTest
{
    /**
     * Setup before each test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->createStorageUnits();
        $this->createFixtureData();
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Returns the storage units that need to be created. This method requires a multidimensional array with the
     * names of the storage units per context
     *
     * [ $context => [$storageUnit1, $storageUnit2] ]
     *
     * @return array
     */
    abstract protected function getStorageUnitsToCreate();

    /**
     * Returns the fixture files that need to be inserted. This method requires a multidimensional array with the
     * names of the fixture files per context
     *
     * [ $context => [$fixtureFileName1, $fixtureFileName2] ]
     *
     * @return array
     */
    abstract protected function getFixtureFiles();

    /**
     * Creates the storage units that are required for the tests
     */
    protected function createStorageUnits()
    {
        $testStorageUnitRepository = $this->getTestStorageUnitRepository();

        $chamiloStorageUnitCreator =
            new ChamiloStorageUnitCreator(PathBuilder::getInstance(), $testStorageUnitRepository);

        foreach ($this->getStorageUnitsToCreate() as $context => $storageUnits)
        {
            $chamiloStorageUnitCreator->createStorageUnitsForContext($context, $storageUnits);
        }
    }

    /**
     * Inserts the fixture data for the tests in the created storage units
     */
    protected function createFixtureData()
    {
        $chamiloFixtureLoader = new ChamiloFixtureLoader();

        $testDataClassRepository = $this->getTestDataClassRepository();

        foreach ($this->getFixtureFiles() as $context => $fixtureFiles)
        {
            $basePath = PathBuilder::getInstance()->namespaceToFullPath($context . '\Test') . 'Fixtures/';

            foreach ($fixtureFiles as $fixtureFile)
            {
                $loadedObjects = $chamiloFixtureLoader->loadFile($basePath . $fixtureFile . '.yml');
                foreach ($loadedObjects->getObjects() as $object)
                {
                    $testDataClassRepository->create($object);
                }
            }
        }
    }
}