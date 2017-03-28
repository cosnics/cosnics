<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
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
     * @var LearningPathTrackingParametersInterface
     */
    protected $learningPathTrackingParameters;

    /**
     * LearningPathTrackingRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     * @param LearningPathTrackingParametersInterface $learningPathTrackingParameters
     */
    public function __construct(
        DataClassRepository $dataClassRepository,
        LearningPathTrackingParametersInterface $learningPathTrackingParameters
    )
    {
        parent::__construct($dataClassRepository);

        $this->learningPathTrackingParameters = $learningPathTrackingParameters;
    }

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

        $customConditions = $this->learningPathTrackingParameters->getLearningPathAttemptConditions();
        if ($customConditions)
        {
            $conditions[] = $customConditions;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->learningPathTrackingParameters->getLearningPathAttemptClassName(),
                LearningPathAttempt::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($learningPath->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->learningPathTrackingParameters->getLearningPathAttemptClassName(),
                LearningPathAttempt::PROPERTY_USER_ID
            ),
            new StaticConditionVariable($user->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            $this->learningPathTrackingParameters->getLearningPathAttemptClassName(),
            new DataClassRetrieveParameters($condition)
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
                $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttempt->getId())
        );

        return $this->dataClassRepository->retrieves(
            $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName(),
            new DataClassRetrievesParameters($condition)
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
                $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttempt->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
            ),
            new StaticConditionVariable($learningPathTreeNode->getId())
        );

        $conditions[] = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(
                    $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName(),
                    LearningPathChildAttempt::PROPERTY_STATUS
                ),
                array(LearningPathChildAttempt::STATUS_COMPLETED, LearningPathChildAttempt::STATUS_PASSED)
            )
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName(),
            new DataClassRetrieveParameters($condition)
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
                $this->learningPathTrackingParameters->getLearningPathQuestionAttemptClassName(),
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathItemAttempt->getId())
        );

        return $this->dataClassRepository->retrieves(
            $this->learningPathTrackingParameters->getLearningPathQuestionAttemptClassName(),
            new DataClassRetrievesParameters($condition)
        );
    }
}