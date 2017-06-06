<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service to manage the attempt data classes of a learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathAttemptService
{
    /**
     * @var LearningPathTrackingRepositoryInterface
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
     * @var LearningPathAttempt[][]
     */
    protected $existingLearningPathAttemptCache;

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
     * @param LearningPathTrackingRepositoryInterface $learningPathTrackingRepository
     * @param LearningPathTrackingParametersInterface $learningPathTrackingParameters
     */
    public function __construct(
        LearningPathTrackingRepositoryInterface $learningPathTrackingRepository,
        LearningPathTrackingParametersInterface $learningPathTrackingParameters
    )
    {
        $this->learningPathTrackingRepository = $learningPathTrackingRepository;
        $this->learningPathTrackingParameters = $learningPathTrackingParameters;
    }

    /**
     * Returns the existing learning path attempt or creates a new one for the given learning path and user
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt
     */
    public function getOrCreateLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        if (!array_key_exists($learningPath->getId(), $this->learningPathAttemptCache) ||
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
    public function getLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        if (!array_key_exists($learningPath->getId(), $this->existingLearningPathAttemptCache) ||
            !array_key_exists($user->getId(), $this->existingLearningPathAttemptCache[$learningPath->getId()])
        )
        {
            $this->existingLearningPathAttemptCache[$learningPath->getId()][$user->getId()] =
                $this->learningPathTrackingRepository->findLearningPathAttemptForUser($learningPath, $user);
        }

        return $this->existingLearningPathAttemptCache[$learningPath->getId()][$user->getId()];
    }

    /**
     * Creates a new LearningPathAttempt for a given LearningPath and User
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt
     */
    public function createLearningPathAttemptForUser(LearningPath $learningPath, User $user)
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
     * LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return LearningPathChildAttempt
     */
    public function getOrCreateActiveLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $learningPathAttemptId = $learningPathAttempt->getId();
        $treeNodeId = $treeNode->getId();

        if (!array_key_exists($learningPathAttemptId, $this->activeLearningPathChildAttemptCache) || !array_key_exists(
                $treeNodeId, $this->activeLearningPathChildAttemptCache[$learningPathAttemptId]
            )
        )
        {
            $activeLearningPathChildAttempt = $this->getActiveLearningPathChildAttempt(
                $learningPathAttempt, $treeNode
            );

            if ($activeLearningPathChildAttempt instanceof LearningPathChildAttempt)
            {
                $activeLearningPathChildAttempt->set_start_time(time());
                $this->learningPathTrackingRepository->update($activeLearningPathChildAttempt);
            }
            else
            {
                $activeLearningPathChildAttempt =
                    $this->createLearningPathChildAttempt($learningPathAttempt, $treeNode);
            }

            $this->activeLearningPathChildAttemptCache[$learningPathAttemptId][$treeNodeId] =
                $activeLearningPathChildAttempt;
        }

        return $this->activeLearningPathChildAttemptCache[$learningPathAttemptId][$treeNodeId];
    }

    /**
     * Returns the active LearningPathChildAttempt for a given LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return LearningPathChildAttempt
     */
    public function getActiveLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        return $this->learningPathTrackingRepository->findActiveLearningPathChildAttempt(
            $learningPathAttempt, $treeNode
        );
    }

    /**
     * Creates a LearningPathChildAttempt for a given LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return LearningPathChildAttempt
     */
    public function createLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $learningPathChildAttempt = $this->learningPathTrackingParameters->createLearningPathChildAttemptInstance();

        $learningPathChildAttempt->set_learning_path_attempt_id($learningPathAttempt->getId());
        $learningPathChildAttempt->set_learning_path_item_id($treeNode->getId());
        $learningPathChildAttempt->set_start_time(time());
        $learningPathChildAttempt->set_total_time(0);
        $learningPathChildAttempt->set_score(0);
        $learningPathChildAttempt->set_min_score(0);
        $learningPathChildAttempt->set_max_score(0);
        $learningPathChildAttempt->set_status(LearningPathChildAttempt::STATUS_NOT_ATTEMPTED);

        $this->learningPathTrackingRepository->create($learningPathChildAttempt);
        $this->clearLearningPathChildAttemptCache($learningPathAttempt);

        return $learningPathChildAttempt;
    }

    /**
     * Clears the LearningPathChildAttempt cache for the given LearningPathAttempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     */
    public function clearLearningPathChildAttemptCache(LearningPathAttempt $learningPathAttempt)
    {
        $this->learningPathTrackingRepository->clearLearningPathChildAttemptCache();

        unset($this->learningPathChildAttemptsForLearningPathAttemptCache[$learningPathAttempt->getId()]);
    }

    /**
     * Returns the learning path item attempts, sorted by the children to which they belong
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return LearningPathChildAttempt[][]
     */
    public function getLearningPathChildAttempts(LearningPathAttempt $learningPathAttempt)
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
     * Returns the LearningPathChildAttempt objects for a given learning path tree node
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return LearningPathChildAttempt[]
     */
    public function getLearningPathChildAttemptsForTreeNode(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $learningPathChildAttempts = $this->getLearningPathChildAttempts($learningPathAttempt);

        if(array_key_exists($treeNode->getId(), $learningPathChildAttempts))
        {
            return $learningPathChildAttempts[$treeNode->getId()];
        }

        return array();
    }

    /**
     * Returns the LearningPathQuestionAttempt objects for a given LearningPathChildAttempt
     *
     * @param LearningPathChildAttempt $learningPathItemAttempt
     *
     * @return LearningPathQuestionAttempt[]
     */
    public function getLearningPathQuestionAttempts(
        LearningPathChildAttempt $learningPathItemAttempt
    )
    {
        $learningPathQuestionAttempts =
            $this->learningPathTrackingRepository->findLearningPathQuestionAttempts($learningPathItemAttempt);

        $learningPathQuestionAttemptsPerQuestion = array();

        foreach ($learningPathQuestionAttempts as $learningPathQuestionAttempt)
        {
            $learningPathQuestionAttemptsPerQuestion[$learningPathQuestionAttempt->get_question_complex_id()] =
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
    public function createLearningPathQuestionAttempt(LearningPathChildAttempt $learningPathChildAttempt, $questionId
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

    /**
     * Deletes a LearningPathChildAttempt
     *
     * @param LearningPathChildAttempt $learningPathChildAttempt
     */
    public function deleteLearningPathChildAttempt(LearningPathChildAttempt $learningPathChildAttempt)
    {
        $this->learningPathTrackingRepository->delete($learningPathChildAttempt);
    }

    /**
     * Deletes a LearningPathAttempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     */
    public function deleteLearningPathAttempt(LearningPathAttempt $learningPathAttempt)
    {
        $this->learningPathTrackingRepository->delete($learningPathAttempt);
    }
}