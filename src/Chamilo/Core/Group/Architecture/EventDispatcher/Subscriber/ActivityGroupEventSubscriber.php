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
use Chamilo\Core\Group\Storage\Entity\GroupActivity;
use Chamilo\Core\Group\Storage\Repository\GroupActivityRepository;
use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class ActivityGroupEventSubscriber implements EventSubscriberInterface
{
    public function __construct(protected GroupActivityRepository $groupActivityRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterCreate(AfterGroupCreateEvent $afterGroupCreateEvent): void
    {
        $this->groupActivityRepository->saveGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::CREATED, $afterGroupCreateEvent->group->getIdentifier(),
                $afterGroupCreateEvent->executingUser
            ), $afterGroupCreateEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterDelete(AfterGroupDeleteEvent $afterGroupDeleteEvent): void
    {
        $this->groupActivityRepository->saveGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::DELETED, $afterGroupDeleteEvent->group->getIdentifier(),
                $afterGroupDeleteEvent->executingUser
            ), $afterGroupDeleteEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterEmpty(AfterGroupEmptyEvent $afterGroupEmptyEvent): void
    {
        $this->groupActivityRepository->saveGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::TRUNCATED, $afterGroupEmptyEvent->group->getIdentifier(),
                $afterGroupEmptyEvent->executingUser
            ), $afterGroupEmptyEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterMove(AfterGroupMoveEvent $afterGroupMoveEvent): void
    {
        $this->groupActivityRepository->saveGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::MOVED, $afterGroupMoveEvent->group->getIdentifier(),
                $afterGroupMoveEvent->executingUser
            ), $afterGroupMoveEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterSubscribe(AfterGroupSubscribeEvent $afterGroupSubscribeEvent): void
    {
        $this->groupActivityRepository->saveGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::SUBSCRIBED, $afterGroupSubscribeEvent->groupIdentifier,
                $afterGroupSubscribeEvent->executingUser, $afterGroupSubscribeEvent->userIdentifier
            ), $afterGroupSubscribeEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterUnsubscribe(AfterGroupUnsubscribeEvent $afterGroupUnsubscribeEvent): void
    {
        $this->groupActivityRepository->saveGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::UNSUBSCRIBED, $afterGroupUnsubscribeEvent->groupIdentifier,
                $afterGroupUnsubscribeEvent->executingUser, $afterGroupUnsubscribeEvent->userIdentifier
            ), $afterGroupUnsubscribeEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterUpdate(AfterGroupUpdateEvent $afterGroupUpdateEvent): void
    {
        $this->groupActivityRepository->saveGroupActivity(
            $this->initializeGroupActivityFromParameters(
                GroupActivityTypeEnum::UPDATED, $afterGroupUpdateEvent->group->getIdentifier(),
                $afterGroupUpdateEvent->executingUser
            ), $afterGroupUpdateEvent->flush
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
        GroupActivityTypeEnum $action, Uuid $groupIdentifier, ?User $executingUser = null,
        ?Uuid $targetUserIdentifier = null
    ): GroupActivity
    {
        $groupActivity = new GroupActivity();

        $groupActivity->setIdentifier(new UuidV7());
        $groupActivity->setAction($action);
        $groupActivity->setDate(time());
        $groupActivity->setGroupIdentifier($groupIdentifier);
        $groupActivity->setUserIdentifier(
            $executingUser instanceof User ? $executingUser->getIdentifier() : null
        );
        $groupActivity->setTargetUserIdentifier($targetUserIdentifier);

        return $groupActivity;
    }
}