<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
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
        $treeNodeAttempt->setTreeNodeDataId($treeNode->getId());
        $treeNodeAttempt->set_start_time(time());
        $treeNodeAttempt->set_total_time(0);
        $treeNodeAttempt->set_score(0);
        $treeNodeAttempt->setCompleted(false);

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
                $attempt_data[$treeNodeAttempt->getTreeNodeDataId()][] = $treeNodeAttempt;
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
     * Returns the TreeNodeQuestionAttempt objects for a given TreeNodeAttempt
     *
     * @param TreeNodeAttempt $learningPathItemAttempt
     *
     * @return TreeNodeQuestionAttempt[]
     */
    public function getTreeNodeQuestionAttempts(
        TreeNodeAttempt $learningPathItemAttempt
    )
    {
        $treeNodeQuestionAttempts =
            $this->trackingRepository->findTreeNodeQuestionAttempts($learningPathItemAttempt);

        $treeNodeQuestionAttemptsPerQuestion = array();

        foreach ($treeNodeQuestionAttempts as $treeNodeQuestionAttempt)
        {
            $treeNodeQuestionAttemptsPerQuestion[$treeNodeQuestionAttempt->get_question_complex_id()] =
                $treeNodeQuestionAttempt;
        }

        return $treeNodeQuestionAttemptsPerQuestion;
    }

    /**
     * Creates a TreeNodeQuestionAttempt for a given TreeNodeAttempt and question identifier
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param int $questionId
     *
     * @return TreeNodeQuestionAttempt
     */
    public function createTreeNodeQuestionAttempt(TreeNodeAttempt $treeNodeAttempt, $questionId
    )
    {
        $treeNodeQuestionAttempt =
            $this->trackingParameters->createTreeNodeQuestionAttemptInstance();

        $treeNodeQuestionAttempt->setTreeNodeAttemptId($treeNodeAttempt->getId());
        $treeNodeQuestionAttempt->set_question_complex_id($questionId);
        $treeNodeQuestionAttempt->set_answer('');
        $treeNodeQuestionAttempt->set_score(0);
        $treeNodeQuestionAttempt->set_feedback('');
        $treeNodeQuestionAttempt->set_hint(0);

        $this->trackingRepository->create($treeNodeQuestionAttempt);
        $this->clearTreeNodeQuestionAttemptCache();

        return $treeNodeQuestionAttempt;
    }

    /**
     * Clears the TreeNodeAttempt cache
     */
    public function clearTreeNodeQuestionAttemptCache()
    {
        $this->trackingRepository->clearTreeNodeQuestionAttemptCache();
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
