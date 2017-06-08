<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service to manage the attempt data classes of a learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttemptService
{
    /**
     * @var TrackingRepositoryInterface
     */
    protected $trackingRepository;

    /**
     * @var TrackingParametersInterface
     */
    protected $trackingParameters;

    /**
     * @var LearningPathAttempt[][]
     */
    protected $learningPathAttemptCache;

    /**
     * @var LearningPathAttempt[][]
     */
    protected $existingLearningPathAttemptCache;

    /**
     * @var TreeNodeAttempt[][]
     */
    protected $activeTreeNodeAttemptCache;

    /**
     * @var TreeNodeAttempt[][][]
     */
    protected $treeNodeAttemptsForLearningPathAttemptCache;

    /**
     * TrackingService constructor.
     *
     * @param TrackingRepositoryInterface $trackingRepository
     * @param TrackingParametersInterface $trackingParameters
     */
    public function __construct(
        TrackingRepositoryInterface $trackingRepository,
        TrackingParametersInterface $trackingParameters
    )
    {
        $this->trackingRepository = $trackingRepository;
        $this->trackingParameters = $trackingParameters;
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
                $this->trackingRepository->findLearningPathAttemptForUser($learningPath, $user);
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
        $learningPathAttempt = $this->trackingParameters->createLearningPathAttemptInstance();

        $learningPathAttempt->setLearningPathId($learningPath->getId());
        $learningPathAttempt->set_user_id($user->getId());
        $learningPathAttempt->set_progress(0);

        $this->trackingRepository->create($learningPathAttempt);
        $this->trackingRepository->clearLearningPathAttemptCache();

        return $learningPathAttempt;
    }

    /**
     * Returns the existing and active TreeNodeAttempt or creates a new one for the given
     * LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeAttempt
     */
    public function getOrCreateActiveTreeNodeAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $learningPathAttemptId = $learningPathAttempt->getId();
        $treeNodeId = $treeNode->getId();

        if (!array_key_exists($learningPathAttemptId, $this->activeTreeNodeAttemptCache) || !array_key_exists(
                $treeNodeId, $this->activeTreeNodeAttemptCache[$learningPathAttemptId]
            )
        )
        {
            $activeTreeNodeAttempt = $this->getActiveTreeNodeAttempt(
                $learningPathAttempt, $treeNode
            );

            if ($activeTreeNodeAttempt instanceof TreeNodeAttempt)
            {
                $activeTreeNodeAttempt->set_start_time(time());
                $this->trackingRepository->update($activeTreeNodeAttempt);
            }
            else
            {
                $activeTreeNodeAttempt =
                    $this->createTreeNodeAttempt($learningPathAttempt, $treeNode);
            }

            $this->activeTreeNodeAttemptCache[$learningPathAttemptId][$treeNodeId] =
                $activeTreeNodeAttempt;
        }

        return $this->activeTreeNodeAttemptCache[$learningPathAttemptId][$treeNodeId];
    }

    /**
     * Returns the active TreeNodeAttempt for a given LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeAttempt
     */
    public function getActiveTreeNodeAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        return $this->trackingRepository->findActiveTreeNodeAttempt(
            $learningPathAttempt, $treeNode
        );
    }

    /**
     * Creates a TreeNodeAttempt for a given LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeAttempt
     */
    public function createTreeNodeAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $treeNodeAttempt = $this->trackingParameters->createTreeNodeAttemptInstance();

        $treeNodeAttempt->set_learning_path_attempt_id($learningPathAttempt->getId());
        $treeNodeAttempt->set_learning_path_item_id($treeNode->getId());
        $treeNodeAttempt->set_start_time(time());
        $treeNodeAttempt->set_total_time(0);
        $treeNodeAttempt->set_score(0);
        $treeNodeAttempt->set_min_score(0);
        $treeNodeAttempt->set_max_score(0);
        $treeNodeAttempt->set_status(TreeNodeAttempt::STATUS_NOT_ATTEMPTED);

        $this->trackingRepository->create($treeNodeAttempt);
        $this->clearTreeNodeAttemptCache($learningPathAttempt);

        return $treeNodeAttempt;
    }

    /**
     * Clears the TreeNodeAttempt cache for the given LearningPathAttempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     */
    public function clearTreeNodeAttemptCache(LearningPathAttempt $learningPathAttempt)
    {
        $this->trackingRepository->clearTreeNodeAttemptCache();

        unset($this->treeNodeAttemptsForLearningPathAttemptCache[$learningPathAttempt->getId()]);
    }

    /**
     * Returns the learning path item attempts, sorted by the children to which they belong
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return TreeNodeAttempt[][]
     */
    public function getTreeNodeAttempts(LearningPathAttempt $learningPathAttempt)
    {
        if (!array_key_exists(
            $learningPathAttempt->getId(), $this->treeNodeAttemptsForLearningPathAttemptCache
        )
        )
        {
            $treeNodeAttempts =
                $this->trackingRepository->findTreeNodeAttempts($learningPathAttempt);

            $attempt_data = array();

            foreach ($treeNodeAttempts as $treeNodeAttempt)
            {
                $attempt_data[$treeNodeAttempt->get_learning_path_item_id()][] = $treeNodeAttempt;
            }

            $this->treeNodeAttemptsForLearningPathAttemptCache[$learningPathAttempt->getId()] = $attempt_data;
        }

        return $this->treeNodeAttemptsForLearningPathAttemptCache[$learningPathAttempt->getId()];
    }

    /**
     * Returns the TreeNodeAttempt objects for a given learning path tree node
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeAttempt[]
     */
    public function getTreeNodeAttemptsForTreeNode(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $treeNodeAttempts = $this->getTreeNodeAttempts($learningPathAttempt);

        if(array_key_exists($treeNode->getId(), $treeNodeAttempts))
        {
            return $treeNodeAttempts[$treeNode->getId()];
        }

        return array();
    }

    /**
     * Returns the LearningPathQuestionAttempt objects for a given TreeNodeAttempt
     *
     * @param TreeNodeAttempt $learningPathItemAttempt
     *
     * @return LearningPathQuestionAttempt[]
     */
    public function getLearningPathQuestionAttempts(
        TreeNodeAttempt $learningPathItemAttempt
    )
    {
        $learningPathQuestionAttempts =
            $this->trackingRepository->findLearningPathQuestionAttempts($learningPathItemAttempt);

        $learningPathQuestionAttemptsPerQuestion = array();

        foreach ($learningPathQuestionAttempts as $learningPathQuestionAttempt)
        {
            $learningPathQuestionAttemptsPerQuestion[$learningPathQuestionAttempt->get_question_complex_id()] =
                $learningPathQuestionAttempt;
        }

        return $learningPathQuestionAttemptsPerQuestion;
    }

    /**
     * Creates a LearningPathQuestionAttempt for a given TreeNodeAttempt and question identifier
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param int $questionId
     *
     * @return LearningPathQuestionAttempt
     */
    public function createLearningPathQuestionAttempt(TreeNodeAttempt $treeNodeAttempt, $questionId
    )
    {
        $learningPathQuestionAttempt =
            $this->trackingParameters->createLearningPathQuestionAttemptInstance();

        $learningPathQuestionAttempt->set_item_attempt_id($treeNodeAttempt->getId());
        $learningPathQuestionAttempt->set_question_complex_id($questionId);
        $learningPathQuestionAttempt->set_answer('');
        $learningPathQuestionAttempt->set_score(0);
        $learningPathQuestionAttempt->set_feedback('');
        $learningPathQuestionAttempt->set_hint(0);

        $this->trackingRepository->create($learningPathQuestionAttempt);

        return $learningPathQuestionAttempt;
    }

    /**
     * Deletes a TreeNodeAttempt
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     */
    public function deleteTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt)
    {
        $this->trackingRepository->delete($treeNodeAttempt);
    }

    /**
     * Deletes a LearningPathAttempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     */
    public function deleteLearningPathAttempt(LearningPathAttempt $learningPathAttempt)
    {
        $this->trackingRepository->delete($learningPathAttempt);
    }
}