<?php
namespace Chamilo\Core\Group\Service\Tracking;

use Chamilo\Core\Group\Service\GroupEventListenerInterface;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupActivity;
use Chamilo\Core\Group\Storage\Repository\GroupTrackingRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Group\Service\Tracking
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupActivityService implements GroupEventListenerInterface
{

    protected ?User $currentUser;

    protected GroupTrackingRepository $groupTrackingRepository;

    public function __construct(GroupTrackingRepository $groupTrackingRepository, ?User $currentUser)
    {
        $this->groupTrackingRepository = $groupTrackingRepository;
        $this->currentUser = $currentUser;
    }

    public function afterCreate(Group $group)
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_CREATED, $group->getId()
            )
        );
    }

    public function afterDelete(Group $group, array $subGroupIds = [], array $impactedUserIds = [])
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_DELETED, $group->getId()
            )
        );
    }

    public function afterEmptyGroup(Group $group, array $impactedUserIds = [])
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_TRUNCATED, $group->getId()
            )
        );
    }

    public function afterMove(Group $group, Group $oldParentGroup, Group $newParentGroup)
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_MOVED, $group->getId()
            )
        );
    }

    public function afterSubscribe(Group $group, User $user)
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_SUBSCRIBED, $group->getId(), $user->getId()
            )
        );
    }

    public function afterUnsubscribe(Group $group, User $user)
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_UNSUBSCRIBED, $group->getId(), $user->getId()
            )
        );
    }

    public function getCurrentUser(): ?User
    {
        return $this->currentUser;
    }

    public function getGroupTrackingRepository(): GroupTrackingRepository
    {
        return $this->groupTrackingRepository;
    }

    protected function initializeGroupActivityFromParameters(
        int $action, string $groupIdentifier, ?string $targetUserIdentifier = null
    ): GroupActivity
    {
        $currentUser = $this->getCurrentUser();
        $groupActivity = new GroupActivity();

        $groupActivity->setAction($action);
        $groupActivity->setDate(time());
        $groupActivity->setGroupIdentifier($groupIdentifier);
        $groupActivity->setUserIdentifier($currentUser instanceof User ? $currentUser->getId() : null);
        $groupActivity->setTargetUserIdentifier($targetUserIdentifier);

        return $groupActivity;
    }
}