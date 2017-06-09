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
     * @var TreeNodeAttempt[]
     */
    protected $activeTreeNodeAttemptCache;

    /**
     * @var TreeNodeAttempt[][][]
     */
    protected $treeNodeAttemptCache;

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
     * Returns the existing and active TreeNodeAttempt or creates a new one for the given
     * LearningPathAttempt and TreeNode
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     *
     * @return TreeNodeAttempt
     */
    public function getOrCreateActiveTreeNodeAttempt(
        LearningPath $learningPath, TreeNode $treeNode, User $user
    )
    {
        $cacheKey = md5($learningPath->getId() . ':' . $treeNode->getId() . ':' . $user->getId());


        if (!array_key_exists($cacheKey, $this->activeTreeNodeAttemptCache))
        {
            $activeTreeNodeAttempt = $this->getActiveTreeNodeAttempt($learningPath, $treeNode, $user);

            if ($activeTreeNodeAttempt instanceof TreeNodeAttempt)
            {
                $activeTreeNodeAttempt->set_start_time(time());
                $this->trackingRepository->update($activeTreeNodeAttempt);
            }
            else
            {
                $activeTreeNodeAttempt = $this->createTreeNodeAttempt($learningPath, $treeNode, $user);
            }

            $this->activeTreeNodeAttemptCache[$cacheKey] =
                $activeTreeNodeAttempt;
        }

        return $this->activeTreeNodeAttemptCache[$cacheKey];
    }

    /**
     * Returns the active TreeNodeAttempt for a given LearningPathAttempt and TreeNode
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @param User $user
     *
     * @return TreeNodeAttempt
     */
    public function getActiveTreeNodeAttempt(
        LearningPath $learningPath, TreeNode $treeNode, User $user
    )
    {
        return $this->trackingRepository->findActiveTreeNodeAttempt($learningPath, $treeNode, $user);
    }

    /**
     * Creates a TreeNodeAttempt for a given LearningPathAttempt and TreeNode
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @param User $user
     *
     * @return TreeNodeAttempt
     */
    public function createTreeNodeAttempt(
        LearningPath $learningPath, TreeNode $treeNode, User $user
    )
    {
        $treeNodeAttempt = $this->trackingParameters->createTreeNodeAttemptInstance();

        $treeNodeAttempt->setLearningPathId($learningPath->getId());
        $treeNodeAttempt->setUserId($user->getId());
        $treeNodeAttempt->set_learning_path_item_id($treeNode->getId());
        $treeNodeAttempt->set_start_time(time());
        $treeNodeAttempt->set_total_time(0);
        $treeNodeAttempt->set_score(0);
        $treeNodeAttempt->set_min_score(0);
        $treeNodeAttempt->set_max_score(0);
        $treeNodeAttempt->set_status(TreeNodeAttempt::STATUS_NOT_ATTEMPTED);

        $this->trackingRepository->create($treeNodeAttempt);
        $this->clearTreeNodeAttemptCache();

        return $treeNodeAttempt;
    }

    /**
     * Clears the TreeNodeAttempt cache
     */
    public function clearTreeNodeAttemptCache()
    {
        $this->trackingRepository->clearTreeNodeAttemptCache();
    }

    /**
     * Returns the learning path item attempts, sorted by the children to which they belong
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt[][]
     */
    public function getTreeNodeAttempts(LearningPath $learningPath, User $user)
    {
        $cacheKey = md5($learningPath->getId() . ':' . $user->getId());

        if (!array_key_exists($cacheKey, $this->treeNodeAttemptCache))
        {
            $treeNodeAttempts = $this->trackingRepository->findTreeNodeAttempts($learningPath, $user);

            $attempt_data = array();

            foreach ($treeNodeAttempts as $treeNodeAttempt)
            {
                $attempt_data[$treeNodeAttempt->get_learning_path_item_id()][] = $treeNodeAttempt;
            }

            $this->treeNodeAttemptCache[$cacheKey] = $attempt_data;
        }

        return $this->treeNodeAttemptCache[$cacheKey];
    }

    /**
     * Returns the TreeNodeAttempt objects for a given learning path tree node
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return TreeNodeAttempt[]
     */
    public function getTreeNodeAttemptsForTreeNode(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $treeNodeAttempts = $this->getTreeNodeAttempts($learningPath, $user);

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
}