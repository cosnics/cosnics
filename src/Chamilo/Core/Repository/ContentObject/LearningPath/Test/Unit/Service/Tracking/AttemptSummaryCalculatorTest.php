<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AttemptSummaryCalculator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the AttemptSummaryCalculator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttemptSummaryCalculatorTest extends Test
{
    /**
     * @var AttemptSummaryCalculator
     */
    protected $attemptSummaryCalculator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | AttemptService
     */
    protected $attemptServiceMock;

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

        $this->trackingRepositoryMock = $this->getMockBuilder(TrackingRepositoryInterface::class)
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->attemptSummaryCalculator =
            new AttemptSummaryCalculator($this->attemptServiceMock, $this->trackingRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->attemptSummaryCalculator);
        unset($this->trackingRepositoryMock);
        unset($this->attemptServiceMock);
    }
}


