<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
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
     * Finds all the LearningPathChildAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | LearningPathChildAttempt[]
     */
    public function findLearningPathChildAttemptsForLearningPath(LearningPath $learningPath)
    {
        $learningPathChildAttemptClassName =
            $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName();

        $condition = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath);

        $joins = new Joins();
        $joins->add($this->getJoinForLearningPathAttemptWithLearningPathChildAttempt());

        $parameters = new DataClassRetrievesParameters($condition, null, null, array(), $joins);

        return $this->dataClassRepository->retrieves($learningPathChildAttemptClassName, $parameters);
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
     * @param LearningPathChildAttempt $learningPathChildAttempt
     *
     * @return LearningPathQuestionAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathQuestionAttempts(LearningPathChildAttempt $learningPathChildAttempt)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->learningPathTrackingParameters->getLearningPathQuestionAttemptClassName(),
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathChildAttempt->getId())
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

        $properties = $this->getPropertiesForLearningPathAttemptsWithUser();
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
        $joins = new Joins();
        $joins->add(new Join(User::class_name(), $this->getConditionForLearningPathAttemptWithUser()));

        return $joins;
    }

    /**
     * Returns the properties needed to retrieve the data for learning path attempts with users
     *
     * @return DataClassProperties
     */
    protected function getPropertiesForLearningPathAttemptsWithUser()
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $properties = new DataClassProperties();

        $properties->add(new PropertiesConditionVariable($learningPathAttemptClassName));
        $properties->add(new FixedPropertyConditionVariable(User::class_name(), User::PROPERTY_ID, 'user_id'));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL));

        return $properties;
    }

    /**
     * Finds the targeted users (left) joined with the learning path attempts
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithLearningPathAttempts(
        LearningPath $learningPath, Condition $condition = null, $offset = 0, $count = 0, $orderBy = array()
    )
    {
        $properties = $this->getPropertiesForLearningPathAttemptsWithUser();
        $condition = $this->getConditionForTargetUsersForLearningPath($learningPath, $condition);
        $joins = $this->getJoinsForTargetUsersWithLearningPathAttempts($learningPath);

        return $this->dataClassRepository->records(
            User::class_name(), new RecordRetrievesParameters(
                $properties, $condition, $count, $offset, $orderBy, $joins
            )
        );
    }

    /**
     * Counts the targeted users (left) joined with the learning path attempts
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersWithLearningPathAttempts(LearningPath $learningPath, Condition $condition = null)
    {
        $condition = $this->getConditionForTargetUsersForLearningPath($learningPath, $condition);
        $joins = $this->getJoinsForTargetUsersWithLearningPathAttempts($learningPath);

        return $this->dataClassRepository->count(
            User::class_name(), new DataClassCountParameters($condition, $joins)
        );
    }

    /**
     * Counts the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithoutLearningPathAttempts(LearningPath $learningPath)
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $conditions = array();
        $conditions[] = $this->getConditionForTargetUsersForLearningPath($learningPath);
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($learningPathAttemptClassName, LearningPathAttempt::PROPERTY_ID), null
        );

        $condition = new AndCondition($conditions);
        $joins = $this->getJoinsForTargetUsersWithLearningPathAttempts($learningPath);

        return $this->dataClassRepository->count(
            User::class_name(), new DataClassCountParameters($condition, $joins)
        );
    }

    /**
     * Counts the target users with attempts on a learning path that are completed
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithFullLearningPathAttempts(LearningPath $learningPath)
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $conditions = array();
        $conditions[] = $this->getConditionForTargetUsersForLearningPath($learningPath);
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($learningPathAttemptClassName, LearningPathAttempt::PROPERTY_ID),
            new StaticConditionVariable(100)
        );

        $condition = new AndCondition($conditions);
        $joins = $this->getJoinsForTargetUsersWithLearningPathAttempts($learningPath);

        return $this->dataClassRepository->count(
            User::class_name(), new DataClassCountParameters($condition, $joins)
        );
    }

    /**
     * Counts the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithPartialLearningPathAttempts(LearningPath $learningPath)
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $conditions = array();
        $conditions[] = $this->getConditionForTargetUsersForLearningPath($learningPath);

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable($learningPathAttemptClassName, LearningPathAttempt::PROPERTY_ID),
            ComparisonCondition::GREATER_THAN_OR_EQUAL,
            new StaticConditionVariable(0)
        );

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable($learningPathAttemptClassName, LearningPathAttempt::PROPERTY_ID),
            ComparisonCondition::LESS_THAN,
            new StaticConditionVariable(100)
        );

        $condition = new AndCondition($conditions);
        $joins = $this->getJoinsForTargetUsersWithLearningPathAttempts($learningPath);

        return $this->dataClassRepository->count(
            User::class_name(), new DataClassCountParameters($condition, $joins)
        );
    }

    /**
     * Counts the total number of target users for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsers(LearningPath $learningPath)
    {
        return count($this->learningPathTrackingParameters->getLearningPathTargetUserIds($learningPath));
    }

    /**
     * Returns the joins object needed to left join the User class with the LearningPathAttempt for a given
     * learning path
     *
     * @param LearningPath $learningPath
     *
     * @return Joins
     */
    protected function getJoinsForTargetUsersWithLearningPathAttempts(LearningPath $learningPath)
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $joinConditions = array();
        $joinConditions[] = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath);
        $joinConditions[] = $this->getConditionForLearningPathAttemptWithUser();
        $joinCondition = new AndCondition($joinConditions);

        $joins = new Joins();
        $joins->add(new Join($learningPathAttemptClassName, $joinCondition, Join::TYPE_LEFT));

        return $joins;
    }

    /**
     * Returns the conditions needed for the target users for a learning path
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return Condition
     */
    protected function getConditionForTargetUsersForLearningPath(
        LearningPath $learningPath, Condition $condition = null
    )
    {
        $targetUserIds = $this->learningPathTrackingParameters->getLearningPathTargetUserIds($learningPath);

        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $targetUserIds
        );

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }

    /**
     * Returns the condition to join the LearningPathAttempt class with the User class
     *
     * @return EqualityCondition
     */
    protected function getConditionForLearningPathAttemptWithUser()
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        return new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
            new PropertyConditionVariable(
                $learningPathAttemptClassName, LearningPathAttempt::PROPERTY_USER_ID
            )
        );
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

    /**
     * Retrieves all the LearningPathAttempt objects with the LearningPathChildAttempt objects and
     * LearningPathQuestionAttempt objects for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return RecordIterator
     */
    public function findLearningPathAttemptsWithLearningPathChildAttemptsAndLearningPathQuestionAttempts(
        LearningPath $learningPath
    )
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $learningPathChildAttemptClassName =
            $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName();

        $learningPathQuestionAttemptClassName =
            $this->learningPathTrackingParameters->getLearningPathQuestionAttemptClassName();

        $properties = new DataClassProperties();

        $properties->add(
            new FixedPropertyConditionVariable(
                $learningPathAttemptClassName, LearningPathAttempt::PROPERTY_ID, 'learning_path_attempt_id'
            )
        );

        $learningPathAttemptProperties = array(
            LearningPathAttempt::PROPERTY_USER_ID, LearningPathAttempt::PROPERTY_LEARNING_PATH_ID,
            LearningPathAttempt::PROPERTY_PROGRESS
        );

        foreach ($learningPathAttemptProperties as $learningPathAttemptProperty)
        {
            $properties->add(
                new PropertyConditionVariable($learningPathAttemptClassName, $learningPathAttemptProperty)
            );
        }

        $properties->add(
            new FixedPropertyConditionVariable(
                $learningPathChildAttemptClassName, LearningPathChildAttempt::PROPERTY_ID,
                'learning_path_child_attempt_id'
            )
        );

        $learningPathChildAttemptProperties = array(
            LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ITEM_ID, LearningPathChildAttempt::PROPERTY_START_TIME,
            LearningPathChildAttempt::PROPERTY_TOTAL_TIME, LearningPathChildAttempt::PROPERTY_SCORE,
            LearningPathChildAttempt::PROPERTY_STATUS
        );

        foreach ($learningPathChildAttemptProperties as $learningPathChildAttemptProperty)
        {
            $properties->add(
                new PropertyConditionVariable($learningPathChildAttemptClassName, $learningPathChildAttemptProperty)
            );
        }

        $properties->add(
            new FixedPropertyConditionVariable(
                $learningPathQuestionAttemptClassName, LearningPathQuestionAttempt::PROPERTY_ID,
                'learning_path_question_attempt_id'
            )
        );

        $learningPathQuestionAttemptProperties = array(
            LearningPathQuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID, LearningPathQuestionAttempt::PROPERTY_ANSWER,
            LearningPathQuestionAttempt::PROPERTY_FEEDBACK, LearningPathQuestionAttempt::PROPERTY_SCORE,
            LearningPathQuestionAttempt::PROPERTY_HINT
        );

        foreach ($learningPathQuestionAttemptProperties as $learningPathQuestionAttemptProperty)
        {
            $properties->add(
                new PropertyConditionVariable(
                    $learningPathQuestionAttemptClassName, $learningPathQuestionAttemptProperty
                )
            );
        }

        $joins = new Joins();
        $joins->add($this->getJoinForLearningPathAttemptWithLearningPathChildAttempt());
        $joins->add($this->getJoinForLearningPathChildAttemptWithLearningPathQuestionAttempt());

        $condition = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath);

        return $this->dataClassRepository->records(
            $learningPathAttemptClassName,
            new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins)
        );
    }

    /**
     * Builds a Join object between LearningPathAttempt and LearningPathChildAttempt
     *
     * @return Join
     */
    protected function getJoinForLearningPathAttemptWithLearningPathChildAttempt()
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $learningPathChildAttemptClassName =
            $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName();

        return new Join(
            $learningPathChildAttemptClassName,
            new EqualityCondition(
                new PropertyConditionVariable($learningPathAttemptClassName, LearningPathAttempt::PROPERTY_ID),
                new PropertyConditionVariable(
                    $learningPathChildAttemptClassName, LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
                )

            )
        );
    }

    /**
     * Builds a Join object between LearningPathChildAttempt and LearningPathQuestionAttempt
     *
     * @return Join
     */
    protected function getJoinForLearningPathChildAttemptWithLearningPathQuestionAttempt()
    {
        $learningPathChildAttemptClassName =
            $this->learningPathTrackingParameters->getLearningPathChildAttemptClassName();

        $learningPathQuestionAttemptClassName =
            $this->learningPathTrackingParameters->getLearningPathQuestionAttemptClassName();

        return new Join(
            $learningPathQuestionAttemptClassName,
            new EqualityCondition(
                new PropertyConditionVariable(
                    $learningPathChildAttemptClassName, LearningPathChildAttempt::PROPERTY_ID
                ),
                new PropertyConditionVariable(
                    $learningPathQuestionAttemptClassName, LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID
                )

            )
        );
    }

}