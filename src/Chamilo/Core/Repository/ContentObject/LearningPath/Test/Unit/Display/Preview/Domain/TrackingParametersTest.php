<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Display\Preview\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain\TrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Tests the preview TrackingParameters class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingParametersTest extends ChamiloTestCase
{
    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
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

        $this->getSession()->set(Manager::SESSION_USER_IO, 2);

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