<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Defines the parameters needed for the tracking service and repository
 *
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Domain
 */
interface TrackingParametersInterface
{
    /**
     * Creates a new instance of the TreeNodeAttempt extension
     *
     * @return TreeNodeAttempt
     */
    public function createTreeNodeAttemptInstance();

    /**
     * Creates a new instance of the LearningPathQuestionAttempt extension
     *
     * @return LearningPathQuestionAttempt
     */
    public function createLearningPathQuestionAttemptInstance();

    /**
     * Returns the class name for the TreeNodeAttempt extension
     *
     * @return string
     */
    public function getTreeNodeAttemptClassName();

    /**
     * Returns the class name for the LearningPathQuestionAttempt extension
     *
     * @return string
     */
    public function getLearningPathQuestionAttemptClassName();

    /**
     * Returns the condition needed to retrieve tree node attempts
     * (can be used to add additional parameters to the queries)
     *
     * @return Condition
     */
    public function getTreeNodeAttemptConditions();

    /**
     * Returns the user ids for whom the learning path was targeted
     *
     * @param LearningPath $learningPath
     *
     * @return \int[]
     */
    public function getLearningPathTargetUserIds(LearningPath $learningPath);
}