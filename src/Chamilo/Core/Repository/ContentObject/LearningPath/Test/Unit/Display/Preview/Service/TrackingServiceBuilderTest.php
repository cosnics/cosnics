<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Display\Preview\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain\TrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Service\TrackingServiceBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Storage\Repository\TrackingRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * Tests the TrackingServiceBuilder class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingServiceBuilderTest extends ChamiloTestCase
{
    public function testBuildTrackingServiceSetsCorrectRepository()
    {
        $dataClassRepositoryMock = $this->getMockBuilder(DataClassRepository::class)
            ->disableOriginalConstructor()->getMock();

        $trackingServiceBuilder = new TrackingServiceBuilder($dataClassRepositoryMock);

        $trackingService = $trackingServiceBuilder->buildTrackingService(new TrackingParameters());
        $attemptTrackingService = $this->get_property_value($trackingService, 'attemptTrackingService');

        $this->assertInstanceOf(
            TrackingRepository::class, $this->get_property_value($attemptTrackingService, 'trackingRepository')
        );
    }
}