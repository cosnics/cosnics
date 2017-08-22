<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Test\Unit\Domain;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeQuestionAttempt;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManagerWrapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Money\InvalidArgumentException;

/**
 * Tests the TrackingParameters
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingParametersTest extends ChamiloTestCase
{
//    /**
//     * @var TrackingParameters
//     */
//    protected $trackingParameters;
//
//    /**
//     * Setup before each test
//     */
//    public function setUp()
//    {
//        $this->trackingParameters = new TrackingParameters();
//    }
//
//    /**
//     * Tear down after each test
//     */
//    public function tearDown()
//    {
//        unset($this->trackingParameters);
//    }

    public function testCreateTrackingParameters()
    {
        $this->assertInstanceOf(TrackingParametersInterface::class, new TrackingParameters(6));
    }

    public function testSetPublicationId()
    {
        $trackingParameters = new TrackingParameters(6);
        $trackingParameters->setPublicationId(5);

        $this->assertEquals(5, $this->get_property_value($trackingParameters, 'publicationId'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetPublicationIdWithInvalidId()
    {
        $trackingParameters = new TrackingParameters(6);
        $trackingParameters->setPublicationId('test');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetPublicationIdWithEmptyId()
    {
        $trackingParameters = new TrackingParameters(6);
        $trackingParameters->setPublicationId(0);
    }

    public function testGetTreeNodeAttemptClassName()
    {
        $trackingParameters = new TrackingParameters(6);
        $this->assertEquals(LearningPathTreeNodeAttempt::class, $trackingParameters->getTreeNodeAttemptClassName());
    }

    public function testGetTreeNodeQuestionAttemptClassName()
    {
        $trackingParameters = new TrackingParameters(6);
        $this->assertEquals(
            LearningPathTreeNodeQuestionAttempt::class, $trackingParameters->getTreeNodeQuestionAttemptClassName()
        );
    }

    public function testGetTreeNodeAttemptConditions()
    {
        $trackingParameters = new TrackingParameters(6);

        $this->assertNotNull($trackingParameters->getTreeNodeAttemptConditions());
    }

    public function testCreateTreeNodeAttemptInstance()
    {
        $trackingParameters = new TrackingParameters(6);
        $this->assertInstanceOf(TreeNodeAttempt::class, $trackingParameters->createTreeNodeAttemptInstance());
    }

    public function testCreateTreeNodeAttemptInstanceSetsPublicationId()
    {
        $trackingParameters = new TrackingParameters(6);

        /** @var LearningPathTreeNodeAttempt $treeNodeAttempt */
        $treeNodeAttempt = $trackingParameters->createTreeNodeAttemptInstance();

        $this->assertEquals(6, $treeNodeAttempt->get_publication_id());
    }

    public function testCreateTreeNodeQuestionAttemptInstance()
    {
        $trackingParameters = new TrackingParameters(6);
        $this->assertInstanceOf(
            TreeNodeQuestionAttempt::class, $trackingParameters->createTreeNodeQuestionAttemptInstance()
        );
    }

    public function testGetLearningPathTargetUserIds()
    {
        $dataManagerWrapperMock = $this->getMockBuilder(DataManagerWrapper::class)
            ->disableOriginalConstructor()->getMock();

        $dataManagerWrapperMock->expects($this->once())
            ->method('getPublicationTargetUserIds')
            ->with(6)
            ->will($this->returnValue([2, 4, 6]));

        $trackingParameters = new TrackingParameters(6, $dataManagerWrapperMock);
        $this->assertEquals([2, 4, 6], $trackingParameters->getLearningPathTargetUserIds(new LearningPath()));
    }

    public function testGetLearningPathTargetUserIdsUsesCache()
    {
        $dataManagerWrapperMock = $this->getMockBuilder(DataManagerWrapper::class)
            ->disableOriginalConstructor()->getMock();

        $dataManagerWrapperMock->expects($this->once())
            ->method('getPublicationTargetUserIds')
            ->with(6)
            ->will($this->returnValue([2, 4, 6]));

        $trackingParameters = new TrackingParameters(6, $dataManagerWrapperMock);
        $trackingParameters->getLearningPathTargetUserIds(new LearningPath());

        $this->assertEquals([2, 4, 6], $trackingParameters->getLearningPathTargetUserIds(new LearningPath()));
    }
}

