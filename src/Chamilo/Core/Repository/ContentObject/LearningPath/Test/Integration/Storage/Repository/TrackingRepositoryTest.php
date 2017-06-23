<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper\FixtureBasedTest;

/**
 * Tests the TrackingRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingRepositoryTest extends FixtureBasedTest
{
    /**
     * @var TrackingRepository
     */
    protected $trackingRepository;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        parent::setUp();

        $trackingParameters = new TrackingParameters(1);
        $this->trackingRepository = new TrackingRepository($this->getTestDataClassRepository(), $trackingParameters);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->trackingRepository);
    }

    /**
     * Returns the storage units that need to be created. This method requires a multidimensional array with the
     * names of the storage units per context
     *
     * [ $context => [$storageUnit1, $storageUnit2] ]
     *
     * @return array
     */
    protected function getStorageUnitsToCreate()
    {
        return [
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking' => [
                'learning_path_tree_node_attempt', 'learning_path_tree_node_question_attempt'
            ]
        ];
    }

    /**
     * Returns the fixture files that need to be inserted. This method requires a multidimensional array with the
     * names of the fixture files per context
     *
     * [ $context => [$storageUnit1, $storageUnit2] ]
     *
     * @return array
     */
    protected function getFixtureFiles()
    {
        return [];
    }

    public function test()
    {

    }
}


