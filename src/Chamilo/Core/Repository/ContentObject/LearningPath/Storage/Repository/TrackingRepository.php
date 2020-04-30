<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingRepository extends CommonDataClassRepository implements TrackingRepositoryInterface
{

    /**
     *
     * @var TrackingParametersInterface
     */
    protected $trackingParameters;

    /**
     * TrackingRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     * @param TrackingParametersInterface $trackingParameters
     */
    public function __construct(DataClassRepository $dataClassRepository,
        TrackingParametersInterface $trackingParameters)
    {
        parent::__construct($dataClassRepository);

        $this->trackingParameters = $trackingParameters;
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearTreeNodeAttemptCache()
    {
        $treeNodeAttemptClassName = $this->trackingParameters->getTreeNodeAttemptClassName();

        $this->dataClassRepository->getDataClassRepositoryCache()->truncate($treeNodeAttemptClassName);
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearTreeNodeQuestionAttemptCache()
    {
        $this->dataClassRepository->getDataClassRepositoryCache()->truncate(
            $this->trackingParameters->getTreeNodeQuestionAttemptClassName()
        );
    }

    /**
     * Finds the learning path child attempts for a given learning path attempt
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return TreeNodeAttempt[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodeAttempts(LearningPath $learningPath, User $user)
    {
        $condition = $this->getConditionForTreeNodeAttemptsForLearningPathAndUser($learningPath, $user);

        return $this->dataClassRepository->retrieves(
            $this->trackingParameters->getTreeNodeAttemptClassName(),
            new DataClassRetrievesParameters($condition));
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
        $condition = $this->getConditionForTreeNodeAttemptsForLearningPath($learningPath);

        return $this->dataClassRepository->retrieves(
            $this->trackingParameters->getTreeNodeAttemptClassName(),
            new DataClassRetrievesParameters($condition));
    }

    /**
     * Finds a TreeNodeAttempt by a given LearningPathAttempt and TreeNode
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     *
     * @return TreeNodeAttempt|DataClass
     */
    public function findActiveTreeNodeAttempt(LearningPath $learningPath, TreeNode $treeNode, User $user)
    {
        $conditions = array();

        $conditions[] = $this->getConditionForTreeNodeAttemptsForLearningPathAndUser($learningPath, $user);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getTreeNodeAttemptClassName(),
                TreeNodeAttempt::PROPERTY_TREE_NODE_DATA_ID),
            new StaticConditionVariable($treeNode->getId()));

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    $this->trackingParameters->getTreeNodeAttemptClassName(),
                    TreeNodeAttempt::PROPERTY_COMPLETED),
                new StaticConditionVariable(1)));

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            $this->trackingParameters->getTreeNodeAttemptClassName(),
            new DataClassRetrieveParameters($condition));
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
            $this->trackingParameters->getTreeNodeAttemptClassName(),
            $treeNodeAttemptId);
    }

    /**
     * Finds the TreeNodeQuestionAttempt objects for a given TreeNodeAttempt
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     *
     * @return TreeNodeQuestionAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodeQuestionAttempts(TreeNodeAttempt $treeNodeAttempt)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getTreeNodeQuestionAttemptClassName(),
                TreeNodeQuestionAttempt::PROPERTY_TREE_NODE_ATTEMPT_ID),
            new StaticConditionVariable($treeNodeAttempt->getId()));

        return $this->dataClassRepository->retrieves(
            $this->trackingParameters->getTreeNodeQuestionAttemptClassName(),
            new DataClassRetrievesParameters($condition));
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
     * @return DataClassIterator
     */
    public function findLearningPathAttemptsWithUser(LearningPath $learningPath, $treeNodeDataIds = array(),
        Condition $condition = null, $offset = 0, $count = 0, $orderBy = array())
    {
        $parameters = new RecordRetrievesParameters(
            $this->getPropertiesForLearningPathAttemptsWithUser(),
            $condition,
            $count,
            $offset,
            $orderBy,
            $this->getJoinsForTreeNodeAttemptsWithUser($learningPath, $treeNodeDataIds),
            $this->getGroupByUserId());

        return $this->dataClassRepository->records(
            $this->trackingParameters->getTreeNodeAttemptClassName(),
            $parameters);
    }

    /**
     * Counts the learning path attempts joined with users for searching
     *
     * @param LearningPath $learningPath
     * @param int[] $treeNodeDataIds
     * @param Condition $condition
     *
     * @return int
     */
    public function countLearningPathAttemptsWithUser(LearningPath $learningPath, $treeNodeDataIds = array(),
        Condition $condition = null)
    {
        $parameters = new DataClassCountParameters(
            $condition,
            $this->getJoinsForTreeNodeAttemptsWithUser($learningPath, $treeNodeDataIds),
            new DataClassProperties(array(new FunctionConditionVariable(
                FunctionConditionVariable::DISTINCT,
                new PropertyConditionVariable(
                    $this->trackingParameters->getTreeNodeAttemptClassName(),
                    TreeNodeAttempt::PROPERTY_USER_ID)))));

        return $this->dataClassRepository->count($this->trackingParameters->getTreeNodeAttemptClassName(), $parameters);
    }

    /**
     * Returns the joins object needed to join the TreeNodeAttempt class with the User class
     *
     * @param LearningPath $learningPath
     * @param int[] $treeNodeDataIds
     * @param int $joinType
     *
     * @return Joins
     */
    protected function getJoinsForTreeNodeAttemptsWithUser(LearningPath $learningPath, $treeNodeDataIds = array(),
        $joinType = Join::TYPE_NORMAL)
    {
        $joinConditions = array();

        $joinConditions[] = $this->getConditionForTreeNodeAttemptWithUser();
        $joinConditions[] = $this->getConditionForTreeNodeAttemptsForLearningPath($learningPath);
        $joinConditions[] = $this->getConditionForCompletedTreeNodesData($treeNodeDataIds);

        $joinCondition = new AndCondition($joinConditions);

        $joins = new Joins();
        $joins->add(new Join(User::class, $joinCondition, $joinType));

        return $joins;
    }

    /**
     * Returns the properties needed to retrieve the data for learning path attempts with users
     *
     * @return DataClassProperties
     */
    protected function getPropertiesForLearningPathAttemptsWithUser()
    {
        $properties = new DataClassProperties();

        $properties->add(new FixedPropertyConditionVariable(User::class, User::PROPERTY_ID, 'user_id'));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL));

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT,
                    new PropertyConditionVariable(
                        $this->trackingParameters->getTreeNodeAttemptClassName(),
                        TreeNodeAttempt::PROPERTY_TREE_NODE_DATA_ID)),
                'nodes_completed'));

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
     * @return DataClassIterator
     */
    public function findTargetUsersWithLearningPathAttempts(LearningPath $learningPath, $treeNodeDataIds = array(),
        Condition $condition = null, $offset = 0, $count = 0, $orderBy = array())
    {
        $properties = $this->getPropertiesForLearningPathAttemptsWithUser();
        $condition = $this->getConditionForTargetUsersForLearningPath($learningPath, $condition);
        $joins = $this->getJoinsForTreeNodeAttemptsWithUser($learningPath, $treeNodeDataIds, Join::TYPE_RIGHT);
        $groupBy = $this->getGroupByUserId();

        return $this->dataClassRepository->records(
            $this->trackingParameters->getTreeNodeAttemptClassName(),
            new RecordRetrievesParameters($properties, $condition, $count, $offset, $orderBy, $joins, $groupBy));
    }

    /**
     * Counts the targeted users
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersForLearningPath(LearningPath $learningPath, Condition $condition = null)
    {
        if (empty($condition))
        {
            return count($this->trackingParameters->getLearningPathTargetUserIds($learningPath));
        }

        $condition = $this->getConditionForTargetUsersForLearningPath($learningPath, $condition);

        return $this->dataClassRepository->count(User::class, new DataClassCountParameters($condition));
    }

    /**
     * Returns the conditions needed for the target users for a learning path
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return Condition
     */
    protected function getConditionForTargetUsersForLearningPath(LearningPath $learningPath, Condition $condition = null)
    {
        $targetUserIds = $this->trackingParameters->getLearningPathTargetUserIds($learningPath);

        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ID),
            $targetUserIds);

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
    protected function getConditionForTreeNodeAttemptWithUser()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ID),
            new PropertyConditionVariable(
                $this->trackingParameters->getTreeNodeAttemptClassName(),
                TreeNodeAttempt::PROPERTY_USER_ID));
    }

    /**
     * Returns the condition needed to retrieve TreeNodeAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return AndCondition
     */
    protected function getConditionForTreeNodeAttemptsForLearningPath(LearningPath $learningPath)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getTreeNodeAttemptClassName(),
                TreeNodeAttempt::PROPERTY_LEARNING_PATH_ID),
            new StaticConditionVariable($learningPath->getId()));

        $customCondition = $this->trackingParameters->getTreeNodeAttemptConditions();
        if ($customCondition)
        {
            $conditions[] = $customCondition;
        }

        return new AndCondition($conditions);
    }

    /**
     * Returns the condition needed to retrieve TreeNodeAttempt objects for a given LearningPath and User
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return AndCondition
     */
    protected function getConditionForTreeNodeAttemptsForLearningPathAndUser(LearningPath $learningPath, User $user)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->trackingParameters->getTreeNodeAttemptClassName(),
                TreeNodeAttempt::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId()));

        $conditions[] = $this->getConditionForTreeNodeAttemptsForLearningPath($learningPath);

        return new AndCondition($conditions);
    }

    /**
     * Retrieves all the LearningPathAttempt objects with the TreeNodeAttempt objects and
     * TreeNodeQuestionAttempt objects for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return DataClassIterator
     */
    public function findLearningPathAttemptsWithTreeNodeAttemptsAndTreeNodeQuestionAttempts(LearningPath $learningPath)
    {
        $treeNodeAttemptClassName = $this->trackingParameters->getTreeNodeAttemptClassName();

        $treeNodeQuestionAttemptClassName = $this->trackingParameters->getTreeNodeQuestionAttemptClassName();

        $properties = new DataClassProperties();

        $properties->add(
            new FixedPropertyConditionVariable(
                $treeNodeAttemptClassName,
                TreeNodeAttempt::PROPERTY_ID,
                'tree_node_attempt_id'));

        $treeNodeAttemptProperties = array(
            TreeNodeAttempt::PROPERTY_USER_ID,
            TreeNodeAttempt::PROPERTY_LEARNING_PATH_ID,
            TreeNodeAttempt::PROPERTY_TREE_NODE_DATA_ID,
            TreeNodeAttempt::PROPERTY_START_TIME,
            TreeNodeAttempt::PROPERTY_TOTAL_TIME,
            TreeNodeAttempt::PROPERTY_SCORE,
            TreeNodeAttempt::PROPERTY_COMPLETED);

        foreach ($treeNodeAttemptProperties as $treeNodeAttemptProperty)
        {
            $properties->add(new PropertyConditionVariable($treeNodeAttemptClassName, $treeNodeAttemptProperty));
        }

        $properties->add(
            new FixedPropertyConditionVariable(
                $treeNodeQuestionAttemptClassName,
                TreeNodeQuestionAttempt::PROPERTY_ID,
                'tree_node_question_attempt_id'));

        $treeNodeQuestionAttemptProperties = array(
            TreeNodeQuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID,
            TreeNodeQuestionAttempt::PROPERTY_ANSWER,
            TreeNodeQuestionAttempt::PROPERTY_FEEDBACK,
            TreeNodeQuestionAttempt::PROPERTY_SCORE,
            TreeNodeQuestionAttempt::PROPERTY_HINT);

        foreach ($treeNodeQuestionAttemptProperties as $treeNodeQuestionAttemptProperty)
        {
            $properties->add(
                new PropertyConditionVariable($treeNodeQuestionAttemptClassName, $treeNodeQuestionAttemptProperty));
        }

        $joins = new Joins();
        $joins->add($this->getJoinForTreeNodeAttemptWithTreeNodeQuestionAttempt());

        $condition = $this->getConditionForTreeNodeAttemptsForLearningPath($learningPath);

        return $this->dataClassRepository->records(
            $treeNodeAttemptClassName,
            new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins));
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
        $treeNodeAttemptClassName = $this->trackingParameters->getTreeNodeAttemptClassName();

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_COMPLETED),
            new StaticConditionVariable(1));

        if (! empty($treeNodeDataIds) && is_array($treeNodeDataIds))
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable($treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_TREE_NODE_DATA_ID),
                $treeNodeDataIds);
        }

        return new AndCondition($conditions);
    }

    /**
     * Builds a Join object between TreeNodeAttempt and TreeNodeQuestionAttempt
     *
     * @return Join
     */
    protected function getJoinForTreeNodeAttemptWithTreeNodeQuestionAttempt()
    {
        $treeNodeAttemptClassName = $this->trackingParameters->getTreeNodeAttemptClassName();

        $treeNodeQuestionAttemptClassName = $this->trackingParameters->getTreeNodeQuestionAttemptClassName();

        return new Join(
            $treeNodeQuestionAttemptClassName,
            new EqualityCondition(
                new PropertyConditionVariable($treeNodeAttemptClassName, TreeNodeAttempt::PROPERTY_ID),
                new PropertyConditionVariable(
                    $treeNodeQuestionAttemptClassName,
                    TreeNodeQuestionAttempt::PROPERTY_TREE_NODE_ATTEMPT_ID))
            );
    }

    /**
     * Creates the GroupBy object to group by the identifier of the users
     *
     * @return GroupBy
     */
    protected function getGroupByUserId(): GroupBy
    {
        return new GroupBy(array(new PropertyConditionVariable(User::class, User::PROPERTY_ID)));
    }
}