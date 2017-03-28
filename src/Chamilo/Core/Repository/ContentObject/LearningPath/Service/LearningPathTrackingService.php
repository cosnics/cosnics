<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service to manage the tracking of attempts in a learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingService
{
    /**
     * @var LearningPathTrackingRepository
     */
    protected $learningPathTrackingRepository;

    /**
     * @var LearningPathTrackingParametersInterface
     */
    protected $learningPathTrackingParameters;

    /**
     * @var LearningPathAttempt[][]
     */
    protected $learningPathAttemptCache;

    /**
     * @var LearningPathChildAttempt[][]
     */
    protected $activeLearningPathChildAttemptCache;

    /**
     * @var LearningPathChildAttempt[][][]
     */
    protected $learningPathChildAttemptsForLearningPathAttemptCache;

    /**
     * LearningPathTrackingService constructor.
     *
     * @param LearningPathTrackingRepository $learningPathTrackingRepository
     * @param LearningPathTrackingParametersInterface $learningPathTrackingParameters
     */
    public function __construct(
        LearningPathTrackingRepository $learningPathTrackingRepository,
        LearningPathTrackingParametersInterface $learningPathTrackingParameters
    )
    {
        $this->learningPathTrackingRepository = $learningPathTrackingRepository;
        $this->learningPathTrackingParameters = $learningPathTrackingParameters;
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
        $learningPathAttempt = $this->getOrCreateLearningPathAttemptForUser($learningPath, $user);

        $this->getOrCreateActiveLearningPathChildAttempt(
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
        $learningPathAttempt = $this->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->getOrCreateActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );

        $activeAttempt->set_status($newStatus);
        $this->learningPathTrackingRepository->update($activeAttempt);

        if ($activeAttempt->isFinished())
        {
            $this->recalculateLearningPathProgress($learningPath, $user, $learningPathTreeNode->getLearningPathTree());
        }
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
        $learningPathAttempt = $this->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $activeAttempt = $this->getOrCreateActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );

        $activeAttempt->set_total_time($activeAttempt->get_total_time() + (time() - $activeAttempt->get_start_time()));
        $this->learningPathTrackingRepository->update($activeAttempt);
    }

    /**
     * Recalculates and updates the progress of the learning path tree
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTree $learningPathTree
     */
    public function recalculateLearningPathProgress(
        LearningPath $learningPath, User $user, LearningPathTree $learningPathTree
    )
    {
        $nodesCompleted = 0;

        $learningPathAttempt = $this->getOrCreateLearningPathAttemptForUser($learningPath, $user);

        foreach ($learningPathTree->getLearningPathTreeNodes() as $learningPathTreeNode)
        {
            if ($this->isLearningPathTreeNodeCompleted($learningPath, $user, $learningPathTreeNode))
            {
                $nodesCompleted ++;
            }
        }

        $progress = round(($nodesCompleted / count($learningPathTree->getLearningPathTreeNodes())) * 100);
        $learningPathAttempt->set_progress($progress);

        $this->learningPathTrackingRepository->update($learningPathAttempt);
    }

    /**
     * Returns the progress for a given user in a given learning path
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return int
     */
    public function getLearningPathProgress(LearningPath $learningPath, User $user)
    {
        $learningPathAttempt = $this->getOrCreateLearningPathAttemptForUser($learningPath, $user);

        return $learningPathAttempt->get_progress();
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
        $learningPathAttempt = $this->getOrCreateLearningPathAttemptForUser($learningPath, $user);
        $learningPathChildAttempts = $this->getLearningPathChildAttempts($learningPathAttempt);

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
     * Returns the existing learning path attempt or creates a new one for the given learning path and user
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt
     */
    protected function getOrCreateLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        if (!array_key_exists($learningPath->getId(), $this->learningPathAttemptCache) &&
            !array_key_exists($user->getId(), $this->learningPathAttemptCache[$learningPath->getId()])
        )
        {
            $learningPathAttempt = $this->getLearningPathAttemptForUser($learningPath, $user);
            if (!$learningPathAttempt instanceof LearningPathAttempt)
            {
                $learningPathAttempt = $this->createLearningPathAttemptForUser($learningPath, $user);
            }

            $this->learningPathAttemptCache[$learningPath->getId()][$user->getId()] = $learningPathAttempt;
        }

        return $this->learningPathAttemptCache[$learningPath->getId()][$user->getId()];
    }

    /**
     * Returns a LearningPathAttempt for a given LearningPath and User
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt
     */
    protected function getLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        return $this->learningPathTrackingRepository->findLearningPathAttemptForUser($learningPath, $user);
    }

    /**
     * Creates a new LearningPathAttempt for a given LearningPath and User
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt
     */
    protected function createLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        $learningPathAttempt = $this->learningPathTrackingParameters->createLearningPathAttemptInstance();

        $learningPathAttempt->setLearningPathId($learningPath->getId());
        $learningPathAttempt->set_user_id($user->getId());
        $learningPathAttempt->set_progress(0);

        $this->learningPathTrackingRepository->create($learningPathAttempt);
        $this->learningPathTrackingRepository->clearLearningPathAttemptCache();

        return $learningPathAttempt;
    }

    /**
     * Returns the existing and active LearningPathChildAttempt or creates a new one for the given
     * LearningPathAttempt and LearningPathTreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathChildAttempt
     */
    protected function getOrCreateActiveLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $learningPathAttemptId = $learningPathAttempt->getId();
        $learningPathTreeNodeId = $learningPathTreeNode->getId();

        if (!array_key_exists($learningPathAttemptId, $this->activeLearningPathChildAttemptCache) && !array_key_exists(
                $learningPathTreeNodeId, $this->activeLearningPathChildAttemptCache[$learningPathAttemptId]
            )
        )
        {
            $activeLearningPathChildAttempt = $this->getActiveLearningPathChildAttempt(
                $learningPathAttempt, $learningPathTreeNode
            );

            if ($activeLearningPathChildAttempt instanceof LearningPathChildAttempt)
            {
                $activeLearningPathChildAttempt->set_start_time(time());
                $this->learningPathTrackingRepository->update($activeLearningPathChildAttempt);
            }
            else
            {
                $activeLearningPathChildAttempt =
                    $this->createLearningPathChildAttempt($learningPathAttempt, $learningPathTreeNode);
            }

            $this->activeLearningPathChildAttemptCache[$learningPathAttemptId][$learningPathTreeNodeId] =
                $activeLearningPathChildAttempt;
        }

        return $this->activeLearningPathChildAttemptCache[$learningPathAttemptId][$learningPathTreeNodeId];
    }

    /**
     * Returns the active LearningPathChildAttempt for a given LearningPathAttempt and LearningPathTreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathChildAttempt
     */
    protected function getActiveLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, LearningPathTreeNode $learningPathTreeNode
    )
    {
        return $this->learningPathTrackingRepository->findActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );
    }

    /**
     * Creates a LearningPathChildAttempt for a given LearningPathAttempt and LearningPathTreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathChildAttempt
     */
    protected function createLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $learningPathChildAttempt = $this->learningPathTrackingParameters->createLearningPathChildAttemptInstance();

        $learningPathChildAttempt->set_learning_path_attempt_id($learningPathAttempt->getId());
        $learningPathChildAttempt->set_learning_path_item_id($learningPathTreeNode->getId());
        $learningPathChildAttempt->set_start_time(time());
        $learningPathChildAttempt->set_total_time(0);
        $learningPathChildAttempt->set_score(0);
        $learningPathChildAttempt->set_min_score(0);
        $learningPathChildAttempt->set_max_score(0);
        $learningPathChildAttempt->set_status(LearningPathChildAttempt::STATUS_NOT_ATTEMPTED);

        $this->learningPathTrackingRepository->create($learningPathChildAttempt);

        return $learningPathChildAttempt;
    }

    /**
     * Returns the learning path item attempts, sorted by the children to which they belong
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return LearningPathChildAttempt[][]
     */
    protected function getLearningPathChildAttempts(LearningPathAttempt $learningPathAttempt)
    {
        if (!array_key_exists(
            $learningPathAttempt->getId(), $this->learningPathChildAttemptsForLearningPathAttemptCache
        )
        )
        {
            $learningPathChildAttempts =
                $this->learningPathTrackingRepository->findLearningPathChildAttempts($learningPathAttempt);

            $attempt_data = array();

            foreach ($learningPathChildAttempts as $learningPathChildAttempt)
            {
                $attempt_data[$learningPathChildAttempt->get_learning_path_item_id()][] = $learningPathChildAttempt;
            }

            $this->learningPathChildAttemptsForLearningPathAttemptCache[$learningPathAttempt->getId()] = $attempt_data;
        }

        return $this->learningPathChildAttemptsForLearningPathAttemptCache[$learningPathAttempt->getId()];
    }

    /**
     * Returns the LearningPathQuestionAttempt objects for a given LearningPathChildAttempt
     *
     * @param LearningPathChildAttempt $learningPathItemAttempt
     *
     * @return array
     */
    protected function getLearningPathQuestionAttempts(
        LearningPathChildAttempt $learningPathItemAttempt
    )
    {
        $learningPathQuestionAttempts =
            $this->learningPathTrackingRepository->findLearningPathQuestionAttempts($learningPathItemAttempt);

        $learningPathQuestionAttemptsPerQuestion = array();

        foreach ($learningPathQuestionAttempts as $learningPathQuestionAttempt)
        {
            $learningPathQuestionAttemptsPerQuestion[$learningPathQuestionAttempt->get_question_complex_id()][] =
                $learningPathQuestionAttempt;
        }

        return $learningPathQuestionAttemptsPerQuestion;
    }

    /**
     * Creates a LearningPathQuestionAttempt for a given LearningPathChildAttempt and question identifier
     *
     * @param LearningPathChildAttempt $learningPathChildAttempt
     * @param int $questionId
     *
     * @return LearningPathQuestionAttempt
     */
    protected function createLearningPathQuestionAttempt(LearningPathChildAttempt $learningPathChildAttempt, $questionId
    )
    {
        $learningPathQuestionAttempt =
            $this->learningPathTrackingParameters->createLearningPathQuestionAttemptInstance();

        $learningPathQuestionAttempt->set_item_attempt_id($learningPathChildAttempt->getId());
        $learningPathQuestionAttempt->set_question_complex_id($questionId);
        $learningPathQuestionAttempt->set_answer('');
        $learningPathQuestionAttempt->set_score(0);
        $learningPathQuestionAttempt->set_feedback('');
        $learningPathQuestionAttempt->set_hint(0);

        $this->learningPathTrackingRepository->create($learningPathQuestionAttempt);

        return $learningPathQuestionAttempt;
    }
}