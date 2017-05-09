<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
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
    public function clearLearningPathChildAttemptCache();

    /**
     * Finds the learning path child attempts for a given learning path attempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return LearningPathChildAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathChildAttempts(LearningPathAttempt $learningPathAttempt);

    /**
     * Finds all the LearningPathChildAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | LearningPathChildAttempt[]
     */
    public function findLearningPathChildAttemptsForLearningPath(LearningPath $learningPath);

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
    );

    /**
     * Finds a LearningPathChildAttempt by a given ID
     *
     * @param int $learningPathChildAttemptId
     *
     * @return DataClass | LearningPathChildAttempt
     */
    public function findLearningPathChildAttemptById($learningPathChildAttemptId);

    /**
     * Finds the LearningPathQuestionAttempt objects for a given LearningPathChildAttempt
     *
     * @param LearningPathChildAttempt $learningPathChildAttempt
     *
     * @return LearningPathQuestionAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathQuestionAttempts(LearningPathChildAttempt $learningPathChildAttempt);

    /**
     * Finds the LearningPathAttempt objects for a given LearningPath with a given condition, offset, count and orderBy
     * Joined with users for searching and sorting
     *
     * @param LearningPath $learningPath
     * @param int[] $learningPathChildIds
     * @param Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return RecordIterator
     */
    public function findLearningPathAttemptsWithUser(
        LearningPath $learningPath, $learningPathChildIds = array(),
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
     * @param array $learningPathChildIds
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return RecordIterator
     */
    public function findTargetUsersWithLearningPathAttempts(
        LearningPath $learningPath, $learningPathChildIds = array(),
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
     * Finds the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithoutLearningPathAttempts(LearningPath $learningPath);

    /**
     * Counts the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithoutLearningPathAttempts(LearningPath $learningPath);

    /**
     * Finds the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithPartialLearningPathAttempts(LearningPath $learningPath);

    /**
     * Counts the target users with attempts on a learning path that are completed
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithFullLearningPathAttempts(LearningPath $learningPath);

    /**
     * Counts the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithPartialLearningPathAttempts(LearningPath $learningPath);

    /**
     * Counts the total number of target users for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsers(LearningPath $learningPath);

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
    );
}