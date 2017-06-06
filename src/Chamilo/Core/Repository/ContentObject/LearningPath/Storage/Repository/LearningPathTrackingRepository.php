<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeDataAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTrackingParametersInterface;
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
    public function clearTreeNodeDataAttemptCache()
    {
        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

        $this->dataClassRepository->getDataClassRepositoryCache()->truncate($treeNodeDataAttemptClassName);
    }

    /**
     * Finds the learning path child attempts for a given learning path attempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return TreeNodeDataAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodeDataAttempts(LearningPathAttempt $learningPathAttempt)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName(),
                TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttempt->getId())
        );

        return $this->dataClassRepository->retrieves(
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName(),
            new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Finds all the TreeNodeDataAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | TreeNodeDataAttempt[]
     */
    public function findTreeNodeDataAttemptsForLearningPath(LearningPath $learningPath)
    {
        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

        $learningPathAttemptClassName =
            $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $condition = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath);

        $joins = new Joins();
        $joins->add($this->getJoinForLearningPathAttemptWithTreeNodeDataAttempt($learningPathAttemptClassName));

        $parameters = new DataClassRetrievesParameters($condition, null, null, array(), $joins);

        return $this->dataClassRepository->retrieves($treeNodeDataAttemptClassName, $parameters);
    }

    /**
     * Finds a TreeNodeDataAttempt by a given LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeDataAttempt | DataClass
     */
    public function findActiveTreeNodeDataAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName(),
                TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttempt->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName(),
                TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
            ),
            new StaticConditionVariable($treeNode->getId())
        );

        $conditions[] = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(
                    $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName(),
                    TreeNodeDataAttempt::PROPERTY_STATUS
                ),
                array(TreeNodeDataAttempt::STATUS_COMPLETED, TreeNodeDataAttempt::STATUS_PASSED)
            )
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName(),
            new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Finds a TreeNodeDataAttempt by a given ID
     *
     * @param int $treeNodeDataAttemptId
     *
     * @return DataClass | TreeNodeDataAttempt
     */
    public function findTreeNodeDataAttemptById($treeNodeDataAttemptId)
    {
        return $this->dataClassRepository->retrieveById(
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName(), $treeNodeDataAttemptId
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
            $this->learningPathTrackingParameters->getLearningPathAttemptClassName(), $learningPathAttemptId
        );
    }

    /**
     * Finds the LearningPathQuestionAttempt objects for a given TreeNodeDataAttempt
     *
     * @param TreeNodeDataAttempt $treeNodeDataAttempt
     *
     * @return LearningPathQuestionAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathQuestionAttempts(TreeNodeDataAttempt $treeNodeDataAttempt)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->learningPathTrackingParameters->getLearningPathQuestionAttemptClassName(),
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID
            ),
            new StaticConditionVariable($treeNodeDataAttempt->getId())
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
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();
        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

        $properties = $this->getPropertiesForLearningPathAttemptsWithUser();
        $joins = $this->getJoinsForLearningPathAttemptsWithUser();
        $condition = $this->getConditionForLearningPathAttemptsForLearningPath($learningPath, $condition);

        $joins->add(
            $this->getJoinForLearningPathAttemptWithTreeNodeDataAttempt(
                $treeNodeDataAttemptClassName,
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
        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

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
                        $treeNodeDataAttemptClassName, TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
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

        $joins = $this->getJoinsForTargetUsersWithLearningPathAndTreeNodeDataAttempts(
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
        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

        $properties = new DataClassProperties();

        $properties->add(new FixedPropertyConditionVariable(User::class_name(), User::PROPERTY_ID, 'user_id'));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL));

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                        $treeNodeDataAttemptClassName, TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
                    )
                ),
                'nodes_completed'
            )
        );

        $condition = $this->getConditionForTargetUsersForLearningPath($learningPath);

        $joins = $this->getJoinsForTargetUsersWithLearningPathAndTreeNodeDataAttempts(
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
        return count($this->learningPathTrackingParameters->getLearningPathTargetUserIds($learningPath));
    }

    /**
     * Returns the joins for the target users with the LearningPathAttempt and TreeNodeDataAttempt classes
     * based on the given LearningPath and TreeNodeData identifiers
     *
     * @param LearningPath $learningPath
     * @param int[] $treeNodeDataIds
     *
     * @return Joins
     */
    protected function getJoinsForTargetUsersWithLearningPathAndTreeNodeDataAttempts(
        LearningPath $learningPath, $treeNodeDataIds = array()
    )
    {
        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

        $joins = $this->getJoinsForTargetUsersWithLearningPathAttempts($learningPath);

        $joins->add(
            $this->getJoinForLearningPathAttemptWithTreeNodeDataAttempt(
                $treeNodeDataAttemptClassName, Join::TYPE_LEFT,
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

        $customCondition = $this->learningPathTrackingParameters->getLearningPathAttemptConditions();
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
     * Retrieves all the LearningPathAttempt objects with the TreeNodeDataAttempt objects and
     * LearningPathQuestionAttempt objects for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return RecordIterator
     */
    public function findLearningPathAttemptsWithTreeNodeDataAttemptsAndLearningPathQuestionAttempts(
        LearningPath $learningPath
    )
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

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
                $treeNodeDataAttemptClassName, TreeNodeDataAttempt::PROPERTY_ID,
                'tree_node_data_attempt_id'
            )
        );

        $treeNodeDataAttemptProperties = array(
            TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ITEM_ID, TreeNodeDataAttempt::PROPERTY_START_TIME,
            TreeNodeDataAttempt::PROPERTY_TOTAL_TIME, TreeNodeDataAttempt::PROPERTY_SCORE,
            TreeNodeDataAttempt::PROPERTY_STATUS
        );

        foreach ($treeNodeDataAttemptProperties as $treeNodeDataAttemptProperty)
        {
            $properties->add(
                new PropertyConditionVariable($treeNodeDataAttemptClassName, $treeNodeDataAttemptProperty)
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
            $this->getJoinForLearningPathAttemptWithTreeNodeDataAttempt($treeNodeDataAttemptClassName)
        );

        $joins->add($this->getJoinForTreeNodeDataAttemptWithLearningPathQuestionAttempt());

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
        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                $treeNodeDataAttemptClassName, TreeNodeDataAttempt::PROPERTY_STATUS
            ),
            array(TreeNodeDataAttempt::STATUS_COMPLETED, TreeNodeDataAttempt::STATUS_PASSED)
        );

        if (!empty($treeNodeDataIds) && is_array($treeNodeDataIds))
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(
                    $treeNodeDataAttemptClassName, TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
                ),
                $treeNodeDataIds
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * Builds a Join object between LearningPathAttempt and TreeNodeDataAttempt
     *
     * @param string $joinWithClass - The class to join with, depends on what the base class in the select is
     * @param int $joinType
     * @param Condition|null $joinCondition
     *
     * @return Join
     */
    protected function getJoinForLearningPathAttemptWithTreeNodeDataAttempt(
        $joinWithClass, $joinType = Join::TYPE_NORMAL, Condition $joinCondition = null
    )
    {
        $learningPathAttemptClassName = $this->learningPathTrackingParameters->getLearningPathAttemptClassName();

        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

        $joinConditions = array();

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable($learningPathAttemptClassName, LearningPathAttempt::PROPERTY_ID),
            new PropertyConditionVariable(
                $treeNodeDataAttemptClassName, TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
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
     * Builds a Join object between TreeNodeDataAttempt and LearningPathQuestionAttempt
     *
     * @return Join
     */
    protected function getJoinForTreeNodeDataAttemptWithLearningPathQuestionAttempt()
    {
        $treeNodeDataAttemptClassName =
            $this->learningPathTrackingParameters->getTreeNodeDataAttemptClassName();

        $learningPathQuestionAttemptClassName =
            $this->learningPathTrackingParameters->getLearningPathQuestionAttemptClassName();

        return new Join(
            $learningPathQuestionAttemptClassName,
            new EqualityCondition(
                new PropertyConditionVariable(
                    $treeNodeDataAttemptClassName, TreeNodeDataAttempt::PROPERTY_ID
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