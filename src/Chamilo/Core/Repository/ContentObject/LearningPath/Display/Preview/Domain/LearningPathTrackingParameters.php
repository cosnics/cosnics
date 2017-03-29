<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTrackingParametersInterface;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Tracking parameters for the learning path tracking service and repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingParameters implements LearningPathTrackingParametersInterface
{
    /**
     * @return string
     */
    public function getLearningPathAttemptClassName()
    {
        return DummyAttempt::class_name();
    }

    /**
     * @return string
     */
    public function getLearningPathChildAttemptClassName()
    {
        return DummyChildAttempt::class_name();
    }

    /**
     * @return string
     */
    public function getLearningPathQuestionAttemptClassName()
    {
        return DummyQuestionAttempt::class_name();
    }

    /**
     * @return Condition
     */
    public function getLearningPathAttemptConditions()
    {
        return null;
    }

    /**
     * Creates a new instance of the LearningPathAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt
     */
    public function createLearningPathAttemptInstance()
    {
        return new DummyAttempt();
    }

    /**
     * Creates a new instance of the LearningPathChildAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt
     */
    public function createLearningPathChildAttemptInstance()
    {
        return new DummyChildAttempt();
    }

    /**
     * Creates a new instance of the LearningPathQuestionAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt
     */
    public function createLearningPathQuestionAttemptInstance()
    {
        return new DummyQuestionAttempt();
    }
}