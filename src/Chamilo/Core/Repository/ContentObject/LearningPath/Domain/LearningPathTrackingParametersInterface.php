<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Defines the parameters needed for the tracking service and repository
 *
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Domain
 */
interface LearningPathTrackingParametersInterface
{
    /**
     * Creates a new instance of the LearningPathAttempt extension
     *
     * @return LearningPathAttempt
     */
    public function createLearningPathAttemptInstance();

    /**
     * Creates a new instance of the LearningPathChildAttempt extension
     *
     * @return LearningPathChildAttempt
     */
    public function createLearningPathChildAttemptInstance();

    /**
     * Creates a new instance of the LearningPathQuestionAttempt extension
     *
     * @return LearningPathQuestionAttempt
     */
    public function createLearningPathQuestionAttemptInstance();

    /**
     * Returns the class name for the LearningPathAttempt extension
     *
     * @return string
     */
    public function getLearningPathAttemptClassName();

    /**
     * Returns the class name for the LearningPathChildAttempt extension
     *
     * @return string
     */
    public function getLearningPathChildAttemptClassName();

    /**
     * Returns the class name for the LearningPathQuestionAttempt extension
     *
     * @return string
     */
    public function getLearningPathQuestionAttemptClassName();

    /**
     * Returns the condition needed to retrieve learning path attempts
     * (can be used to add additional parameters to the queries)
     *
     * @return Condition
     */
    public function getLearningPathAttemptConditions();

    /**
     * Returns the user ids for whom the learning path was targeted
     *
     * @param LearningPath $learningPath
     *
     * @return \int[]
     */
    public function getLearningPathTargetUserIds(LearningPath $learningPath);
}