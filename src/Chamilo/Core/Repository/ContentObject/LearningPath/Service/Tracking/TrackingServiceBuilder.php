<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * Builds the TrackingServices and TrackingRepository based on the
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
    public function __construct(DataClassRepository $dataClassRepository = null)
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
    public function buildTrackingService(TrackingParametersInterface $trackingParameters)
    {
        $repository = $this->buildTrackingRepository($trackingParameters);
        $attemptService = new AttemptService($repository, $trackingParameters);
        $attemptTrackingService = new AttemptTrackingService($attemptService, $repository);
        $attemptSummaryCalculator = new AttemptSummaryCalculator($attemptService, $repository);
        $progressCalculator = new ProgressCalculator($attemptService);

        $assessmentTrackingService = new AssessmentTrackingService(
            $attemptService, $attemptTrackingService, $repository
        );


        return new TrackingService(
            $attemptTrackingService, $attemptSummaryCalculator, $assessmentTrackingService, $progressCalculator
        );
    }

    /**
     * Builds the TrackingRepository based on the given TrackingParameters
     *
     * @param TrackingParametersInterface $trackingParameters
     *
     * @return TrackingRepositoryInterface
     */
    protected function buildTrackingRepository(TrackingParametersInterface $trackingParameters)
    {
        return new TrackingRepository($this->dataClassRepository, $trackingParameters);
    }
}