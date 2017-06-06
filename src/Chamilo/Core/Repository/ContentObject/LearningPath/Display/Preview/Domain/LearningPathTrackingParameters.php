<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Platform\Session\Session;
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
    public function getTreeNodeAttemptClassName()
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
     * Creates a new instance of the TreeNodeAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    public function createTreeNodeAttemptInstance()
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

    /**
     * Returns the user ids for whom the learning path was targeted
     *
     * @param LearningPath $learningPath
     *
     * @return \int[]
     */
    public function getLearningPathTargetUserIds(LearningPath $learningPath)
    {
        return array(Session::get_user_id());
    }
}