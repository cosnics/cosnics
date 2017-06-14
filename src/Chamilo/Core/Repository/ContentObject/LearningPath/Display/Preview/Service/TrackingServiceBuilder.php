<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Storage\Repository\TrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;

/**
 * Builds the TrackingServices and TrackingRepository based on the
 * given TrackingParameters class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingServiceBuilder extends \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TrackingServiceBuilder
{
    /**
     * Builds the TrackingRepository based on the given TrackingParameters
     *
     * @param TrackingParametersInterface $trackingParameters
     *
     * @return TrackingRepository
     */
    protected function buildTrackingRepository(TrackingParametersInterface $trackingParameters)
    {
        return new TrackingRepository();
    }
}