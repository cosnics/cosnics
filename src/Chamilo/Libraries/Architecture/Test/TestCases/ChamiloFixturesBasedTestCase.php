<?php

namespace Chamilo\Libraries\Architecture\Test\TestCases;

use Chamilo\Libraries\Architecture\Test\Fixtures\ChamiloFixtureLoader;
use Chamilo\Libraries\Architecture\Test\Fixtures\ChamiloStorageUnitCreator;
use Chamilo\Libraries\File\PathBuilder;

/**
 * Base for test class that uses data fixtures
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ChamiloFixturesBasedTestCase extends FixturesBasedTestCase
{
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
     * Drops the storage units that are required for the tests
     */
    protected function dropStorageUnits()
    {

    }

    /**
     * Inserts the fixture data for the tests in the created storage units
     */
    protected function createFixtureData()
    {
        $testDataClassRepository = $this->getTestDataClassRepository();
        $chamiloFixtureLoader = new ChamiloFixtureLoader();

        $objects = $chamiloFixtureLoader->loadFixturesFromPackages($this->getFixtureFiles());
        foreach($objects as $object)
        {
            $testDataClassRepository->create($object);
        }
    }
}