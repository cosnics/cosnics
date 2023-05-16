<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use RuntimeException;

/**
 * Service to track attempts on learning paths for a specific node and user
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttemptTrackingService
{
    /**
     * @var AttemptService
     */
    protected $attemptService;

    /**
     * @var TrackingRepositoryInterface
     */
    protected $trackingRepository;

    /**
     * AttemptTrackingService constructor.
     *
     * @param AttemptService $attemptService
     * @param TrackingRepositoryInterface $trackingRepository
     */
    public function __construct(AttemptService $attemptService, TrackingRepositoryInterface $trackingRepository)
    {
        $this->attemptService = $attemptService;
        $this->trackingRepository = $trackingRepository;
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
        return $this->getActiveAttempt($learningPath, $treeNode, $user)->getId();
    }

    /**
     * Returns the identifier for the active TreeNodeAttempt
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    public function getActiveAttempt(
        LearningPath $learningPath, TreeNode $treeNode, User $user
    )
    {
        return $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);
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

        throw new RuntimeException('Could not find the TreeNodeAttempt by id ' . $treeNodeAttemptId);
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
            $treeNode->getTreeNodeDataIdsFromSelfAndDescendants() : [];

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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLearningPathAttemptsWithUser(
        LearningPath $learningPath, TreeNode $treeNode = null, Condition $condition = null,
        $offset = 0, $count = 0, $orderBy = null
    )
    {
        $treeNodeDataIds = $treeNode instanceof TreeNode ?
            $treeNode->getTreeNodeDataIdsFromSelfAndDescendants() : [];

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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTargetUsersWithLearningPathAttempts(
        LearningPath $learningPath, TreeNode $treeNode = null,
        Condition $condition = null, $offset = 0, $count = 0, $orderBy = null
    )
    {
        $treeNodeDataIds = $treeNode instanceof TreeNode ?
            $treeNode->getTreeNodeDataIdsFromSelfAndDescendants() : [];

        return $this->trackingRepository->findTargetUsersWithLearningPathAttempts(
            $learningPath, $treeNodeDataIds, $condition, $offset, $count, $orderBy
        );
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