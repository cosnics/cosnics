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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function beforeDelete(BeforeUserDeleteEvent $beforeUserDeleteEvent): void
    {
        $this->groupMembershipService->deleteGroupMembershipsByUser($beforeUserDeleteEvent->user);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeUserDeleteEvent::class => 'beforeDelete'
        ];
    }
}