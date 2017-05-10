<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Service to manage the tracking of attempts in a learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingService
{
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
    protected $learningPathTreeNodesCompletedCache;

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
        $this->learningPathTreeNodesCompletedCache = array();
    }

    /**
     * Tracks an attempt for a given user
     *
     * @param LearningPath $learningPath
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param User $user
     */
    public function trackAttemptForUser(
        LearningPath $learningPath, LearningPathTreeNode $learningPathTreeNode, User $user
    )
    {
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);

        $this->learningPathAttemptService->getOrCreateActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );
    }

    /**
     * Change the status of a given learning path tree node
     *
     * @param LearningPath $learningPath
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param User $user
     * @param string $newStatus
     */
    public function changeActiveAttemptStatus(
        LearningPath $learningPath, LearningPathTreeNode $learningPathTreeNode, User $user,
        $newStatus = LearningPathChildAttempt::STATUS_COMPLETED
    )
    {
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );

        $activeAttempt->set_status($newStatus);
        $this->learningPathTrackingRepository->update($activeAttempt);
    }

    /**
     * Returns the identifier for the active LearningPathChildAttempt
     *
     * @param LearningPath $learningPath
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param User $user
     *
     * @return int
     */
    public function getActiveAttemptId(
        LearningPath $learningPath, LearningPathTreeNode $learningPathTreeNode, User $user
    )
    {
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );

        return $activeAttempt->getId();
    }

    /**
     * Calculates and stores the total time for the active attempt of the given learning path three node for a given
     * user
     *
     * @param LearningPath $learningPath
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param User $user
     */
    public function setActiveAttemptTotalTime(
        LearningPath $learningPath, LearningPathTreeNode $learningPathTreeNode, User $user
    )
    {
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );

        $activeAttempt->calculateAndSetTotalTime();
        $this->learningPathTrackingRepository->update($activeAttempt);
    }

    /**
     * Sets the total time of a given attempt identified by the learning path child attempt id
     *
     * @param $learningPathChildAttemptId
     *
     * @throws ObjectNotExistException
     */
    public function setAttemptTotalTimeByLearningPathChildAttemptId($learningPathChildAttemptId)
    {
        $learningPathChildAttempt =
            $this->learningPathTrackingRepository->findLearningPathChildAttemptById($learningPathChildAttemptId);

        if (!$learningPathChildAttempt instanceof LearningPathChildAttempt)
        {
            throw new ObjectNotExistException('LearningPathAttempt');
        }

        $learningPathChildAttempt->calculateAndSetTotalTime();
        $this->learningPathTrackingRepository->update($learningPathChildAttempt);
    }

    /**
     * Returns the progress for a given user in a given learning path
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return int
     */
    public function getLearningPathProgress(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode = null
    )
    {
        /** @var LearningPathTreeNode[] $nodes */
        $nodes = array();
        $nodes[] = $learningPathTreeNode;
        $nodes = array_merge($nodes, $learningPathTreeNode->getDescendantNodes());

        $nodesCompleted = 0;

        foreach ($nodes as $node)
        {
            if ($this->isLearningPathTreeNodeCompleted($learningPath, $user, $node))
            {
                $nodesCompleted ++;
            }
        }

        $progress = (int) round(($nodesCompleted / count($nodes)) * 100);

        return $progress > 100 ? 100 : $progress;
    }

    /**
     * Checks if a given learning path tree node is completed
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return bool
     */
    public function isLearningPathTreeNodeCompleted(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $cacheKey = md5($learningPath->getId() . ':' . $user->getId() . ':' . $learningPathTreeNode->getId());

        if (!array_key_exists($cacheKey, $this->learningPathTreeNodesCompletedCache))
        {
            $this->learningPathTreeNodesCompletedCache[$cacheKey] =
                $this->calculateLearningPathTreeNodeCompleted($learningPath, $user, $learningPathTreeNode);
        }

        return $this->learningPathTreeNodesCompletedCache[$cacheKey];
    }

    /**
     * Determines whether or not the learning path tree node is completed by checking the tracking and every subitem
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return bool
     */
    protected function calculateLearningPathTreeNodeCompleted(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $learningPathAttempt = $this->learningPathAttemptService->getLearningPathAttemptForUser($learningPath, $user);

        if (!$learningPathAttempt instanceof LearningPathAttempt)
        {
            return false;
        }

        $learningPathChildAttempts =
            $this->learningPathAttemptService->getLearningPathChildAttempts($learningPathAttempt);

        if ($learningPathTreeNode->hasChildNodes())
        {
            $completed = true;

            foreach ($learningPathTreeNode->getChildNodes() as $childLearningPathTreeNode)
            {
                $completed &= $this->isLearningPathTreeNodeCompleted(
                    $learningPath, $user, $childLearningPathTreeNode
                );
            }

            return $completed;
        }

        /** @var LearningPathChildAttempt[] $learningPathTreeNodeAttempts */
        $learningPathTreeNodeAttempts = $learningPathChildAttempts[$learningPathTreeNode->getId()];

        foreach ($learningPathTreeNodeAttempts as $learningPathTreeNodeAttempt)
        {
            if ($learningPathTreeNodeAttempt->isFinished())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether or not the maximum number of attempts is reached for the given LearningPath, User
     * and LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return bool
     */
    public function isMaximumAttemptsReachedForAssessment(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $this->validateLearningPathTreeNodeIsAssessment($learningPathTreeNode);

        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $learningPathChildAttempts =
            $this->learningPathAttemptService->getLearningPathChildAttempts($learningPathAttempt);

        /** @var Assessment $assessment */
        $assessment = $learningPathTreeNode->getContentObject();

        return $assessment->get_maximum_attempts() > 0 &&
            count($learningPathChildAttempts) > $assessment->get_maximum_attempts();
    }

    /**
     * Saves the answer, score and hint for a question for the given LearningPath, User, LearningPathTreeNode and
     * Question identifier
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param int $questionIdentifier
     * @param string $answer
     * @param int $score
     * @param string $hint
     */
    public function saveAnswerForQuestion(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode, $questionIdentifier,
        $answer = '', $score = 0, $hint = ''
    )
    {
        $this->validateLearningPathTreeNodeIsAssessment($learningPathTreeNode);

        $learningPathQuestionAttempts = $this->getQuestionAttempts($learningPath, $user, $learningPathTreeNode);
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
     * Saves the assessment score for the given LearningPath, User and LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param int $assessmentScore
     */
    public function saveAssessmentScore(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode, $assessmentScore = 0
    )
    {
        $this->validateLearningPathTreeNodeIsAssessment($learningPathTreeNode);

        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );

        $activeAttempt->set_score($assessmentScore);
        $activeAttempt->calculateAndSetTotalTime();
        $activeAttempt->set_status($this->determineStatusForAssessmentByScore($learningPathTreeNode, $assessmentScore));

        $this->learningPathTrackingRepository->update($activeAttempt);
    }

    /**
     * Changes the assessment score for the given LearningPath, User, LearningPathTreeNode and
     * LearningPathChildAttemptId
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param null $learningPathChildAttemptId
     * @param int $newScore
     */
    public function changeAssessmentScore(
        LearningPath $learningPath, User $user,
        LearningPathTreeNode $learningPathTreeNode, $learningPathChildAttemptId, $newScore = 0
    )
    {
        $learningPathChildAttempt = $this->getLearningPathChildAttemptById(
            $learningPath, $user, $learningPathTreeNode, $learningPathChildAttemptId
        );

        $learningPathChildAttempt->set_score($newScore);

        $learningPathChildAttempt->set_status(
            $this->determineStatusForAssessmentByScore($learningPathTreeNode, $newScore)
        );

        $this->learningPathTrackingRepository->update($learningPathChildAttempt);
    }

    /**
     * Changes the score and feedback for a given question in a given LearningPathChildAttempt identifier by ID
     * for a given LearningPath, User and LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param int $learningPathChildAttemptId
     * @param int $questionIdentifier
     * @param int $score
     * @param string $feedback
     */
    public function changeQuestionScoreAndFeedback(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode, $learningPathChildAttemptId,
        $questionIdentifier, $score = 0, $feedback = ''
    )
    {
        $learningPathQuestionAttempts = $this->getQuestionAttempts(
            $learningPath, $user, $learningPathTreeNode, $learningPathChildAttemptId
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
     * Determines the status for a given assessment LearningPathTreeNode based on the given score
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param int $assessmentScore
     *
     * @return string
     */
    protected function determineStatusForAssessmentByScore(
        LearningPathTreeNode $learningPathTreeNode, $assessmentScore = 0
    )
    {
        $masteryScore = $learningPathTreeNode->getLearningPathChild()->getMasteryScore();
        if ($masteryScore > 0)
        {
            $status = ($assessmentScore >= $masteryScore) ? LearningPathChildAttempt::STATUS_PASSED :
                LearningPathChildAttempt::STATUS_FAILED;
        }
        else
        {
            $status = LearningPathChildAttempt::STATUS_COMPLETED;
        }

        return $status;
    }

    /**
     * Returns the question attempts for a given LearningPath, User and LearningPathTreeNode
     * using the given attempt (by id) or the active attempt
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param int $learningPathChildAttemptId
     *
     * @return LearningPathQuestionAttempt[]
     */
    public function getQuestionAttempts(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode,
        $learningPathChildAttemptId = null
    )
    {
        $this->validateLearningPathTreeNodeIsAssessment($learningPathTreeNode);

        if (is_null($learningPathChildAttemptId))
        {
            $learningPathAttempt =
                $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
            $learningPathChildAttempt = $this->learningPathAttemptService->getOrCreateActiveLearningPathChildAttempt(
                $learningPathAttempt, $learningPathTreeNode
            );
        }
        else
        {
            $learningPathChildAttempt = $this->getLearningPathChildAttemptById(
                $learningPath, $user, $learningPathTreeNode, $learningPathChildAttemptId
            );
        }

        $questionAttempts = $this->learningPathAttemptService->getLearningPathQuestionAttempts(
            $learningPathChildAttempt
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
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param int[] $questionIdentifiers
     *
     * @return LearningPathQuestionAttempt[]
     */
    public function registerQuestionAttempts(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode,
        $questionIdentifiers = array()
    )
    {
        $this->validateLearningPathTreeNodeIsAssessment($learningPathTreeNode);

        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->learningPathAttemptService->getOrCreateActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
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
     * Returns a LearningPathChildAttempt by a given id, validating that it belongs to the attempt of the given user
     * and learning path tree node
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param $learningPathChildAttemptId
     *
     * @return LearningPathChildAttempt
     */
    public function getLearningPathChildAttemptById(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode, $learningPathChildAttemptId
    )
    {
        $learningPathTreeNodeAttempts = $this->getLearningPathTreeNodeAttempts(
            $learningPath, $user, $learningPathTreeNode
        );

        foreach ($learningPathTreeNodeAttempts as $learningPathTreeNodeAttempt)
        {
            if ($learningPathTreeNodeAttempt->getId() == $learningPathChildAttemptId)
            {
                return $learningPathTreeNodeAttempt;
            }
        }

        throw new \RuntimeException('Could not find the LearningPathChildAttempt by id ' . $learningPathChildAttemptId);
    }

    /**
     * Deletes the learning path child attempt by a given id. Verifies that this identifier belongs to the attempts
     * for the given learning path and user
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param int $learningPathChildAttemptId
     */
    public function deleteLearningPathChildAttemptById(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode, $learningPathChildAttemptId
    )
    {
        $learningPathTreeNodeAttempt = $this->getLearningPathChildAttemptById(
            $learningPath, $user, $learningPathTreeNode, $learningPathChildAttemptId
        );

        $this->learningPathAttemptService->deleteLearningPathChildAttempt($learningPathTreeNodeAttempt);
    }

    /**
     * Deletes the learning path child attempts for a given LearningPathTreeNode.
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     */
    public function deleteLearningPathChildAttemptsForLearningPathTreeNode(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $learningPathTreeNodeAttempts = $this->getLearningPathTreeNodeAttempts(
            $learningPath, $user, $learningPathTreeNode
        );

        foreach ($learningPathTreeNodeAttempts as $learningPathTreeNodeAttempt)
        {
            $this->learningPathAttemptService->deleteLearningPathChildAttempt($learningPathTreeNodeAttempt);
        }
    }

    /**
     * Deletes the learning path attempt for the given user
     *
     * @param LearningPath $learningPath
     * @param User $user
     */
    public function deleteLearningPathAttempt(LearningPath $learningPath, User $user)
    {
        $learningPathAttempt = $this->learningPathAttemptService->getLearningPathAttemptForUser($learningPath, $user);

        if ($learningPathAttempt instanceof LearningPathAttempt)
        {
            $this->learningPathAttemptService->deleteLearningPathAttempt($learningPathAttempt);
        }
    }

    /**
     * Validates that the given LearningPathTreeNode contains an assessment content object
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     */
    protected function validateLearningPathTreeNodeIsAssessment(LearningPathTreeNode $learningPathTreeNode)
    {
        if (!$learningPathTreeNode->getContentObject() instanceof Assessment)
        {
            throw new \RuntimeException(
                'The given LearningPathTreeNode is not connected to an assessment'
            );
        }
    }

    /**
     * Returns whether or not the given LearningPathTreeNode is blocked for the given user
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return bool
     */
    public function isCurrentLearningPathTreeNodeBlocked(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);

        $learningPathChildAttempts =
            $this->learningPathAttemptService->getLearningPathChildAttempts($learningPathAttempt);

        $previousNodes = $learningPathTreeNode->getPreviousNodes();

        foreach ($previousNodes as $previousNode)
        {
            if (
                $learningPath->enforcesDefaultTraversingOrder() ||
                (!$previousNode->isRootNode() && $previousNode->getLearningPathChild()->isBlocked())
            )
            {
                if (count($learningPathChildAttempts[$previousNode->getId()]) == 0)
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns a list of the nodes that are responsible that a step can not be taken
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathTreeNode[]
     */
    public function getResponsibleNodesForBlockedLearningPathTreeNode(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $learningPathAttempt =
            $this->learningPathAttemptService->getOrCreateLearningPathAttemptForUser($learningPath, $user);

        $learningPathChildAttempts =
            $this->learningPathAttemptService->getLearningPathChildAttempts($learningPathAttempt);

        $previousNodes = $learningPathTreeNode->getPreviousNodes();

        $blockedNodes = array();

        foreach ($previousNodes as $previousNode)
        {
            if (
                $learningPath->enforcesDefaultTraversingOrder() ||
                (!$previousNode->isRootNode() && $previousNode->getLearningPathChild()->isBlocked())
            )
            {
                if (count($learningPathChildAttempts[$previousNode->getId()]) == 0)
                {
                    $blockedNodes[] = $previousNode;
                }
            }
        }

        return $blockedNodes;
    }

    /**
     * Returns whether or not the LearningPathTreeNode has attempts
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return bool
     */
    public function hasLearningPathTreeNodeAttempts(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        return $this->countLearningPathTreeNodeAttempts($learningPath, $user, $learningPathTreeNode) > 0;
    }

    /**
     * Returns the number of attempts for a given LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return int
     */
    public function countLearningPathTreeNodeAttempts(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        return count($this->getLearningPathTreeNodeAttempts($learningPath, $user, $learningPathTreeNode));
    }

    /**
     * Returns the attempts for a given LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathChildAttempt[]
     */
    public function getLearningPathTreeNodeAttempts(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $learningPathAttempt = $this->learningPathAttemptService->getLearningPathAttemptForUser($learningPath, $user);

        if (!$learningPathAttempt instanceof LearningPathAttempt)
        {
            return array();
        }

        return $this->learningPathAttemptService->getLearningPathChildAttemptsForLearningPathTreeNode(
            $learningPathAttempt, $learningPathTreeNode
        );
    }

    /**
     * Returns the total time spent in the given LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return int|string
     */
    public function getTotalTimeSpentInLearningPathTreeNode(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $totalTime = 0;

        $learningPathAttempts = $this->getLearningPathTreeNodeAttempts($learningPath, $user, $learningPathTreeNode);
        foreach ($learningPathAttempts as $learningPathAttempt)
        {
            $totalTime += $learningPathAttempt->get_total_time();
        }

        if ($learningPathTreeNode->hasChildNodes())
        {
            foreach ($learningPathTreeNode->getChildNodes() as $childNode)
            {
                $totalTime += $this->getTotalTimeSpentInLearningPathTreeNode($learningPath, $user, $childNode);
            }
        }

        return $totalTime;
    }

    /**
     * Returns the average score of the given user  in the given LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return float
     */
    public function getAverageScoreInLearningPathTreeNode(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        try
        {
            $this->validateLearningPathTreeNodeIsAssessment($learningPathTreeNode);
        }
        catch (\Exception $ex)
        {
            return null;
        }

        $totalScore = 0;

        $learningPathAttempts = $this->getLearningPathTreeNodeAttempts($learningPath, $user, $learningPathTreeNode);

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
     * Returns the maximum score of the given user in the given LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return int
     */
    public function getMaximumScoreInLearningPathTreeNode(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $this->validateLearningPathTreeNodeIsAssessment($learningPathTreeNode);

        $maximumScore = 0;

        $learningPathAttempts = $this->getLearningPathTreeNodeAttempts($learningPath, $user, $learningPathTreeNode);

        foreach ($learningPathAttempts as $learningPathAttempt)
        {
            $maximumScore = $maximumScore < $learningPathAttempt->get_score() ?
                (int) $learningPathAttempt->get_score() : $maximumScore;
        }

        return $maximumScore;
    }

    /**
     * Returns the minimum score of the given user in the given LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return int
     */
    public function getMinimumScoreInLearningPathTreeNode(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $this->validateLearningPathTreeNodeIsAssessment($learningPathTreeNode);

        $minimumScore = null;

        $learningPathAttempts = $this->getLearningPathTreeNodeAttempts($learningPath, $user, $learningPathTreeNode);

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
     * Returns the score for the last attempt of the given user in the given LearningPathTreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return string
     */
    public function getLastAttemptScoreForLearningPathTreeNode(
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $this->validateLearningPathTreeNodeIsAssessment($learningPathTreeNode);
        $learningPathChildAttempts =
            $this->getLearningPathTreeNodeAttempts($learningPath, $user, $learningPathTreeNode);

        $learningPathChildAttempt = array_pop($learningPathChildAttempts);

        if (!$learningPathChildAttempt instanceof LearningPathChildAttempt)
        {
            return 0;
        }

        return (int) $learningPathChildAttempt->get_score();
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
     * @param LearningPathTreeNode|null $learningPathTreeNode
     * @param Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getLearningPathAttemptsWithUser(
        LearningPath $learningPath, LearningPathTreeNode $learningPathTreeNode = null, Condition $condition = null,
        $offset = 0, $count = 0, $orderBy = array()
    )
    {
        $learningPathChildIds = $learningPathTreeNode instanceof LearningPathTreeNode ?
            $learningPathTreeNode->getLearningPathChildIdsFromSelfAndDescendants() : array();

        return $this->learningPathTrackingRepository->findLearningPathAttemptsWithUser(
            $learningPath, $learningPathChildIds, $condition, $offset, $count, $orderBy
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
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getTargetUsersWithLearningPathAttempts(
        LearningPath $learningPath, LearningPathTreeNode $learningPathTreeNode,
            Condition $condition = null, $offset = 0, $count = 0, $orderBy = array()
    )
    {
        $learningPathChildIds = $learningPathTreeNode instanceof LearningPathTreeNode ?
            $learningPathTreeNode->getLearningPathChildIdsFromSelfAndDescendants() : array();

        return $this->learningPathTrackingRepository->findTargetUsersWithLearningPathAttempts(
            $learningPath, $learningPathChildIds, $condition, $offset, $count, $orderBy
        );
    }

    /**
     * Counts the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithoutLearningPathAttempts(LearningPath $learningPath)
    {
        return $this->learningPathTrackingRepository->findTargetUsersWithoutLearningPathAttempts($learningPath);
    }

    /**
     * Counts the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithoutLearningPathAttempts(LearningPath $learningPath)
    {
        return $this->learningPathTrackingRepository->countTargetUsersWithoutLearningPathAttempts($learningPath);
    }

    /**
     * Counts the target users with attempts on a learning path that are completed
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithFullLearningPathAttempts(LearningPath $learningPath)
    {
        return $this->learningPathTrackingRepository->countTargetUsersWithFullLearningPathAttempts($learningPath);
    }

    /**
     * Finds the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithPartialLearningPathAttempts(LearningPath $learningPath)
    {
        return $this->learningPathTrackingRepository->findTargetUsersWithPartialLearningPathAttempts($learningPath);
    }

    /**
     * Counts the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithPartialLearningPathAttempts(LearningPath $learningPath)
    {
        return $this->learningPathTrackingRepository->countTargetUsersWithPartialLearningPathAttempts($learningPath);
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
