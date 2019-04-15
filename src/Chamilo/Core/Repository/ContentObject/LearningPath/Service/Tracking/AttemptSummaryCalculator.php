<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service to calculate summary statistics of attempts
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttemptSummaryCalculator
{
    const STATISTICS_COMPLETED = 0;
    const STATISTICS_STARTED = 1;
    const STATISTICS_NOT_STARTED = 2;

    /**
     * @var string[][][][]
     */
    protected $treeNodeStatisticsCache;

    /**
     * @var AttemptService
     */
    protected $attemptService;

    /**
     * @var TrackingRepositoryInterface
     */
    protected $trackingRepository;

    /**
     * AttemptSummaryCalculator constructor.
     *
     * @param AttemptService $attemptService
     * @param TrackingRepositoryInterface $trackingRepository
     */
    public function __construct(
        AttemptService $attemptService, TrackingRepositoryInterface $trackingRepository
    )
    {
        $this->treeNodeStatisticsCache = [];
        $this->attemptService = $attemptService;
        $this->trackingRepository = $trackingRepository;
    }

    /**
     * Returns the total time spent in the given TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return int|string
     */
    public function getTotalTimeSpentInTreeNode(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $totalTime = 0;

        $treeNodeAttempts = $this->attemptService->getTreeNodeAttemptsForTreeNode($learningPath, $user, $treeNode);
        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            $totalTime += $treeNodeAttempt->get_total_time();
        }

        if ($treeNode->hasChildNodes())
        {
            foreach ($treeNode->getChildNodes() as $childNode)
            {
                $totalTime += $this->getTotalTimeSpentInTreeNode($learningPath, $user, $childNode);
            }
        }

        return $totalTime;
    }

    /**
     * Returns the average score of the given user  in the given TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return float
     */
    public function getAverageScoreInTreeNode(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $totalScore = 0;
        $treeNodeAttempts = $this->attemptService->getTreeNodeAttemptsForTreeNode($learningPath, $user, $treeNode);

        if (count($treeNodeAttempts) == 0)
        {
            return 0;
        }

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            $totalScore += (int) $treeNodeAttempt->get_score();
        }

        return round($totalScore / count($treeNodeAttempts), 2);
    }

    /**
     * Returns the maximum score of the given user in the given TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function getMaximumScoreInTreeNode(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $maximumScore = 0;

        $treeNodeAttempts = $this->attemptService->getTreeNodeAttemptsForTreeNode($learningPath, $user, $treeNode);

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            $maximumScore = $maximumScore < $treeNodeAttempt->get_score() ?
                (int) $treeNodeAttempt->get_score() : $maximumScore;
        }

        return $maximumScore;
    }

    /**
     * Returns the minimum score of the given user in the given TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function getMinimumScoreInTreeNode(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $minimumScore = null;

        $treeNodeAttempts = $this->attemptService->getTreeNodeAttemptsForTreeNode($learningPath, $user, $treeNode);

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            $minimumScore = is_null($minimumScore) || $minimumScore > $treeNodeAttempt->get_score() ?
                (int) $treeNodeAttempt->get_score() : $minimumScore;
        }

        if (is_null($minimumScore))
        {
            $minimumScore = 0;
        }

        return $minimumScore;
    }

//    /**
//     * Returns the score for the last attempt of the given user in the given TreeNode
//     *
//     * @param LearningPath $learningPath
//     * @param User $user
//     * @param TreeNode $treeNode
//     *
//     * @return string
//     */
//    public function getLastAttemptScoreForTreeNode(
//        LearningPath $learningPath, User $user, TreeNode $treeNode
//    )
//    {
//        $treeNodeAttempts = $this->attemptService->getTreeNodeAttemptsForTreeNode($learningPath, $user, $treeNode);
//        $treeNodeAttempt = array_pop($treeNodeAttempts);
//
//        if (!$treeNodeAttempt instanceof TreeNodeAttempt)
//        {
//            return 0;
//        }
//
//        return (int) $treeNodeAttempt->get_score();
//    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return int
     */
    public function getLastCompletedAttemptScoreForTreeNode(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $treeNodeAttempts = $this->attemptService->getTreeNodeAttemptsForTreeNode($learningPath, $user, $treeNode);

        do
        {
            $treeNodeAttempt = array_pop($treeNodeAttempts);

            if ($treeNodeAttempt instanceof TreeNodeAttempt && $treeNodeAttempt->isCompleted())
            {
                return (int) $treeNodeAttempt->get_score();
            }
        }
        while($treeNodeAttempt instanceof TreeNodeAttempt);

        return 0;
    }

    /**
     * Counts the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithoutLearningPathAttempts(
        LearningPath $learningPath, TreeNode $treeNode
    )
    {
        $statistics = $this->getLearningPathStatisticsForTreeNode($learningPath, $treeNode);

        return $statistics[self::STATISTICS_NOT_STARTED];
    }

    /**
     * Counts the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function countTargetUsersWithoutLearningPathAttempts(
        LearningPath $learningPath, TreeNode $treeNode
    )
    {
        $statistics = $this->getLearningPathStatisticsForTreeNode($learningPath, $treeNode);

        return count($statistics[self::STATISTICS_NOT_STARTED]);
    }

    /**
     * Counts the target users with attempts on a learning path that are completed
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function countTargetUsersWithFullLearningPathAttempts(
        LearningPath $learningPath, TreeNode $treeNode
    )
    {
        $statistics = $this->getLearningPathStatisticsForTreeNode($learningPath, $treeNode);

        return count($statistics[self::STATISTICS_COMPLETED]);
    }

    /**
     * Finds the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithPartialLearningPathAttempts(
        LearningPath $learningPath, TreeNode $treeNode
    )
    {
        $statistics = $this->getLearningPathStatisticsForTreeNode($learningPath, $treeNode);

        return $statistics[self::STATISTICS_STARTED];
    }

    /**
     * Counts the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function countTargetUsersWithPartialLearningPathAttempts(
        LearningPath $learningPath, TreeNode $treeNode
    )
    {
        $statistics = $this->getLearningPathStatisticsForTreeNode($learningPath, $treeNode);

        return count($statistics[self::STATISTICS_STARTED]);
    }

    /**
     * Retrieves and calculates the LearningPath statistics for a given TreeNode
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return \string[][][]
     */
    protected function getLearningPathStatisticsForTreeNode(
        LearningPath $learningPath, TreeNode $treeNode
    )
    {
        $cacheKey = md5($learningPath->getId() . ':' . $treeNode->getId());
        if (!array_key_exists($cacheKey, $this->treeNodeStatisticsCache))
        {
            $treeNodeDataIds = $treeNode->getTreeNodeDataIdsFromSelfAndDescendants();

            $usersWithCompletedNodesCount = $this->trackingRepository->findTargetUsersWithLearningPathAttempts(
                $learningPath, $treeNodeDataIds
            );

            foreach ($usersWithCompletedNodesCount as $userWithCompletedNodesCount)
            {
                if ($userWithCompletedNodesCount['nodes_completed'] == count($treeNodeDataIds))
                {
                    $this->treeNodeStatisticsCache[$cacheKey][self::STATISTICS_COMPLETED][] =
                        $userWithCompletedNodesCount;
                }
                elseif ($userWithCompletedNodesCount['nodes_completed'] == 0)
                {
                    $this->treeNodeStatisticsCache[$cacheKey][self::STATISTICS_NOT_STARTED][] =
                        $userWithCompletedNodesCount;
                }
                else
                {
                    $this->treeNodeStatisticsCache[$cacheKey][self::STATISTICS_STARTED][] =
                        $userWithCompletedNodesCount;
                }
            }
        }

        return $this->treeNodeStatisticsCache[$cacheKey];
    }
}