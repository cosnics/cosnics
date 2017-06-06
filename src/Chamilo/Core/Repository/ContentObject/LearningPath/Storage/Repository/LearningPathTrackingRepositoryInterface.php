<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeDataAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface LearningPathTrackingRepositoryInterface
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
     * Finds a learning path attempt by a given learning path and user
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt | \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLearningPathAttemptForUser(LearningPath $learningPath, User $user);

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearLearningPathAttemptCache();

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearTreeNodeDataAttemptCache();

    /**
     * Finds the learning path child attempts for a given learning path attempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return TreeNodeDataAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodeDataAttempts(LearningPathAttempt $learningPathAttempt);

    /**
     * Finds all the TreeNodeDataAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | TreeNodeDataAttempt[]
     */
    public function findTreeNodeDataAttemptsForLearningPath(LearningPath $learningPath);

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
    );

    /**
     * Finds a TreeNodeDataAttempt by a given ID
     *
     * @param int $treeNodeDataAttemptId
     *
     * @return DataClass | TreeNodeDataAttempt
     */
    public function findTreeNodeDataAttemptById($treeNodeDataAttemptId);

    /**
     * Finds a LearningPathAttempt by a given ID
     *
     * @param int $learningPathAttemptId
     *
     * @return DataClass | LearningPathAttempt
     */
    public function findLearningPathAttemptById($learningPathAttemptId);

    /**
     * Finds the LearningPathQuestionAttempt objects for a given TreeNodeDataAttempt
     *
     * @param TreeNodeDataAttempt $treeNodeDataAttempt
     *
     * @return LearningPathQuestionAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathQuestionAttempts(TreeNodeDataAttempt $treeNodeDataAttempt);

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
     * @param Condition $condition
     *
     * @return int
     */
    public function countLearningPathAttemptsWithUser(LearningPath $learningPath, Condition $condition = null);

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
     * Counts the targeted users (left) joined with the learning path attempts
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersWithLearningPathAttempts(LearningPath $learningPath, Condition $condition = null);

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
    );

    /**
     * Counts the total number of target users for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsers(LearningPath $learningPath);

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
    );
}