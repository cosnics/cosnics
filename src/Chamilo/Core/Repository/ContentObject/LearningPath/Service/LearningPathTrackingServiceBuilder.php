<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepository;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * Builds the LearningPathTrackingService and LearningPathTrackingRepository based on the
 * given LearningPathTrackingParameters class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingServiceBuilder
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * LearningPathTrackingServiceBuilder constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * Builds the LearningPathTrackingService and LearningPathTrackingRepository based on the
     * given LearningPathTrackingParameters class
     *
     * @param LearningPathTrackingParametersInterface $learningPathTrackingParameters
     *
     * @return LearningPathTrackingService
     */
    public function buildLearningPathTrackingService(
        LearningPathTrackingParametersInterface $learningPathTrackingParameters
    )
    {
        $repository = new LearningPathTrackingRepository($this->dataClassRepository, $learningPathTrackingParameters);

        return new LearningPathTrackingService($repository, $learningPathTrackingParameters);
    }
}