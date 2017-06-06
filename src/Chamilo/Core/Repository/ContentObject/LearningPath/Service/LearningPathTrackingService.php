<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeDataAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Service to manage the tracking of attempts in a learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingService
{
    const STATISTICS_COMPLETED = 0;
    const STATISTICS_STARTED = 1;
    const STATISTICS_NOT_STARTED = 2;

    /**
     * @var LearningPathAttemptService
     */
    protected $learningPathAttemptService;

    /**
     * @var LearningPathTrackingRepositoryInterface
     */
    protected $learningPathTrackingRepository;

    /**
     * @var bool[]
     */
    protected $treeNodesCompletedCache;

    /**
     * @var string[][][][]
     */
    protected $treeNodeStatisticsCache;

    /**
     * LearningPathTrackingService constructor.
     *
     * @param LearningPathAttemptService $learningPathAttemptService
     * @param LearningPathTrackingRepositoryInterface $learningPathTrackingRepository
     */
    public function __construct(
        LearningPathAttemptService $learningPathAttemptService,
        LearningPathTrackingRepositoryInterface $learningPathTrackingRepository
    )
    {
        $this->learningPathAttemptService = $learningPathAttemptService;
        $this->learningPathTrackingRepository = $learningPathTrackingRepository;
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
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);

        $this->learningPathAttemptService->getOrCreateActiveTreeNodeDataAttempt(
            $learningPathAttempt, $treeNode
        );
    }

    /**
     * Change the status of a given learning path tree node
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     * @param string $newStatus
     */
    public function changeActiveAttemptStatus(
        LearningPath $learningPath, TreeNode $treeNode, User $user,
        $newStatus = TreeNodeDataAttempt::STATUS_COMPLETED
    )
    {
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveTreeNodeDataAttempt(
            $learningPathAttempt, $treeNode
        );

        $activeAttempt->set_status($newStatus);
        $this->learningPathTrackingRepository->update($activeAttempt);
    }

    /**
     * Returns the identifier for the active TreeNodeDataAttempt
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
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveTreeNodeDataAttempt(
            $learningPathAttempt, $treeNode
        );

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
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveTreeNodeDataAttempt(
            $learningPathAttempt, $treeNode
        );

        $activeAttempt->calculateAndSetTotalTime();
        $this->learningPathTrackingRepository->update($activeAttempt);
    }

    /**
     * Sets the total time of a given attempt identified by the learning path child attempt id
     *
     * @param $treeNodeDataAttemptId
     *
     * @throws ObjectNotExistException
     */
    public function setAttemptTotalTimeByTreeNodeDataAttemptId($treeNodeDataAttemptId)
    {
        $treeNodeDataAttempt =
            $this->learningPathTrackingRepository->findTreeNodeDataAttemptById($treeNodeDataAttemptId);

        if (!$treeNodeDataAttempt instanceof TreeNodeDataAttempt)
        {
            throw new ObjectNotExistException('LearningPathAttempt');
        }

        $treeNodeDataAttempt->calculateAndSetTotalTime();
        $this->learningPathTrackingRepository->update($treeNodeDataAttempt);
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
        $learningPathAttempt = $this->learningPathAttemptService->getLearningPathAttemptForUser($learningPath, $user);

        if (!$learningPathAttempt instanceof LearningPathAttempt)
        {
            return false;
        }

        $treeNodeDataAttempts =
            $this->learningPathAttemptService->getTreeNodeDataAttempts($learningPathAttempt);

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

        /** @var TreeNodeDataAttempt[] $treeNodeAttempts */
        $treeNodeAttempts = $treeNodeDataAttempts[$treeNode->getId()];

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            if ($treeNodeAttempt->isFinished())
            {
                return true;
            }
        }

        return false;
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

        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $treeNodeDataAttempts =
            $this->learningPathAttemptService->getTreeNodeDataAttempts($learningPathAttempt);

        /** @var Assessment $assessment */
        $assessment = $treeNode->getContentObject();

        return $assessment->get_maximum_attempts() > 0 &&
            count($treeNodeDataAttempts) > $assessment->get_maximum_attempts();
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

        $learningPathQuestionAttempts = $this->getQuestionAttempts($learningPath, $user, $treeNode);
        $learningPathQuestionAttempt = $learningPathQuestionAttempts[$questionIdentifier];

        if (!$learningPathQuestionAttempt instanceof LearningPathQuestionAttempt)
        {
            throw new \RuntimeException(
                sprintf('The given LearningPathQuestionAttempt for the question %s is not found', $questionIdentifier)
            );
        }

        $learningPathQuestionAttempt->set_answer($answer);
        $learningPathQuestionAttempt->set_score($score);
        $learningPathQuestionAttempt->set_hint($hint);

        $this->learningPathTrackingRepository->update($learningPathQuestionAttempt);
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

        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveTreeNodeDataAttempt(
            $learningPathAttempt, $treeNode
        );

        $activeAttempt->set_score($assessmentScore);
        $activeAttempt->calculateAndSetTotalTime();
        $activeAttempt->set_status($this->determineStatusForAssessmentByScore($treeNode, $assessmentScore));

        $this->learningPathTrackingRepository->update($activeAttempt);
    }

    /**
     * Changes the assessment score for the given LearningPath, User, TreeNode and
     * TreeNodeDataAttemptId
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param null $treeNodeDataAttemptId
     * @param int $newScore
     */
    public function changeAssessmentScore(
        LearningPath $learningPath, User $user,
        TreeNode $treeNode, $treeNodeDataAttemptId, $newScore = 0
    )
    {
        $treeNodeDataAttempt = $this->getTreeNodeDataAttemptById(
            $learningPath, $user, $treeNode, $treeNodeDataAttemptId
        );

        $treeNodeDataAttempt->set_score($newScore);

        $treeNodeDataAttempt->set_status(
            $this->determineStatusForAssessmentByScore($treeNode, $newScore)
        );

        $this->learningPathTrackingRepository->update($treeNodeDataAttempt);
    }

    /**
     * Changes the score and feedback for a given question in a given TreeNodeDataAttempt identifier by ID
     * for a given LearningPath, User and TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $treeNodeDataAttemptId
     * @param int $questionIdentifier
     * @param int $score
     * @param string $feedback
     */
    public function changeQuestionScoreAndFeedback(
        LearningPath $learningPath, User $user, TreeNode $treeNode, $treeNodeDataAttemptId,
        $questionIdentifier, $score = 0, $feedback = ''
    )
    {
        $learningPathQuestionAttempts = $this->getQuestionAttempts(
            $learningPath, $user, $treeNode, $treeNodeDataAttemptId
        );

        $learningPathQuestionAttempt = $learningPathQuestionAttempts[$questionIdentifier];

        if (!$learningPathQuestionAttempt instanceof LearningPathQuestionAttempt)
        {
            throw new \RuntimeException(
                sprintf('The given LearningPathQuestionAttempt for the question %s is not found', $questionIdentifier)
            );
        }

        $learningPathQuestionAttempt->set_score($score);
        $learningPathQuestionAttempt->set_feedback($feedback);

        $this->learningPathTrackingRepository->update($learningPathQuestionAttempt);
    }

    /**
     * Determines the status for a given assessment TreeNode based on the given score
     *
     * @param TreeNode $treeNode
     * @param int $assessmentScore
     *
     * @return string
     */
    protected function determineStatusForAssessmentByScore(
        TreeNode $treeNode, $assessmentScore = 0
    )
    {
        $masteryScore = $treeNode->getTreeNodeData()->getMasteryScore();
        if ($masteryScore > 0)
        {
            $status = ($assessmentScore >= $masteryScore) ? TreeNodeDataAttempt::STATUS_PASSED :
                TreeNodeDataAttempt::STATUS_FAILED;
        }
        else
        {
            $status = TreeNodeDataAttempt::STATUS_COMPLETED;
        }

        return $status;
    }

    /**
     * Returns the question attempts for a given LearningPath, User and TreeNode
     * using the given attempt (by id) or the active attempt
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $treeNodeDataAttemptId
     *
     * @return LearningPathQuestionAttempt[]
     */
    public function getQuestionAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode,
        $treeNodeDataAttemptId = null
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        if (is_null($treeNodeDataAttemptId))
        {
            $learningPathAttempt =
                $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
            $treeNodeDataAttempt = $this->learningPathAttemptService->getOrCreateActiveTreeNodeDataAttempt(
                $learningPathAttempt, $treeNode
            );
        }
        else
        {
            $treeNodeDataAttempt = $this->getTreeNodeDataAttemptById(
                $learningPath, $user, $treeNode, $treeNodeDataAttemptId
            );
        }

        $questionAttempts = $this->learningPathAttemptService->getLearningPathQuestionAttempts(
            $treeNodeDataAttempt
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
     * @return LearningPathQuestionAttempt[]
     */
    public function registerQuestionAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode,
        $questionIdentifiers = array()
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveTreeNodeDataAttempt(
            $learningPathAttempt, $treeNode
        );

        $questionAttemptPerQuestion = array();
        foreach ($questionIdentifiers as $questionIdentifier)
        {
            $questionAttemptPerQuestion[$questionIdentifier] =
                $this->learningPathAttemptService->createLearningPathQuestionAttempt(
                    $activeAttempt, $questionIdentifier
                );
        }

        return $questionAttemptPerQuestion;
    }

    /**
     * Returns a TreeNodeDataAttempt by a given id, validating that it belongs to the attempt of the given user
     * and learning path tree node
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param $treeNodeDataAttemptId
     *
     * @return TreeNodeDataAttempt
     */
    public function getTreeNodeDataAttemptById(
        LearningPath $learningPath, User $user, TreeNode $treeNode, $treeNodeDataAttemptId
    )
    {
        $treeNodeAttempts = $this->getTreeNodeAttempts(
            $learningPath, $user, $treeNode
        );

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            if ($treeNodeAttempt->getId() == $treeNodeDataAttemptId)
            {
                return $treeNodeAttempt;
            }
        }

        throw new \RuntimeException('Could not find the TreeNodeDataAttempt by id ' . $treeNodeDataAttemptId);
    }

    /**
     * Deletes the learning path child attempt by a given id. Verifies that this identifier belongs to the attempts
     * for the given learning path and user
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param User $reportingUser
     * @param TreeNode $treeNode
     * @param int $treeNodeDataAttemptId
     *
     * @throws NotAllowedException
     */
    public function deleteTreeNodeDataAttemptById(
        LearningPath $learningPath, User $user, User $reportingUser,
        TreeNode $treeNode, $treeNodeDataAttemptId
    )
    {
        if (!$this->canDeleteLearningPathAttemptData($user, $reportingUser))
        {
            throw new NotAllowedException();
        }

        $treeNodeAttempt = $this->getTreeNodeDataAttemptById(
            $learningPath, $reportingUser, $treeNode, $treeNodeDataAttemptId
        );

        $this->learningPathAttemptService->deleteTreeNodeDataAttempt($treeNodeAttempt);
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
    public function deleteTreeNodeDataAttemptsForTreeNode(
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
            $this->learningPathAttemptService->deleteTreeNodeDataAttempt($treeNodeAttempt);
        }
    }

    /**
     * Deletes the learning path attempt for the given user
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @throws NotAllowedException
     */
    public function deleteLearningPathAttempt(LearningPath $learningPath, User $user)
    {
        $learningPathAttempt = $this->learningPathAttemptService->getLearningPathAttemptForUser($learningPath, $user);

        $targetUser = new User();
        $targetUser->setId($learningPathAttempt->get_user_id());

        if (!$this->canDeleteLearningPathAttemptData($user, $targetUser))
        {
            throw new NotAllowedException();
        }

        if ($learningPathAttempt instanceof LearningPathAttempt)
        {
            $this->learningPathAttemptService->deleteLearningPathAttempt($learningPathAttempt);
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
     * @return TreeNodeDataAttempt[]
     */
    public function getTreeNodeAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $learningPathAttempt = $this->learningPathAttemptService->getLearningPathAttemptForUser($learningPath, $user);

        if (!$learningPathAttempt instanceof LearningPathAttempt)
        {
            return array();
        }

        return $this->learningPathAttemptService->getTreeNodeDataAttemptsForTreeNode(
            $learningPathAttempt, $treeNode
        );
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

        $learningPathAttempts = $this->getTreeNodeAttempts($learningPath, $user, $treeNode);
        foreach ($learningPathAttempts as $learningPathAttempt)
        {
            $totalTime += $learningPathAttempt->get_total_time();
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

        $learningPathAttempts = $this->getTreeNodeAttempts($learningPath, $user, $treeNode);

        if (count($learningPathAttempts) == 0)
        {
            return 0;
        }

        foreach ($learningPathAttempts as $learningPathAttempt)
        {
            $totalScore += (int) $learningPathAttempt->get_score();
        }

        return round($totalScore / count($learningPathAttempts), 2);
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

        $learningPathAttempts = $this->getTreeNodeAttempts($learningPath, $user, $treeNode);

        foreach ($learningPathAttempts as $learningPathAttempt)
        {
            $maximumScore = $maximumScore < $learningPathAttempt->get_score() ?
                (int) $learningPathAttempt->get_score() : $maximumScore;
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

        $learningPathAttempts = $this->getTreeNodeAttempts($learningPath, $user, $treeNode);

        foreach ($learningPathAttempts as $learningPathAttempt)
        {
            $minimumScore = is_null($minimumScore) || $minimumScore > $learningPathAttempt->get_score() ?
                (int) $learningPathAttempt->get_score() : $minimumScore;
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
        $treeNodeDataAttempts =
            $this->getTreeNodeAttempts($learningPath, $user, $treeNode);

        $treeNodeDataAttempt = array_pop($treeNodeDataAttempts);

        if (!$treeNodeDataAttempt instanceof TreeNodeDataAttempt)
        {
            return 0;
        }

        return (int) $treeNodeDataAttempt->get_score();
    }

    /**
     * Counts the learning path attempts joined with users for searching
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countLearningPathAttemptsWithUsers(LearningPath $learningPath, Condition $condition = null)
    {
        return $this->learningPathTrackingRepository->countLearningPathAttemptsWithUser($learningPath, $condition);
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

        return $this->learningPathTrackingRepository->findLearningPathAttemptsWithUser(
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
        return $this->learningPathTrackingRepository->countTargetUsersWithLearningPathAttempts(
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

        return $this->learningPathTrackingRepository->findTargetUsersWithLearningPathAttempts(
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

            $usersWithCompletedNodesCount = $this->learningPathTrackingRepository->findUsersWithCompletedNodesCount(
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
        return $this->learningPathTrackingRepository->countTargetUsers($learningPath);
    }
}
