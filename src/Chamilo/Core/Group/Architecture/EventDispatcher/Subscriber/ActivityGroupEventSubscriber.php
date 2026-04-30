<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber;

use Chamilo\Core\Group\Architecture\Enum\GroupActivityTypeEnum;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupCreateEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupDeleteEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupEmptyEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupMoveEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupSubscribeEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupUnsubscribeEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupUpdateEvent;
use Chamilo\Core\Group\Storage\DataClass\GroupActivity;
use Chamilo\Core\Group\Storage\Repository\GroupTrackingRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class ActivityGroupEventSubscriber implements EventSubscriberInterface
{
    public function __construct(protected GroupTrackingRepository $groupTrackingRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterCreate(AfterGroupCreateEvent $afterGroupCreateEvent): bool
    {
        return $this->groupTrackingRepository->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::CREATED, $afterGroupCreateEvent->group->getId(),
                $afterGroupCreateEvent->executingUser
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterDelete(AfterGroupDeleteEvent $afterGroupDeleteEvent): bool
    {
        return $this->groupTrackingRepository->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::DELETED, $afterGroupDeleteEvent->group->getId(),
                $afterGroupDeleteEvent->executingUser
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterEmptyGroup(AfterGroupEmptyEvent $afterGroupEmptyEvent): bool
    {
        return $this->groupTrackingRepository->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::TRUNCATED, $afterGroupEmptyEvent->group->getId(),
                $afterGroupEmptyEvent->executingUser
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterMove(AfterGroupMoveEvent $afterGroupMoveEvent): bool
    {
        return $this->groupTrackingRepository->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::MOVED, $afterGroupMoveEvent->group->getId(), $afterGroupMoveEvent->executingUser
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterSubscribe(AfterGroupSubscribeEvent $afterGroupSubscribeEvent): bool
    {
        return $this->groupTrackingRepository->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::SUBSCRIBED, $afterGroupSubscribeEvent->groupIdentifier,
                $afterGroupSubscribeEvent->executingUser, $afterGroupSubscribeEvent->userIdentifier
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUnsubscribe(AfterGroupUnsubscribeEvent $afterGroupUnsubscribeEvent): bool
    {
        return $this->groupTrackingRepository->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::UNSUBSCRIBED, $afterGroupUnsubscribeEvent->groupIdentifier,
                $afterGroupUnsubscribeEvent->executingUser, $afterGroupUnsubscribeEvent->userIdentifier
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUpdate(AfterGroupUpdateEvent $afterGroupUpdateEvent): bool
    {
        return $this->groupTrackingRepository->createGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::UPDATED, $afterGroupUpdateEvent->group->getId(),
                $afterGroupUpdateEvent->executingUser
            )
        );
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
        GroupActivityTypeEnum $action, string $groupIdentifier, ?User $executingUser = null,
        ?string $targetUserIdentifier = null
    ): GroupActivity
    {
        $groupActivity = new GroupActivity();

        $groupActivity->setAction($action);
        $groupActivity->setDate(time());
        $groupActivity->setGroupIdentifier($groupIdentifier);
        $groupActivity->setUserIdentifier($executingUser instanceof User ? $executingUser->getId() : null);
        $groupActivity->setTargetUserIdentifier($targetUserIdentifier);

        return $groupActivity;
    }
}