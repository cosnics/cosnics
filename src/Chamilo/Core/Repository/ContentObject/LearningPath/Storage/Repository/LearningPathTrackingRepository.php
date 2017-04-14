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
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingRepository extends CommonDataClassRepository
    implements LearningPathTrackingRepositoryInterface
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
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $this->dataClassRepository->getDataClassRepositoryCache()->truncate($learningPathAttemptClassName);
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearLearningPathChildAttemptCache()
    {
        $learningPathChildAttemptClassName =
            $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName();

        $this->dataClassRepository->getDataClassRepositoryCache()->truncate($learningPathChildAttemptClassName);
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
     * Finds a LearningPathChildAttempt by a given ID
     *
     * @param int $learningPathChildAttemptId
     *
     * @return DataClass | LearningPathChildAttempt
     */
    public function findLearningPathChildAttemptById($learningPathChildAttemptId)
    {
        return $this->dataClassRepository->retrieveById(
            $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName(), $learningPathChildAttemptId
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

    /**
     * Finds the LearningPathAttempt objects for a given LearningPath with a given condition, offset, count and orderBy
     * Joined with users for searching and sorting
     *
     * @param LearningPath $learningPath
     * @param Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findLearningPathAttemptsWithUser(
        LearningPath $learningPath, Condition $condition = null, $offset = 0, $count = 0, $orderBy = array()
    )
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $properties = new DataClassProperties();

        $properties->add(new PropertiesConditionVariable($learningPathAttemptClassName));
        $properties->add(new FixedPropertyConditionVariable(User::class_name(), User::PROPERTY_ID, 'user_id'));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL));

        $joins = $this->getJoinsForLearningPathAttemptsWithUser();
        $condition = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath, $condition);
        $parameters = new RecordRetrievesParameters($properties, $condition, $count, $offset, $orderBy, $joins);

        return $this->dataClassRepository->records($learningPathAttemptClassName, $parameters);
    }

    /**
     * Counts the learning path attempts joined with users for searching
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countLearningPathAttemptsWithUser(LearningPath $learningPath, Condition $condition = null)
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $condition = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath, $condition);
        $joins = $this->getJoinsForLearningPathAttemptsWithUser();

        $parameters = new DataClassCountParameters($condition, $joins);

        return $this->dataClassRepository->count($learningPathAttemptClassName, $parameters);
    }

    /**
     * Returns the joins object needed to join the LearningPathAttempt class with the User class
     *
     * @return Joins
     */
    protected function getJoinsForLearningPathAttemptsWithUser()
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $joins = new Joins();

        $joins->add(
            new Join(
                User::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                    new PropertyConditionVariable(
                        $learningPathAttemptClassName, LearningPathAttempt::PROPERTY_USER_ID
                    )
                )
            )
        );

        return $joins;
    }

    /**
     * Returns the condition needed to retrieve LearningPathAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return AndCondition|Condition
     */
    protected function getConditionForLearningPathAttemptsForLearningPath(
        LearningPath $learningPath, Condition $condition = null
    )
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $learningPathAttemptClassName, LearningPathAttempt::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($learningPath->getId())
        );

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }
}