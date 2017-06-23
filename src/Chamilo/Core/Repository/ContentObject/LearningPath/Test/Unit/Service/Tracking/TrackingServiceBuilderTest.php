<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain\TrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AssessmentTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AttemptSummaryCalculator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AttemptTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\ProgressCalculator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingServiceBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepository;
use Chamilo\Libraries\Architecture\Test\Test;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * Tests the TrackingServiceBuilder class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingServiceBuilderTest extends Test
{
    /**
     * @var TrackingService
     */
    protected $trackingService;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        $dataClassRepositoryMock = $this->getMockBuilder(DataClassRepository::class)
            ->disableOriginalConstructor()->getMock();

        /** @var TrackingParameters | \PHPUnit_Framework_MockObject_MockObject $trackingParametersMock */
        $trackingParametersMock = $this->getMockBuilder(TrackingParametersInterface::class)
            ->getMockForAbstractClass();

        $trackingServiceBuilder = new TrackingServiceBuilder($dataClassRepositoryMock);
        $this->trackingService = $trackingServiceBuilder->buildTrackingService($trackingParametersMock);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
    }

    public function testBuildTrackingService()
    {
        $this->assertInstanceOf(TrackingService::class, $this->trackingService);
    }

    public function testBuildTrackingServiceSetsAttemptTrackingService()
    {
        $this->assertInstanceOf(
            AttemptTrackingService::class, $this->get_property_value($this->trackingService, 'attemptTrackingService')
        );
    }

    public function testBuildTrackingServiceSetsAttemptSummaryCalculator()
    {
        $this->assertInstanceOf(
            AttemptSummaryCalculator::class,
            $this->get_property_value($this->trackingService, 'attemptSummaryCalculator')
        );
    }

    public function testBuildTrackingServiceSetsProgressCalculator()
    {
        $this->assertInstanceOf(
            ProgressCalculator::class,
            $this->get_property_value($this->trackingService, 'progressCalculator')
        );
    }

    public function testBuildTrackingServiceSetsAssessmentTrackingService()
    {
        $this->assertInstanceOf(
            AssessmentTrackingService::class,
            $this->get_property_value($this->trackingService, 'assessmentTrackingService')
        );
    }

    public function testBuildTrackingServiceSetsTrackingRepository()
    {
        $this->assertInstanceOf(
            TrackingRepository::class,
            $this->get_property_value(
                $this->get_property_value($this->trackingService, 'attemptTrackingService'), 'trackingRepository'
            )
        );
    }

    public function testBuildTrackingServiceSetsAttemptService()
    {
        $this->assertInstanceOf(
            AttemptService::class,
            $this->get_property_value(
                $this->get_property_value($this->trackingService, 'attemptTrackingService'), 'attemptService'
            )
        );
    }

    public function testBuildTrackingServiceSetsTrackingParameters()
    {
        $this->assertInstanceOf(
            TrackingParametersInterface::class,
            $this->get_property_value(
                $this->get_property_value(
                    $this->get_property_value($this->trackingService, 'attemptTrackingService'), 'attemptService'
                ), 'trackingParameters'
            )
        );
    }
}