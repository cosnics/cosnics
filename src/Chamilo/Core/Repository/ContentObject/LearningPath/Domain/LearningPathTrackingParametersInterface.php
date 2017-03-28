<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
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
     * @return string
     */
    public function getLearningPathAttemptClassName();

    /**
     * @return string
     */
    public function getLearningPathChildAttemptClassName();

    /**
     * @return string
     */
    public function getLearningPathQuestionAttemptClassName();

    /**
     * @return Condition
     */
    public function getLearningPathAttemptConditions();
}