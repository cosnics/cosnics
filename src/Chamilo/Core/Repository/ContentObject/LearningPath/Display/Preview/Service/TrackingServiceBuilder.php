<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Storage\Repository\TrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;

/**
 * Builds the TrackingServices and TrackingRepository based on the
 * given TrackingParameters class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingServiceBuilder extends \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingServiceBuilder
{
    /**
     * @var TrackingRepositoryInterface
     */
    protected $trackingRepository;

    /**
     * Builds the TrackingRepository based on the given TrackingParameters
     *
     * @param TrackingParametersInterface $trackingParameters
     *
     * @return TrackingRepository
     */
    protected function buildTrackingRepository(TrackingParametersInterface $trackingParameters)
    {
        return $this->getTrackingRepository();
    }

    /**
     * Returns the tracking repository for this builder
     */
    public function getTrackingRepository()
    {
        if(!isset($this->trackingRepository))
        {
            $this->trackingRepository = new TrackingRepository();
        }

        return $this->trackingRepository;
    }
}