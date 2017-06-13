<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
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
    const STATISTICS_COMPLETED = 0;
    const STATISTICS_STARTED = 1;
    const STATISTICS_NOT_STARTED = 2;

    /**
     * @var AttemptService
     */
    protected $attemptService;

    /**
     * @var TrackingRepositoryInterface
     */
    protected $trackingRepository;

    /**
     * @var bool[]
     */
    protected $treeNodesCompletedCache;

    /**
     * @var string[][][][]
     */
    protected $treeNodeStatisticsCache;

    /**
     * TrackingService constructor.
     *
     * @param AttemptService $attemptService
     * @param TrackingRepositoryInterface $trackingRepository
     */
    public function __construct(
        AttemptService $attemptService,
        TrackingRepositoryInterface $trackingRepository
    )
    {
        $this->attemptService = $attemptService;
        $this->trackingRepository = $trackingRepository;
        $this->treeNodesCompletedCache = array();
    }

    /**
     * Tracks an attempt for a given user
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     */
    public function trackAttemptForUser(
        LearningPath $learningPath, TreeNode $treeNode, User $user
    )
    {
        $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);
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
        $activeAttempt = $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);

        $activeAttempt->setCompleted(true);
        $this->trackingRepository->update($activeAttempt);
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
    public function getActiveAttemptId(
        LearningPath $learningPath, TreeNode $treeNode, User $user
    )
    {
        $activeAttempt = $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);

        return $activeAttempt->getId();
    }

    /**
     * Calculates and stores the total time for the active attempt of the given learning path three node for a given
     * user
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     */
    public function setActiveAttemptTotalTime(
        LearningPath $learningPath, TreeNode $treeNode, User $user
    )
    {
        $activeAttempt = $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);

        $activeAttempt->calculateAndSetTotalTime();
        $this->trackingRepository->update($activeAttempt);
    }

    /**
     * Sets the total time of a given attempt identified by the learning path child attempt id
     *
     * @param $treeNodeAttemptId
     *
     * @throws ObjectNotExistException
     */
    public function setAttemptTotalTimeByTreeNodeAttemptId($treeNodeAttemptId)
    {
        $treeNodeAttempt =
            $this->trackingRepository->findTreeNodeAttemptById($treeNodeAttemptId);

        if (!$treeNodeAttempt instanceof TreeNodeAttempt)
        {
            throw new ObjectNotExistException('LearningPathAttempt');
        }

        $treeNodeAttempt->calculateAndSetTotalTime();
        $this->trackingRepository->update($treeNodeAttempt);
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
    public function getLearningPathProgress(
        LearningPath $learningPath, User $user, TreeNode $treeNode = null
    )
    {
        /** @var TreeNode[] $nodes */
        $nodes = array();
        $nodes[] = $treeNode;
        $nodes = array_merge($nodes, $treeNode->getDescendantNodes());

        $nodesCompleted = 0;

        foreach ($nodes as $node)
        {
            if ($this->isTreeNodeCompleted($learningPath, $user, $node))
            {
                $nodesCompleted ++;
            }
        }

        $progress = (int) floor(($nodesCompleted / count($nodes)) * 100);

        return $progress > 100 ? 100 : $progress;
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
    public function isTreeNodeCompleted(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $cacheKey = md5($learningPath->getId() . ':' . $user->getId() . ':' . $treeNode->getId());

        if (!array_key_exists($cacheKey, $this->treeNodesCompletedCache))
        {
            $this->treeNodesCompletedCache[$cacheKey] =
                $this->calculateTreeNodeCompleted($learningPath, $user, $treeNode);
        }

        return $this->treeNodesCompletedCache[$cacheKey];
    }

    /**
     * Determines whether or not the learning path tree node is completed by checking the tracking and every subitem
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    protected function calculateTreeNodeCompleted(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $treeNodeAttempts = $this->attemptService->getTreeNodeAttempts($learningPath, $user);

        if ($treeNode->hasChildNodes())
        {
            $completed = true;

            foreach ($treeNode->getChildNodes() as $childTreeNode)
            {
                $completed &= $this->isTreeNodeCompleted(
                    $learningPath, $user, $childTreeNode
                );
            }

            if (!$completed)
            {
                return false;
            }
        }

        /** @var TreeNodeAttempt[] $treeNodeAttempts */
        $treeNodeAttempts = $treeNodeAttempts[$treeNode->getId()];

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            if($this->isAttemptCompleted($treeNode, $treeNodeAttempt))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether or not a given attempt for a treenode is completed
     *
     * @param TreeNode $treeNode
     * @param TreeNodeAttempt $treeNodeAttempt
     *
     * @return bool
     */
    protected function isAttemptCompleted(TreeNode $treeNode, TreeNodeAttempt $treeNodeAttempt)
    {
        $isAssessment = $treeNode->getContentObject() instanceof Assessment;
        $masteryScore = $treeNode->getTreeNodeData()->getMasteryScore();

        if (!$treeNodeAttempt->isCompleted())
        {
            return false;
        }

        if(!$isAssessment)
        {
            return true;
        }

        return $treeNodeAttempt->get_score() >= $masteryScore;
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
    public function isMaximumAttemptsReachedForAssessment(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        $treeNodeAttempts =
            $this->attemptService->getTreeNodeAttempts($learningPath, $user);

        /** @var Assessment $assessment */
        $assessment = $treeNode->getContentObject();

        return $assessment->get_maximum_attempts() > 0 &&
            count($treeNodeAttempts) > $assessment->get_maximum_attempts();
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
    public function saveAnswerForQuestion(
        LearningPath $learningPath, User $user, TreeNode $treeNode, $questionIdentifier,
        $answer = '', $score = 0, $hint = ''
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        $treeNodeQuestionAttempts = $this->getQuestionAttempts($learningPath, $user, $treeNode);
        $treeNodeQuestionAttempt = $treeNodeQuestionAttempts[$questionIdentifier];

        if (!$treeNodeQuestionAttempt instanceof TreeNodeQuestionAttempt)
        {
            throw new \RuntimeException(
                sprintf('The given TreeNodeQuestionAttempt for the question %s is not found', $questionIdentifier)
            );
        }

        $treeNodeQuestionAttempt->set_answer($answer);
        $treeNodeQuestionAttempt->set_score($score);
        $treeNodeQuestionAttempt->set_hint($hint);

        $this->trackingRepository->update($treeNodeQuestionAttempt);
    }

    /**
     * Saves the assessment score for the given LearningPath, User and TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $assessmentScore
     */
    public function saveAssessmentScore(
        LearningPath $learningPath, User $user, TreeNode $treeNode, $assessmentScore = 0
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        $activeAttempt = $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);

        $activeAttempt->set_score($assessmentScore);
        $activeAttempt->calculateAndSetTotalTime();
        $activeAttempt->setCompleted(true);

        $this->trackingRepository->update($activeAttempt);
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
    public function changeAssessmentScore(
        LearningPath $learningPath, User $user,
        TreeNode $treeNode, $treeNodeAttemptId, $newScore = 0
    )
    {
        $treeNodeAttempt = $this->getTreeNodeAttemptById(
            $learningPath, $user, $treeNode, $treeNodeAttemptId
        );

        $treeNodeAttempt->set_score($newScore);

        $this->trackingRepository->update($treeNodeAttempt);
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
    public function changeQuestionScoreAndFeedback(
        LearningPath $learningPath, User $user, TreeNode $treeNode, $treeNodeAttemptId,
        $questionIdentifier, $score = 0, $feedback = ''
    )
    {
        $treeNodeQuestionAttempts = $this->getQuestionAttempts(
            $learningPath, $user, $treeNode, $treeNodeAttemptId
        );

        $treeNodeQuestionAttempt = $treeNodeQuestionAttempts[$questionIdentifier];

        if (!$treeNodeQuestionAttempt instanceof TreeNodeQuestionAttempt)
        {
            throw new \RuntimeException(
                sprintf('The given TreeNodeQuestionAttempt for the question %s is not found', $questionIdentifier)
            );
        }

        $treeNodeQuestionAttempt->set_score($score);
        $treeNodeQuestionAttempt->set_feedback($feedback);

        $this->trackingRepository->update($treeNodeQuestionAttempt);
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
    public function getQuestionAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode,
        $treeNodeAttemptId = null
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        if (is_null($treeNodeAttemptId))
        {
            $treeNodeAttempt = $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);
        }
        else
        {
            $treeNodeAttempt = $this->getTreeNodeAttemptById(
                $learningPath, $user, $treeNode, $treeNodeAttemptId
            );
        }

        $questionAttempts = $this->attemptService->getTreeNodeQuestionAttempts(
            $treeNodeAttempt
        );

        $questionAttemptPerQuestion = array();

        foreach ($questionAttempts as $questionAttempt)
        {
            $questionAttemptPerQuestion[$questionAttempt->get_question_complex_id()] = $questionAttempt;
        }

        return $questionAttemptPerQuestion;
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
    public function registerQuestionAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode,
        $questionIdentifiers = array()
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        $activeAttempt = $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);

        $questionAttemptPerQuestion = array();
        foreach ($questionIdentifiers as $questionIdentifier)
        {
            $questionAttemptPerQuestion[$questionIdentifier] =
                $this->attemptService->createTreeNodeQuestionAttempt(
                    $activeAttempt, $questionIdentifier
                );
        }

        return $questionAttemptPerQuestion;
    }

    /**
     * Returns a TreeNodeAttempt by a given id, validating that it belongs to the attempt of the given user
     * and learning path tree node
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param $treeNodeAttemptId
     *
     * @return TreeNodeAttempt
     */
    public function getTreeNodeAttemptById(
        LearningPath $learningPath, User $user, TreeNode $treeNode, $treeNodeAttemptId
    )
    {
        $treeNodeAttempts = $this->getTreeNodeAttempts(
            $learningPath, $user, $treeNode
        );

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            if ($treeNodeAttempt->getId() == $treeNodeAttemptId)
            {
                return $treeNodeAttempt;
            }
        }

        throw new \RuntimeException('Could not find the TreeNodeAttempt by id ' . $treeNodeAttemptId);
    }

    /**
     * Deletes the learning path child attempt by a given id. Verifies that this identifier belongs to the attempts
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
    public function deleteTreeNodeAttemptById(
        LearningPath $learningPath, User $user, User $reportingUser,
        TreeNode $treeNode, $treeNodeAttemptId
    )
    {
        if (!$this->canDeleteLearningPathAttemptData($user, $reportingUser))
        {
            throw new NotAllowedException();
        }

        $treeNodeAttempt = $this->getTreeNodeAttemptById(
            $learningPath, $reportingUser, $treeNode, $treeNodeAttemptId
        );

        $this->attemptService->deleteTreeNodeAttempt($treeNodeAttempt);
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
    public function deleteTreeNodeAttemptsForTreeNode(
        LearningPath $learningPath, User $user, User $reportingUser, TreeNode $treeNode
    )
    {
        if (!$this->canDeleteLearningPathAttemptData($user, $reportingUser))
        {
            throw new NotAllowedException();
        }

        $treeNodeAttempts = $this->getTreeNodeAttempts(
            $learningPath, $reportingUser, $treeNode
        );

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            $this->attemptService->deleteTreeNodeAttempt($treeNodeAttempt);
        }
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
        return $user->is_platform_admin() || $user->getId() == $targetUser->getId();
    }

    /**
     * Validates that the given TreeNode contains an assessment content object
     *
     * @param TreeNode $treeNode
     */
    protected function validateTreeNodeIsAssessment(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Assessment)
        {
            throw new \RuntimeException(
                'The given TreeNode is not connected to an assessment'
            );
        }
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
    public function isCurrentTreeNodeBlocked(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $previousNodes = $treeNode->getPreviousNodes();

        foreach ($previousNodes as $previousNode)
        {
            if ($this->doesNodeBlockCurrentNode($learningPath, $user, $treeNode, $previousNode))
            {
                return true;
            }
        }

        return false;
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
    public function getResponsibleNodesForBlockedTreeNode(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $previousNodes = $treeNode->getPreviousNodes();

        $blockedNodes = array();

        foreach ($previousNodes as $previousNode)
        {
            if ($this->doesNodeBlockCurrentNode($learningPath, $user, $treeNode, $previousNode))
            {
                $blockedNodes[] = $previousNode;
            }
        }

        return $blockedNodes;
    }

    /**
     * Helper function to check whether or not the
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $currentTreeNode
     * @param TreeNode $possibleBlockNode
     *
     * @return bool
     */
    protected function doesNodeBlockCurrentNode(
        LearningPath $learningPath, User $user, TreeNode $currentTreeNode,
        TreeNode $possibleBlockNode
    )
    {
        if ($currentTreeNode->isChildOf($possibleBlockNode))
        {
            return false;
        }

        if (
            $learningPath->enforcesDefaultTraversingOrder() ||
            (!$possibleBlockNode->isRootNode() && $possibleBlockNode->getTreeNodeData()->isBlocked())
        )
        {
            if (!$this->isTreeNodeCompleted($learningPath, $user, $possibleBlockNode))
            {
                return true;
            }
        }

        return false;
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
    public function hasTreeNodeAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        return $this->countTreeNodeAttempts($learningPath, $user, $treeNode) > 0;
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
    public function countTreeNodeAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        return count($this->getTreeNodeAttempts($learningPath, $user, $treeNode));
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
    public function getTreeNodeAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        return $this->attemptService->getTreeNodeAttemptsForTreeNode($learningPath, $user, $treeNode);
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

        $treeNodeAttempts = $this->getTreeNodeAttempts($learningPath, $user, $treeNode);
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
        try
        {
            $this->validateTreeNodeIsAssessment($treeNode);
        }
        catch (\Exception $ex)
        {
            return null;
        }

        $totalScore = 0;

        $treeNodeAttempts = $this->getTreeNodeAttempts($learningPath, $user, $treeNode);

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
        $this->validateTreeNodeIsAssessment($treeNode);

        $maximumScore = 0;

        $treeNodeAttempts = $this->getTreeNodeAttempts($learningPath, $user, $treeNode);

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
        $this->validateTreeNodeIsAssessment($treeNode);

        $minimumScore = null;

        $treeNodeAttempts = $this->getTreeNodeAttempts($learningPath, $user, $treeNode);

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

    /**
     * Returns the score for the last attempt of the given user in the given TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return string
     */
    public function getLastAttemptScoreForTreeNode(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);
        $treeNodeAttempts =
            $this->getTreeNodeAttempts($learningPath, $user, $treeNode);

        $treeNodeAttempt = array_pop($treeNodeAttempts);

        if (!$treeNodeAttempt instanceof TreeNodeAttempt)
        {
            return 0;
        }

        return (int) $treeNodeAttempt->get_score();
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
    public function countLearningPathAttemptsWithUsers(
        LearningPath $learningPath, TreeNode $treeNode = null, Condition $condition = null
    )
    {
        $treeNodeDataIds = $treeNode instanceof TreeNode ?
            $treeNode->getTreeNodeDataIdsFromSelfAndDescendants() : array();

        return $this->trackingRepository->countLearningPathAttemptsWithUser(
            $learningPath, $treeNodeDataIds, $condition
        );
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
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getLearningPathAttemptsWithUser(
        LearningPath $learningPath, TreeNode $treeNode = null, Condition $condition = null,
        $offset = 0, $count = 0, $orderBy = array()
    )
    {
        $treeNodeDataIds = $treeNode instanceof TreeNode ?
            $treeNode->getTreeNodeDataIdsFromSelfAndDescendants() : array();

        return $this->trackingRepository->findLearningPathAttemptsWithUser(
            $learningPath, $treeNodeDataIds, $condition, $offset, $count, $orderBy
        );
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
        return $this->trackingRepository->countTargetUsersForLearningPath(
            $learningPath, $condition
        );
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
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getTargetUsersWithLearningPathAttempts(
        LearningPath $learningPath, TreeNode $treeNode,
        Condition $condition = null, $offset = 0, $count = 0, $orderBy = array()
    )
    {
        $treeNodeDataIds = $treeNode instanceof TreeNode ?
            $treeNode->getTreeNodeDataIdsFromSelfAndDescendants() : array();

        return $this->trackingRepository->findTargetUsersWithLearningPathAttempts(
            $learningPath, $treeNodeDataIds, $condition, $offset, $count, $orderBy
        );
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

    /**
     * Counts the total number of target users for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsers(LearningPath $learningPath)
    {
        return $this->trackingRepository->countTargetUsersForLearningPath($learningPath);
    }
}
