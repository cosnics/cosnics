<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepository;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * Builds the TrackingService and TrackingRepository based on the
 * given TrackingParameters class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingServiceBuilder
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * TrackingServiceBuilder constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * Builds the TrackingService and TrackingRepository based on the
     * given TrackingParameters class
     *
     * @param TrackingParametersInterface $trackingParameters
     *
     * @return TrackingService
     */
    public function buildTrackingService(
        TrackingParametersInterface $trackingParameters
    )
    {
        $repository = new TrackingRepository($this->dataClassRepository, $trackingParameters);
        $attemptService = new AttemptService($repository, $trackingParameters);

        return new TrackingService($attemptService, $repository);
    }
}