<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeDataAttempt;
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
     * @var TreeNodeDataAttempt[][]
     */
    protected $activeTreeNodeDataAttemptCache;

    /**
     * @var TreeNodeDataAttempt[][][]
     */
    protected $treeNodeDataAttemptsForLearningPathAttemptCache;

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
     * Returns the existing and active TreeNodeDataAttempt or creates a new one for the given
     * LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeDataAttempt
     */
    public function getOrCreateActiveTreeNodeDataAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $learningPathAttemptId = $learningPathAttempt->getId();
        $treeNodeId = $treeNode->getId();

        if (!array_key_exists($learningPathAttemptId, $this->activeTreeNodeDataAttemptCache) || !array_key_exists(
                $treeNodeId, $this->activeTreeNodeDataAttemptCache[$learningPathAttemptId]
            )
        )
        {
            $activeTreeNodeDataAttempt = $this->getActiveTreeNodeDataAttempt(
                $learningPathAttempt, $treeNode
            );

            if ($activeTreeNodeDataAttempt instanceof TreeNodeDataAttempt)
            {
                $activeTreeNodeDataAttempt->set_start_time(time());
                $this->learningPathTrackingRepository->update($activeTreeNodeDataAttempt);
            }
            else
            {
                $activeTreeNodeDataAttempt =
                    $this->createTreeNodeDataAttempt($learningPathAttempt, $treeNode);
            }

            $this->activeTreeNodeDataAttemptCache[$learningPathAttemptId][$treeNodeId] =
                $activeTreeNodeDataAttempt;
        }

        return $this->activeTreeNodeDataAttemptCache[$learningPathAttemptId][$treeNodeId];
    }

    /**
     * Returns the active TreeNodeDataAttempt for a given LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeDataAttempt
     */
    public function getActiveTreeNodeDataAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        return $this->learningPathTrackingRepository->findActiveTreeNodeDataAttempt(
            $learningPathAttempt, $treeNode
        );
    }

    /**
     * Creates a TreeNodeDataAttempt for a given LearningPathAttempt and TreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeDataAttempt
     */
    public function createTreeNodeDataAttempt(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $treeNodeDataAttempt = $this->learningPathTrackingParameters->createTreeNodeDataAttemptInstance();

        $treeNodeDataAttempt->set_learning_path_attempt_id($learningPathAttempt->getId());
        $treeNodeDataAttempt->set_learning_path_item_id($treeNode->getId());
        $treeNodeDataAttempt->set_start_time(time());
        $treeNodeDataAttempt->set_total_time(0);
        $treeNodeDataAttempt->set_score(0);
        $treeNodeDataAttempt->set_min_score(0);
        $treeNodeDataAttempt->set_max_score(0);
        $treeNodeDataAttempt->set_status(TreeNodeDataAttempt::STATUS_NOT_ATTEMPTED);

        $this->learningPathTrackingRepository->create($treeNodeDataAttempt);
        $this->clearTreeNodeDataAttemptCache($learningPathAttempt);

        return $treeNodeDataAttempt;
    }

    /**
     * Clears the TreeNodeDataAttempt cache for the given LearningPathAttempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     */
    public function clearTreeNodeDataAttemptCache(LearningPathAttempt $learningPathAttempt)
    {
        $this->learningPathTrackingRepository->clearTreeNodeDataAttemptCache();

        unset($this->treeNodeDataAttemptsForLearningPathAttemptCache[$learningPathAttempt->getId()]);
    }

    /**
     * Returns the learning path item attempts, sorted by the children to which they belong
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return TreeNodeDataAttempt[][]
     */
    public function getTreeNodeDataAttempts(LearningPathAttempt $learningPathAttempt)
    {
        if (!array_key_exists(
            $learningPathAttempt->getId(), $this->treeNodeDataAttemptsForLearningPathAttemptCache
        )
        )
        {
            $treeNodeDataAttempts =
                $this->learningPathTrackingRepository->findTreeNodeDataAttempts($learningPathAttempt);

            $attempt_data = array();

            foreach ($treeNodeDataAttempts as $treeNodeDataAttempt)
            {
                $attempt_data[$treeNodeDataAttempt->get_learning_path_item_id()][] = $treeNodeDataAttempt;
            }

            $this->treeNodeDataAttemptsForLearningPathAttemptCache[$learningPathAttempt->getId()] = $attempt_data;
        }

        return $this->treeNodeDataAttemptsForLearningPathAttemptCache[$learningPathAttempt->getId()];
    }

    /**
     * Returns the TreeNodeDataAttempt objects for a given learning path tree node
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param TreeNode $treeNode
     *
     * @return TreeNodeDataAttempt[]
     */
    public function getTreeNodeDataAttemptsForTreeNode(
        LearningPathAttempt $learningPathAttempt, TreeNode $treeNode
    )
    {
        $treeNodeDataAttempts = $this->getTreeNodeDataAttempts($learningPathAttempt);

        if(array_key_exists($treeNode->getId(), $treeNodeDataAttempts))
        {
            return $treeNodeDataAttempts[$treeNode->getId()];
        }

        return array();
    }

    /**
     * Returns the LearningPathQuestionAttempt objects for a given TreeNodeDataAttempt
     *
     * @param TreeNodeDataAttempt $learningPathItemAttempt
     *
     * @return LearningPathQuestionAttempt[]
     */
    public function getLearningPathQuestionAttempts(
        TreeNodeDataAttempt $learningPathItemAttempt
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
     * Creates a LearningPathQuestionAttempt for a given TreeNodeDataAttempt and question identifier
     *
     * @param TreeNodeDataAttempt $treeNodeDataAttempt
     * @param int $questionId
     *
     * @return LearningPathQuestionAttempt
     */
    public function createLearningPathQuestionAttempt(TreeNodeDataAttempt $treeNodeDataAttempt, $questionId
    )
    {
        $learningPathQuestionAttempt =
            $this->learningPathTrackingParameters->createLearningPathQuestionAttemptInstance();

        $learningPathQuestionAttempt->set_item_attempt_id($treeNodeDataAttempt->getId());
        $learningPathQuestionAttempt->set_question_complex_id($questionId);
        $learningPathQuestionAttempt->set_answer('');
        $learningPathQuestionAttempt->set_score(0);
        $learningPathQuestionAttempt->set_feedback('');
        $learningPathQuestionAttempt->set_hint(0);

        $this->learningPathTrackingRepository->create($learningPathQuestionAttempt);

        return $learningPathQuestionAttempt;
    }

    /**
     * Deletes a TreeNodeDataAttempt
     *
     * @param TreeNodeDataAttempt $treeNodeDataAttempt
     */
    public function deleteTreeNodeDataAttempt(TreeNodeDataAttempt $treeNodeDataAttempt)
    {
        $this->learningPathTrackingRepository->delete($treeNodeDataAttempt);
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