<?php
namespace Chamilo\Core\Group\EventDispatcher\Subscriber;

use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupCreateEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupDeleteEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupEmptyEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupMoveEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupSubscribeEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupUnsubscribeEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupUpdateEvent;
use Chamilo\Core\Group\Storage\DataClass\GroupActivity;
use Chamilo\Core\Group\Storage\Repository\GroupTrackingRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Core\Group\EventDispatcher\Subscriber
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityGroupEventSubscriber implements EventSubscriberInterface
{

    protected ?User $currentUser;

    protected GroupTrackingRepository $groupTrackingRepository;

    public function __construct(GroupTrackingRepository $groupTrackingRepository, ?User $currentUser)
    {
        $this->groupTrackingRepository = $groupTrackingRepository;
        $this->currentUser = $currentUser;
    }

    public function afterCreate(AfterGroupCreateEvent $afterGroupCreateEvent): bool
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_CREATED, $afterGroupCreateEvent->getGroup()->getId()
            )
        );
    }

    public function afterDelete(AfterGroupDeleteEvent $afterGroupDeleteEvent): bool
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_DELETED, $afterGroupDeleteEvent->getGroup()->getId()
            )
        );
    }

    public function afterEmptyGroup(AfterGroupEmptyEvent $afterGroupEmptyEvent): bool
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_TRUNCATED, $afterGroupEmptyEvent->getGroup()->getId()
            )
        );
    }

    public function afterMove(AfterGroupMoveEvent $afterGroupMoveEvent): bool
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_MOVED, $afterGroupMoveEvent->getGroup()->getId()
            )
        );
    }

    public function afterSubscribe(AfterGroupSubscribeEvent $afterGroupSubscribeEvent): bool
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_SUBSCRIBED, $afterGroupSubscribeEvent->getGroup()->getId(),
                $afterGroupSubscribeEvent->getUser()->getId()
            )
        );
    }

    public function afterUnsubscribe(AfterGroupUnsubscribeEvent $afterGroupUnsubscribeEvent): bool
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_UNSUBSCRIBED, $afterGroupUnsubscribeEvent->getGroup()->getId(),
                $afterGroupUnsubscribeEvent->getUser()->getId()
            )
        );
    }

    public function afterUpdate(AfterGroupUpdateEvent $afterGroupUpdateEvent): bool
    {
        return $this->getGroupTrackingRepository()->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivity::ACTIVITY_UPDATED, $afterGroupUpdateEvent->getGroup()->getId()
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

    public static function getSubscribedEvents(): array
    {
        return [
            AfterGroupCreateEvent::class => 'afterCreate',
            AfterGroupDeleteEvent::class => 'afterDelete',
            AfterGroupEmptyEvent::class => 'afterEmpty',
            AfterGroupMoveEvent::class => 'afterMove',
            AfterGroupSubscribeEvent::class => 'afterSubscribe',
            AfterGroupUnsubscribeEvent::class => 'afterUnsubscribe',
            AfterGroupUpdateEvent::class => 'afterUpdate'
        ];
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