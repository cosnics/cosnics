<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Service to manage the tracking of attempts in a learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingService
{

    /**
     *
     * @var AttemptTrackingService
     */
    protected $attemptTrackingService;

    /**
     *
     * @var AttemptSummaryCalculator
     */
    protected $attemptSummaryCalculator;

    /**
     *
     * @var AssessmentTrackingService
     */
    protected $assessmentTrackingService;

    /**
     *
     * @var ProgressCalculator
     */
    protected $progressCalculator;

    /**
     * TrackingService constructor.
     *
     * @param AttemptTrackingService $attemptTrackingService
     * @param AttemptSummaryCalculator $attemptSummaryCalculator
     * @param AssessmentTrackingService $assessmentTrackingService
     * @param ProgressCalculator $progressCalculator
     */
    public function __construct(AttemptTrackingService $attemptTrackingService,
        AttemptSummaryCalculator $attemptSummaryCalculator, AssessmentTrackingService $assessmentTrackingService,
        ProgressCalculator $progressCalculator)
    {
        $this->attemptTrackingService = $attemptTrackingService;
        $this->attemptSummaryCalculator = $attemptSummaryCalculator;
        $this->assessmentTrackingService = $assessmentTrackingService;
        $this->progressCalculator = $progressCalculator;
    }

    /**
     * Tracks an attempt for a given user
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     */
    public function trackAttemptForUser(LearningPath $learningPath, TreeNode $treeNode, User $user)
    {
        $this->attemptTrackingService->trackAttemptForUser($learningPath, $treeNode, $user);
    }

    /**
     * Change the status of a given learning path tree node
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     */
    public function setActiveAttemptCompleted(LearningPath $learningPath, TreeNode $treeNode, User $user)
    {
        $this->attemptTrackingService->setActiveAttemptCompleted($learningPath, $treeNode, $user);
    }

    /**
     * Returns the identifier for the active TreeNodeAttempt
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     *
     * @return int
     */
    public function getActiveAttemptId(LearningPath $learningPath, TreeNode $treeNode, User $user)
    {
        return $this->attemptTrackingService->getActiveAttemptId($learningPath, $treeNode, $user);
    }

    /**
     * Returns the active TreeNodeAttempt
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    public function getActiveAttempt(LearningPath $learningPath, TreeNode $treeNode, User $user)
    {
        return $this->attemptTrackingService->getActiveAttempt($learningPath, $treeNode, $user);
    }

    /**
     * Calculates and stores the total time for the active attempt of the given learning path three node for a given
     * user
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     */
    public function setActiveAttemptTotalTime(LearningPath $learningPath, TreeNode $treeNode, User $user)
    {
        $this->attemptTrackingService->setActiveAttemptTotalTime($learningPath, $treeNode, $user);
    }

    /**
     * Sets the total time of a given attempt identified by the learning path child attempt id
     *
     * @param $treeNodeAttemptId
     * @throws ObjectNotExistException
     */
    public function setAttemptTotalTimeByTreeNodeAttemptId($treeNodeAttemptId)
    {
        $this->attemptTrackingService->setAttemptTotalTimeByTreeNodeAttemptId($treeNodeAttemptId);
    }

    /**
     * Returns the progress for a given user in a given learning path
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function getLearningPathProgress(LearningPath $learningPath, User $user, TreeNode $treeNode = null)
    {
        return $this->progressCalculator->getLearningPathProgress($learningPath, $user, $treeNode);
    }

    /**
     * Checks if a given learning path tree node is completed
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    public function isTreeNodeCompleted(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->progressCalculator->isTreeNodeCompleted($learningPath, $user, $treeNode);
    }

    /**
     * Returns whether or not the maximum number of attempts is reached for the given LearningPath, User
     * and TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    public function isMaximumAttemptsReachedForAssessment(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->assessmentTrackingService->isMaximumAttemptsReachedForAssessment($learningPath, $user, $treeNode);
    }

    /**
     * Saves the answer, score and hint for a question for the given LearningPath, User, TreeNode and
     * Question identifier
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $questionIdentifier
     * @param string $answer
     * @param int $score
     * @param string $hint
     */
    public function saveAnswerForQuestion(LearningPath $learningPath, User $user, TreeNode $treeNode,
        $questionIdentifier, $answer = '', $score = 0, $hint = '')
    {
        $this->assessmentTrackingService->saveAnswerForQuestion(
            $learningPath,
            $user,
            $treeNode,
            $questionIdentifier,
            $answer,
            $score,
            $hint);
    }

    /**
     * Saves the assessment score for the given LearningPath, User and TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $assessmentScore
     */
    public function saveAssessmentScore(LearningPath $learningPath, User $user, TreeNode $treeNode, $assessmentScore = 0)
    {
        $this->assessmentTrackingService->saveAssessmentScore($learningPath, $user, $treeNode, $assessmentScore);
    }

    /**
     * Changes the assessment score for the given LearningPath, User, TreeNode and
     * TreeNodeAttemptId
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param null $treeNodeAttemptId
     * @param int $newScore
     */
    public function changeAssessmentScore(LearningPath $learningPath, User $user, TreeNode $treeNode, $treeNodeAttemptId,
        $newScore = 0)
    {
        $this->assessmentTrackingService->changeAssessmentScore(
            $learningPath,
            $user,
            $treeNode,
            $treeNodeAttemptId,
            $newScore);
    }

    /**
     * Changes the score and feedback for a given question in a given TreeNodeAttempt identifier by ID
     * for a given LearningPath, User and TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $treeNodeAttemptId
     * @param int $questionIdentifier
     * @param int $score
     * @param string $feedback
     */
    public function changeQuestionScoreAndFeedback(LearningPath $learningPath, User $user, TreeNode $treeNode,
        $treeNodeAttemptId, $questionIdentifier, $score = 0, $feedback = '')
    {
        $this->assessmentTrackingService->changeQuestionScoreAndFeedback(
            $learningPath,
            $user,
            $treeNode,
            $treeNodeAttemptId,
            $questionIdentifier,
            $score,
            $feedback);
    }

    /**
     * Returns the question attempts for a given LearningPath, User and TreeNode
     * using the given attempt (by id) or the active attempt
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $treeNodeAttemptId
     *
     * @return TreeNodeQuestionAttempt[]
     */
    public function getQuestionAttempts(LearningPath $learningPath, User $user, TreeNode $treeNode,
        $treeNodeAttemptId = null)
    {
        return $this->assessmentTrackingService->getQuestionAttempts(
            $learningPath,
            $user,
            $treeNode,
            $treeNodeAttemptId);
    }

    /**
     * Registers the question attempts for the given question identifiers
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int[] $questionIdentifiers
     *
     * @return TreeNodeQuestionAttempt[]
     */
    public function registerQuestionAttempts(LearningPath $learningPath, User $user, TreeNode $treeNode,
        $questionIdentifiers = [])
    {
        return $this->assessmentTrackingService->registerQuestionAttempts(
            $learningPath,
            $user,
            $treeNode,
            $questionIdentifiers);
    }

    /**
     * Returns a TreeNodeAttempt by a given id, validating that it belongs to the attempt of the given user
     * and learning path tree node
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param $treeNodeAttemptId
     * @return TreeNodeAttempt
     */
    public function getTreeNodeAttemptById(LearningPath $learningPath, User $user, TreeNode $treeNode,
        $treeNodeAttemptId)
    {
        return $this->attemptTrackingService->getTreeNodeAttemptById(
            $learningPath,
            $user,
            $treeNode,
            $treeNodeAttemptId);
    }

    /**
     * Deletes the learning path child attempt by a given id.
     * Verifies that this identifier belongs to the attempts
     * for the given learning path and user
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param User $reportingUser
     * @param TreeNode $treeNode
     * @param int $treeNodeAttemptId
     *
     * @throws NotAllowedException
     */
    public function deleteTreeNodeAttemptById(LearningPath $learningPath, User $user, User $reportingUser,
        TreeNode $treeNode, $treeNodeAttemptId)
    {
        $this->attemptTrackingService->deleteTreeNodeAttemptById(
            $learningPath,
            $user,
            $reportingUser,
            $treeNode,
            $treeNodeAttemptId);
    }

    /**
     * Deletes the learning path child attempts for a given TreeNode.
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param User $reportingUser
     * @param TreeNode $treeNode
     *
     * @throws NotAllowedException
     */
    public function deleteTreeNodeAttemptsForTreeNode(LearningPath $learningPath, User $user, User $reportingUser,
        TreeNode $treeNode)
    {
        $this->attemptTrackingService->deleteTreeNodeAttemptsForTreeNode(
            $learningPath,
            $user,
            $reportingUser,
            $treeNode);
    }

    /**
     * Returns whether or not the given user can delete the attempt data for the given target user
     *
     * @param User $user
     * @param User $targetUser
     *
     * @return bool
     */
    public function canDeleteLearningPathAttemptData(User $user, User $targetUser)
    {
        return $this->attemptTrackingService->canDeleteLearningPathAttemptData($user, $targetUser);
    }

    /**
     * Returns whether or not the given TreeNode is blocked for the given user
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    public function isCurrentTreeNodeBlocked(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->progressCalculator->isCurrentTreeNodeBlocked($learningPath, $user, $treeNode);
    }

    /**
     * Returns a list of the nodes that are responsible that a step can not be taken
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return TreeNode[]
     */
    public function getResponsibleNodesForBlockedTreeNode(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->progressCalculator->getResponsibleNodesForBlockedTreeNode($learningPath, $user, $treeNode);
    }

    /**
     * Returns whether or not the TreeNode has attempts
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    public function hasTreeNodeAttempts(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->attemptTrackingService->hasTreeNodeAttempts($learningPath, $user, $treeNode);
    }

    /**
     * Returns the number of attempts for a given TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function countTreeNodeAttempts(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->attemptTrackingService->countTreeNodeAttempts($learningPath, $user, $treeNode);
    }

    /**
     * Returns the attempts for a given TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return TreeNodeAttempt[]
     */
    public function getTreeNodeAttempts(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->attemptTrackingService->getTreeNodeAttempts($learningPath, $user, $treeNode);
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
    public function getTotalTimeSpentInTreeNode(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->getTotalTimeSpentInTreeNode($learningPath, $user, $treeNode);
    }

    /**
     * Returns the average score of the given user in the given TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return float
     */
    public function getAverageScoreInTreeNode(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->getAverageScoreInTreeNode($learningPath, $user, $treeNode);
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
    public function getMaximumScoreInTreeNode(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->getMaximumScoreInTreeNode($learningPath, $user, $treeNode);
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
    public function getMinimumScoreInTreeNode(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->getMinimumScoreInTreeNode($learningPath, $user, $treeNode);
    }

    /**
     * Returns the score for the last attempt of the given user in the given TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return string
     */
    public function getLastAttemptScoreForTreeNode(LearningPath $learningPath, User $user, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->getLastAttemptScoreForTreeNode($learningPath, $user, $treeNode);
    }

    /**
     * Counts the learning path attempts joined with users for searching
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param Condition $condition
     *
     * @return int
     */
    public function countLearningPathAttemptsWithUsers(LearningPath $learningPath, TreeNode $treeNode = null,
        Condition $condition = null)
    {
        return $this->attemptTrackingService->countLearningPathAttemptsWithUsers($learningPath, $treeNode, $condition);
    }

    /**
     * Returns the LearningPathAttempt objects for a given LearningPath with a given condition, offset,
     * count and orderBy Joined with users for searching and sorting
     *
     * @param LearningPath $learningPath
     * @param TreeNode|null $treeNode
     * @param Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getLearningPathAttemptsWithUser(LearningPath $learningPath, TreeNode $treeNode = null,
        Condition $condition = null, $offset = 0, $count = 0, $orderBy = null)
    {
        return $this->attemptTrackingService->getLearningPathAttemptsWithUser(
            $learningPath,
            $treeNode,
            $condition,
            $offset,
            $count,
            $orderBy);
    }

    /**
     * Counts the learning path attempts joined with users for searching
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersWithLearningPathAttempts(LearningPath $learningPath, Condition $condition = null)
    {
        return $this->attemptTrackingService->countTargetUsersWithLearningPathAttempts($learningPath, $condition);
    }

    /**
     * Returns the LearningPathAttempt objects for a given LearningPath with a given condition, offset,
     * count and orderBy Joined with users for searching and sorting
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getTargetUsersWithLearningPathAttempts(LearningPath $learningPath, TreeNode $treeNode,
        Condition $condition = null, $offset = 0, $count = 0, $orderBy = null)
    {
        return $this->attemptTrackingService->getTargetUsersWithLearningPathAttempts(
            $learningPath,
            $treeNode,
            $condition,
            $offset,
            $count,
            $orderBy);
    }

    /**
     * Counts the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTargetUsersWithoutLearningPathAttempts(LearningPath $learningPath, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->findTargetUsersWithoutLearningPathAttempts($learningPath, $treeNode);
    }

    /**
     * Counts the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function countTargetUsersWithoutLearningPathAttempts(LearningPath $learningPath, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->countTargetUsersWithoutLearningPathAttempts($learningPath, $treeNode);
    }

    /**
     * Counts the target users with attempts on a learning path that are completed
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function countTargetUsersWithFullLearningPathAttempts(LearningPath $learningPath, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->countTargetUsersWithFullLearningPathAttempts($learningPath, $treeNode);
    }

    /**
     * Finds the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTargetUsersWithPartialLearningPathAttempts(LearningPath $learningPath, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->findTargetUsersWithPartialLearningPathAttempts($learningPath, $treeNode);
    }

    /**
     * Counts the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function countTargetUsersWithPartialLearningPathAttempts(LearningPath $learningPath, TreeNode $treeNode)
    {
        return $this->attemptSummaryCalculator->countTargetUsersWithPartialLearningPathAttempts(
            $learningPath,
            $treeNode);
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
        return $this->attemptTrackingService->countTargetUsers($learningPath);
    }
}
