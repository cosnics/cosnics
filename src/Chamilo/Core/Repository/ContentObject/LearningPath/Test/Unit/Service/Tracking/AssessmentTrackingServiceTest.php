<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AssessmentTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AttemptTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the AssessmentTrackingService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentTrackingServiceTest extends Test
{
    /**
     * @var AssessmentTrackingService
     */
    protected $assessmentTrackingService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | AttemptService
     */
    protected $attemptServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | AttemptTrackingService
     */
    protected $attemptTrackingServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | TrackingRepositoryInterface
     */
    protected $trackingRepositoryMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->attemptServiceMock = $this->getMockBuilder(AttemptService::class)
            ->disableOriginalConstructor()->getMock();

        $this->attemptTrackingServiceMock = $this->getMockBuilder(AttemptTrackingService::class)
            ->disableOriginalConstructor()->getMock();

        $this->trackingRepositoryMock = $this->getMockBuilder(TrackingRepositoryInterface::class)
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->assessmentTrackingService = new AssessmentTrackingService(
            $this->attemptServiceMock, $this->attemptTrackingServiceMock, $this->trackingRepositoryMock
        );
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->assessmentTrackingService);
        unset($this->attemptTrackingServiceMock);
        unset($this->trackingRepositoryMock);
        unset($this->attemptServiceMock);
    }
}

