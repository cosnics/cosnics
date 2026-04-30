<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber;

use Chamilo\Core\Group\Architecture\EventDispatcher\Event\BeforeGroupDeleteEvent;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class GroupMembershipEventSubscriber implements EventSubscriberInterface
{
    public function __construct(protected GroupMembershipService $groupMembershipService)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function beforeDelete(BeforeGroupDeleteEvent $afterGroupCreateEvent): bool
    {
        return $this->groupMembershipService->deleteGroupMembershipsByGroup(
            $afterGroupCreateEvent->group, $afterGroupCreateEvent->executingUser
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeGroupDeleteEvent::class => 'beforeDelete'
        ];
    }
}