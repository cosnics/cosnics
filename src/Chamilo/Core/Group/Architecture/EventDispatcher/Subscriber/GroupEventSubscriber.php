<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber;

use Chamilo\Core\Group\Architecture\EventDispatcher\Event\BeforeGroupDeleteEvent;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class GroupEventSubscriber implements EventSubscriberInterface
{
    public function __construct(protected GroupMembershipService $groupMembershipService)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function beforeDelete(BeforeGroupDeleteEvent $beforeGroupDeleteEvent): void
    {
        $this->groupMembershipService->deleteGroupMembershipsByGroup(
            $beforeGroupDeleteEvent->group, $beforeGroupDeleteEvent->executingUser
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeGroupDeleteEvent::class => 'beforeDelete'
        ];
    }
}