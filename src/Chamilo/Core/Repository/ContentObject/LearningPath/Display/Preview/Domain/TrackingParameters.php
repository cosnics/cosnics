<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Tracking parameters for the learning path tracking service and repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingParameters implements TrackingParametersInterface
{

    /**
     *
     * @return string
     */
    public function getTreeNodeAttemptClassName()
    {
        return DummyTreeNodeAttempt::class;
    }

    /**
     *
     * @return string
     */
    public function getTreeNodeQuestionAttemptClassName()
    {
        return DummyQuestionAttempt::class;
    }

    /**
     *
     * @return Condition
     */
    public function getTreeNodeAttemptConditions()
    {
        return null;
    }

    /**
     * Creates a new instance of the TreeNodeAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    public function createTreeNodeAttemptInstance()
    {
        return new DummyTreeNodeAttempt();
    }

    /**
     * Creates a new instance of the TreeNodeQuestionAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt
     */
    public function createTreeNodeQuestionAttemptInstance()
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
        if (empty(Session::get_user_id()))
        {
            return [];
        }

        return [Session::get_user_id()];
    }
}