<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Display\Preview\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain\TrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Test\Test;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * Tests the preview TrackingParameters class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingParametersTest extends Test
{
    public function testGetTreeNodeAttemptClassName()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertEquals(DummyTreeNodeAttempt::class_name(), $trackingParameters->getTreeNodeAttemptClassName());
    }

    public function testGetTreeNodeQuestionAttemptClassName()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertEquals(
            DummyQuestionAttempt::class_name(), $trackingParameters->getTreeNodeQuestionAttemptClassName()
        );
    }

    public function testGetTreeNodeAttemptConditions()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertNull($trackingParameters->getTreeNodeAttemptConditions());
    }

    public function testCreateTreeNodeAttemptInstance()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertInstanceOf(TreeNodeAttempt::class_name(), $trackingParameters->createTreeNodeAttemptInstance());
    }

    public function testCreateTreeNodeQuestionAttemptInstance()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertInstanceOf(
            TreeNodeQuestionAttempt::class_name(), $trackingParameters->createTreeNodeQuestionAttemptInstance()
        );
    }

    public function testGetLearningPathTargetUserIds()
    {
        $learningPath = new LearningPath();
        $trackingParameters = new TrackingParameters();

        $this->assertEquals(
            [], $trackingParameters->getLearningPathTargetUserIds($learningPath)
        );
    }

    public function testGetLearningPathTargetUserIdsWithSessionNotEmpty()
    {
        $learningPath = new LearningPath();
        $trackingParameters = new TrackingParameters();

        Session::register('_uid', 2);

        $this->assertEquals(
            [2], $trackingParameters->getLearningPathTargetUserIds($learningPath)
        );
    }
}