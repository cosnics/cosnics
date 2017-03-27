<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingRepository extends CommonDataClassRepository
{
    /**
     * Finds a learning path attempt by a given learning path and user
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt | \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        $conditions = array();

        $customConditions = $this->getLearningPathAttemptConditions();
        if ($customConditions)
        {
            $conditions[] = $customConditions;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getLearningPathAttemptClassName(), LearningPathAttempt::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($learningPath->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getLearningPathAttemptClassName(), LearningPathAttempt::PROPERTY_USER_ID
            ),
            new StaticConditionVariable($user->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            $this->getLearningPathAttemptClassName(), new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearLearningPathAttemptCache()
    {
        $this->dataClassRepository->getDataClassRepositoryCache()->truncate(LearningPathAttempt::class_name());
    }

    /**
     * Finds the learning path child attempts for a given learning path attempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return LearningPathChildAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathChildAttempts(LearningPathAttempt $learningPathAttempt)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathChildAttempt::class_name(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttempt->getId())
        );

        return $this->dataClassRepository->retrieves(
            LearningPathChildAttempt::class_name(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Finds a LearningPathChildAttempt by a given LearningPathAttempt and LearningPathTreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathChildAttempt | DataClass
     */
    public function findActiveLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathChildAttempt::class_name(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttempt->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathChildAttempt::class_name(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
            ),
            new StaticConditionVariable($learningPathTreeNode->getId())
        );

        $conditions[] = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(
                    LearningPathChildAttempt::class_name(),
                    LearningPathChildAttempt::PROPERTY_STATUS
                ),
                array(LearningPathChildAttempt::STATUS_COMPLETED, LearningPathChildAttempt::STATUS_PASSED)
            )
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            LearningPathChildAttempt::class_name(), new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Finds the LearningPathQuestionAttempt objects for a given LearningPathChildAttempt
     *
     * @param LearningPathChildAttempt $learningPathItemAttempt
     *
     * @return LearningPathQuestionAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathQuestionAttempts(LearningPathChildAttempt $learningPathItemAttempt)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathQuestionAttempt::class_name(),
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathItemAttempt->getId())
        );

        return $this->dataClassRepository->retrieves(
            LearningPathQuestionAttempt::class_name(),
            new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @return Condition
     */
    protected function getLearningPathAttemptConditions()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getLearningPathAttemptClassName(), LearningPathAttempt::PROPERTY_COURSE_ID
            ),
            new StaticConditionVariable($this->get_course_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getLearningPathAttemptClassName(),
                LearningPathAttempt::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($this->get_publication()->get_id())
        );

        return new AndCondition($conditions);
    }

    /**
     * @return string
     */
    protected function getLearningPathAttemptClassName()
    {
        return LearningPathAttempt::class_name();
    }
}