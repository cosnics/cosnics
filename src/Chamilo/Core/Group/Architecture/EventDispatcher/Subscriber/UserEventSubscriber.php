<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber;

use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserDeleteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class UserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(protected GroupMembershipService $groupMembershipService)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function beforeDelete(BeforeUserDeleteEvent $beforeUserDeleteEvent): bool
    {
        $this->groupMembershipService->deleteGroupMembershipsByUser($beforeUserDeleteEvent->getUser());

        return true;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeUserDeleteEvent::class => 'beforeDelete'
        ];
    }
}