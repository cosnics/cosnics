<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Display\Preview\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain\TrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\Session\SessionUtilities;

/**
 * Tests the preview TrackingParameters class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingParametersTest extends ChamiloTestCase
{
    public function getSessionUtilities(): SessionUtilities
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionUtilities::class);
    }

    public function testCreateTreeNodeAttemptInstance()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertInstanceOf(TreeNodeAttempt::class, $trackingParameters->createTreeNodeAttemptInstance());
    }

    public function testCreateTreeNodeQuestionAttemptInstance()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertInstanceOf(
            TreeNodeQuestionAttempt::class, $trackingParameters->createTreeNodeQuestionAttemptInstance()
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

        $this->getSessionUtilities()->register('_uid', 2);

        $this->assertEquals(
            [2], $trackingParameters->getLearningPathTargetUserIds($learningPath)
        );
    }

    public function testGetTreeNodeAttemptClassName()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertEquals(DummyTreeNodeAttempt::class, $trackingParameters->getTreeNodeAttemptClassName());
    }

    public function testGetTreeNodeAttemptConditions()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertNull($trackingParameters->getTreeNodeAttemptConditions());
    }

    public function testGetTreeNodeQuestionAttemptClassName()
    {
        $trackingParameters = new TrackingParameters();
        $this->assertEquals(
            DummyQuestionAttempt::class, $trackingParameters->getTreeNodeQuestionAttemptClassName()
        );
    }
}