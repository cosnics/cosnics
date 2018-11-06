<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\NotificationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationServiceBridge implements NotificationServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\NotificationService
     */
    protected $notificationService;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * AssignmentDataProvider constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUnseenNotificationsForUserAndTreeNode(User $user, TreeNode $treeNode)
    {
        return $this->notificationService->countUnseenNotificationsForUserAndPublication(
            $user, $treeNode, $this->contentObjectPublication
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $offset
     * @param int $count
     *
     * @return \Chamilo\Core\Notification\Domain\NotificationDTO[]
     */
    public function getNotificationsForUserAndTreeNode(User $user, TreeNode $treeNode, $offset = null, $count = null)
    {
        return $this->notificationService->getNotificationsForUserAndPublication(
            $user, $treeNode, $this->contentObjectPublication, $offset, $count
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry|\Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNotificationForNewEntry(User $user, TreeNode $treeNode, Entry $entry)
    {
        $this->notificationService->createNotificationForNewEntry($user, $treeNode, $this->contentObjectPublication, $entry);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry|\Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Feedback|\Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback $feedback
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNotificationForNewFeedback(User $user, TreeNode $treeNode, Entry $entry, Feedback $feedback)
    {
        $this->notificationService->createNotificationForNewFeedback(
            $user, $treeNode, $this->contentObjectPublication, $entry, $feedback
        );
    }
}