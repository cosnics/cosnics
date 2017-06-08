<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
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
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingRepository extends CommonDataClassRepository implements TrackingRepositoryInterface
{
    /**
     * @var TrackingParametersInterface
     */
    protected $trackingParameters;

    /**
     * TrackingRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     * @param TrackingParametersInterface $trackingParameters
     */
    public function __construct(
        DataClassRepository $dataClassRepository,
        TrackingParametersInterface $trackingParameters
    )
    {
        parent::__construct($dataClassRepository);

        $this->trackingParameters = $trackingParameters;
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

        $customConditions = $this->trackingParameters->getLearningPathAttemptConditions();
        if ($customConditions)
        {
            $conditions[] = $customConditions;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getLearningPathAttemptClassName(),
                LearningPathAttempt::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($learningPath->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getLearningPathAttemptClassName(),
                LearningPathAttempt::PROPERTY_USER_ID
            ),
            new StaticConditionVariable($user->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            $this->trackingParameters->getLearningPathAttemptClassName(),
            new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearLearningPathAttemptCache()
    {
        $learningPathAttemptClassName = $this->trackingParameters->getLearningPathAttemptClassName();

        $this->dataClassRepository->getDataClassRepositoryCache()->truncate($learningPathAttemptClassName);
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearTreeNodeAttemptCache()
    {
        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $this->dataClassRepository->getDataClassRepositoryCache()->truncate($treeNodeAttemptClassName);
    }

    /**
     * Finds the learning path child attempts for a given learning path attempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return TreeNodeAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodeAttempts(LearningPathAttempt $learningPathAttempt)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getTreeNodeAttemptClassName(),
                TreeNodeAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttempt->getId())
        );

        return $this->dataClassRepository->retrieves(
            $this->trackingParameters->getTreeNodeAttemptClassName(),
            new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Finds all the TreeNodeAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | TreeNodeAttempt[]
     */
    public function findTreeNodeAttemptsForLearningPath(LearningPath $learningPath)
    {
        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $learningPathAttemptClassName =
            $this->trackingParameters->getLearningPathAttemptClassName();

        $condition = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath);

        $joins = new Joins();
        $joins->add($this->getJoinForLearningPathAttemptWithTreeNodeAttempt($learningPathAttemptClassName));

        $parameters = new DataClassRetrievesParameters($condition, null, null, array(), $joins);

        return $this->dataClassRepository->retrieves($treeNodeAttemptClassName, $parameters);
    }

    /**
     * Finds a TreeNodeAttempt by a given LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeAttempt | DataClass
     */
    public function findActiveTreeNodeAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getTreeNodeAttemptClassName(),
                TreeNodeAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttempt->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getTreeNodeAttemptClassName(),
                TreeNodeAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
            ),
            new StaticConditionVariable($treeNode->getId())
        );

        $conditions[] = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(
                    $this->trackingParameters->getTreeNodeAttemptClassName(),
                    TreeNodeAttempt::PROPERTY_STATUS
                ),
                array(TreeNodeAttempt::STATUS_COMPLETED, TreeNodeAttempt::STATUS_PASSED)
            )
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            $this->trackingParameters->getTreeNodeAttemptClassName(),
            new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Finds a TreeNodeAttempt by a given ID
     *
     * @param int $treeNodeAttemptId
     *
     * @return DataClass | TreeNodeAttempt
     */
    public function findTreeNodeAttemptById($treeNodeAttemptId)
    {
        return $this->dataClassRepository->retrieveById(
            $this->trackingParameters->getTreeNodeAttemptClassName(), $treeNodeAttemptId
        );
    }

    /**
     * Finds a LearningPathAttempt by a given ID
     *
     * @param int $learningPathAttemptId
     *
     * @return DataClass | LearningPathAttempt
     */
    public function findLearningPathAttemptById($learningPathAttemptId)
    {
        return $this->dataClassRepository->retrieveById(
            $this->trackingParameters->getLearningPathAttemptClassName(), $learningPathAttemptId
        );
    }

    /**
     * Finds the LearningPathQuestionAttempt objects for a given TreeNodeAttempt
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     *
     * @return LearningPathQuestionAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathQuestionAttempts(TreeNodeAttempt $treeNodeAttempt)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getLearningPathQuestionAttemptClassName(),
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID
            ),
            new StaticConditionVariable($treeNodeAttempt->getId())
        );

        return $this->dataClassRepository->retrieves(
            $this->trackingParameters->getLearningPathQuestionAttemptClassName(),
            new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Finds the LearningPathAttempt objects for a given LearningPath with a given condition, offset, count and orderBy
     * Joined with users for searching and sorting
     *
     * @param LearningPath $learningPath
     * @param int[] $treeNodeDataIds
     * @param Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return RecordIterator
     */
    public function findLearningPathAttemptsWithUser(
        LearningPath $learningPath, $treeNodeDataIds = array(),
        Condition $condition = null, $offset = 0, $count = 0, $orderBy = array()
    )
    {
        $learningPathAttemptClassName = $this->trackingParameters->getLearningPathAttemptClassName();
        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $properties = $this->getPropertiesForLearningPathAttemptsWithUser();
        $joins = $this->getJoinsForLearningPathAttemptsWithUser();
        $condition = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath, $condition);

        $joins->add(
            $this->getJoinForLearningPathAttemptWithTreeNodeAttempt(
                $treeNodeAttemptClassName,
                Join::TYPE_LEFT,
                $this->getConditionForCompletedTreeNodesData($treeNodeDataIds)
            )
        );

        $groupBy = $this->getGroupByUserId();

        $parameters =
            new RecordRetrievesParameters($properties, $condition, $count, $offset, $orderBy, $joins, $groupBy);

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
        $learningPathAttemptClassName = $this->trackingParameters->getLearningPathAttemptClassName();

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
        $learningPathAttemptClassName = $this->trackingParameters->getLearningPathAttemptClassName();
        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $properties = new DataClassProperties();

        $properties->add(new PropertiesConditionVariable($learningPathAttemptClassName));
        $properties->add(new FixedPropertyConditionVariable(User::class_name(), User::PROPERTY_ID, 'user_id'));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL));

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                        $treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
                    )
                ),
                'nodes_completed'
            )
        );

        return $properties;
    }

    /**
     * Finds the targeted users (left) joined with the learning path attempts
     *
     * @param LearningPath $learningPath
     * @param array $treeNodeDataIds
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return RecordIterator
     */
    public function findTargetUsersWithLearningPathAttempts(
        LearningPath $learningPath, $treeNodeDataIds = array(),
        Condition $condition = null, $offset = 0, $count = 0, $orderBy = array()
    )
    {
        $properties = $this->getPropertiesForLearningPathAttemptsWithUser();
        $condition = $this->getConditionForTargetUsersForLearningPath($learningPath, $condition);

        $joins = $this->getJoinsForTargetUsersWithLearningPathAndTreeNodeAttempts(
            $learningPath, $treeNodeDataIds
        );

        $groupBy = $this->getGroupByUserId();

        return $this->dataClassRepository->records(
            User::class_name(), new RecordRetrievesParameters(
                $properties, $condition, $count, $offset, $orderBy, $joins, $groupBy
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
     * Finds the target users with the completed nodes for a given learning path, limiting it by the given nodes
     *
     * @param LearningPath $learningPath
     * @param int[] $treeNodeDataIds
     *
     * @return RecordIterator
     */
    public function findUsersWithCompletedNodesCount(
        LearningPath $learningPath, $treeNodeDataIds = array()
    )
    {
        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $properties = new DataClassProperties();

        $properties->add(new FixedPropertyConditionVariable(User::class_name(), User::PROPERTY_ID, 'user_id'));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL));

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                        $treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
                    )
                ),
                'nodes_completed'
            )
        );

        $condition = $this->getConditionForTargetUsersForLearningPath($learningPath);

        $joins = $this->getJoinsForTargetUsersWithLearningPathAndTreeNodeAttempts(
            $learningPath, $treeNodeDataIds
        );

        $groupBy = $this->getGroupByUserId();

        return $this->dataClassRepository->records(
            User::class_name(),
            new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins, $groupBy)
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
        return count($this->trackingParameters->getLearningPathTargetUserIds($learningPath));
    }

    /**
     * Returns the joins for the target users with the LearningPathAttempt and TreeNodeAttempt classes
     * based on the given LearningPath and TreeNodeData identifiers
     *
     * @param LearningPath $learningPath
     * @param int[] $treeNodeDataIds
     *
     * @return Joins
     */
    protected function getJoinsForTargetUsersWithLearningPathAndTreeNodeAttempts(
        LearningPath $learningPath, $treeNodeDataIds = array()
    )
    {
        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $joins = $this->getJoinsForTargetUsersWithLearningPathAttempts($learningPath);

        $joins->add(
            $this->getJoinForLearningPathAttemptWithTreeNodeAttempt(
                $treeNodeAttemptClassName, Join::TYPE_LEFT,
                $this->getConditionForCompletedTreeNodesData($treeNodeDataIds)
            )
        );

        return $joins;
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
        $learningPathAttemptClassName = $this->trackingParameters->getLearningPathAttemptClassName();

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
        $targetUserIds = $this->trackingParameters->getLearningPathTargetUserIds($learningPath);

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
        $learningPathAttemptClassName = $this->trackingParameters->getLearningPathAttemptClassName();

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
        $learningPathAttemptClassName = $this->trackingParameters->getLearningPathAttemptClassName();

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $learningPathAttemptClassName, LearningPathAttempt::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($learningPath->getId())
        );

        $customCondition = $this->trackingParameters->getLearningPathAttemptConditions();
        if ($customCondition instanceof Condition)
        {
            $conditions[] = $customCondition;
        }

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    /**
     * Retrieves all the LearningPathAttempt objects with the TreeNodeAttempt objects and
     * LearningPathQuestionAttempt objects for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return RecordIterator
     */
    public function findLearningPathAttemptsWithTreeNodeAttemptsAndLearningPathQuestionAttempts(
        LearningPath $learningPath
    )
    {
        $learningPathAttemptClassName = $this->trackingParameters->getLearningPathAttemptClassName();

        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $learningPathQuestionAttemptClassName =
            $this->trackingParameters->getLearningPathQuestionAttemptClassName();

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
                $treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_ID,
                'tree_node_attempt_id'
            )
        );

        $treeNodeAttemptProperties = array(
            TreeNodeAttempt::PROPERTY_LEARNING_PATH_ITEM_ID, TreeNodeAttempt::PROPERTY_START_TIME,
            TreeNodeAttempt::PROPERTY_TOTAL_TIME, TreeNodeAttempt::PROPERTY_SCORE,
            TreeNodeAttempt::PROPERTY_STATUS
        );

        foreach ($treeNodeAttemptProperties as $treeNodeAttemptProperty)
        {
            $properties->add(
                new PropertyConditionVariable($treeNodeAttemptClassName, $treeNodeAttemptProperty)
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

        $joins->add(
            $this->getJoinForLearningPathAttemptWithTreeNodeAttempt($treeNodeAttemptClassName)
        );

        $joins->add($this->getJoinForTreeNodeAttemptWithLearningPathQuestionAttempt());

        $condition = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath);

        return $this->dataClassRepository->records(
            $learningPathAttemptClassName,
            new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins)
        );
    }

    /**
     * Returns the condition to retrieve completed TreeNodeData objects (limited to the given TreeNodeData ids)
     *
     * @param int[] $treeNodeDataIds
     *
     * @return AndCondition
     */
    protected function getConditionForCompletedTreeNodesData($treeNodeDataIds = array())
    {
        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                $treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_STATUS
            ),
            array(TreeNodeAttempt::STATUS_COMPLETED, TreeNodeAttempt::STATUS_PASSED)
        );

        if (!empty($treeNodeDataIds) && is_array($treeNodeDataIds))
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(
                    $treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
                ),
                $treeNodeDataIds
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * Builds a Join object between LearningPathAttempt and TreeNodeAttempt
     *
     * @param string $joinWithClass - The class to join with, depends on what the base class in the select is
     * @param int $joinType
     * @param Condition|null $joinCondition
     *
     * @return Join
     */
    protected function getJoinForLearningPathAttemptWithTreeNodeAttempt(
        $joinWithClass, $joinType = Join::TYPE_NORMAL, Condition $joinCondition = null
    )
    {
        $learningPathAttemptClassName = $this->trackingParameters->getLearningPathAttemptClassName();

        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $joinConditions = array();

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable($learningPathAttemptClassName, LearningPathAttempt::PROPERTY_ID),
            new PropertyConditionVariable(
                $treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            )

        );

        if ($joinCondition instanceof Condition)
        {
            $joinConditions[] = $joinCondition;
        }

        $joinCondition = new AndCondition($joinConditions);

        return new Join($joinWithClass, $joinCondition, $joinType);
    }

    /**
     * Builds a Join object between TreeNodeAttempt and LearningPathQuestionAttempt
     *
     * @return Join
     */
    protected function getJoinForTreeNodeAttemptWithLearningPathQuestionAttempt()
    {
        $treeNodeAttemptClassName =
            $this->trackingParameters->getTreeNodeAttemptClassName();

        $learningPathQuestionAttemptClassName =
            $this->trackingParameters->getLearningPathQuestionAttemptClassName();

        return new Join(
            $learningPathQuestionAttemptClassName,
            new EqualityCondition(
                new PropertyConditionVariable(
                    $treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_ID
                ),
                new PropertyConditionVariable(
                    $learningPathQuestionAttemptClassName, LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID
                )

            )
        );
    }

    /**
     * Creates the GroupBy object to group by the identifier of the users
     *
     * @return GroupBy
     */
    protected function getGroupByUserId(): GroupBy
    {
        return new GroupBy(array(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID)));
    }

}