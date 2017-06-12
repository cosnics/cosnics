<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface TrackingRepositoryInterface
{
    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function create(DataClass $dataClass);

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function update(DataClass $dataClass);

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function delete(DataClass $dataClass);

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearTreeNodeAttemptCache();

    /**
     * Finds the learning path child attempts for a given learning path attempt
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return TreeNodeAttempt[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodeAttempts(LearningPath $learningPath, User $user);

    /**
     * Finds all the TreeNodeAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | TreeNodeAttempt[]
     */
    public function findTreeNodeAttemptsForLearningPath(LearningPath $learningPath);

    /**
     * Finds a TreeNodeAttempt by a given LearningPathAttempt and TreeNode
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     *
     * @return TreeNodeAttempt|DataClass
     */
    public function findActiveTreeNodeAttempt(
        LearningPath $learningPath, TreeNode $treeNode, User $user
    );

    /**
     * Finds a TreeNodeAttempt by a given ID
     *
     * @param int $treeNodeAttemptId
     *
     * @return DataClass | TreeNodeAttempt
     */
    public function findTreeNodeAttemptById($treeNodeAttemptId);

    /**
     * Finds the TreeNodeQuestionAttempt objects for a given TreeNodeAttempt
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     *
     * @return TreeNodeQuestionAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodeQuestionAttempts(TreeNodeAttempt $treeNodeAttempt);

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
    );

    /**
     * Counts the learning path attempts joined with users for searching
     *
     * @param LearningPath $learningPath
     * @param int[] $treeNodeDataIds
     * @param Condition $condition
     *
     * @return int
     */
    public function countLearningPathAttemptsWithUser(
        LearningPath $learningPath, $treeNodeDataIds = array(), Condition $condition = null
    );

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
    );

    /**
     * Counts the targeted users for the given learning path (with a condition)
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersForLearningPath(LearningPath $learningPath, Condition $condition = null);

    /**
     * Retrieves all the LearningPathAttempt objects with the TreeNodeAttempt objects and
     * TreeNodeQuestionAttempt objects for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return RecordIterator
     */
    public function findLearningPathAttemptsWithTreeNodeAttemptsAndTreeNodeQuestionAttempts(
        LearningPath $learningPath
    );
}