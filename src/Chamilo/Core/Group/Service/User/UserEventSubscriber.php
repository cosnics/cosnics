<?php
namespace Chamilo\Core\Group\Service\User;

use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\User\EventDispatcher\Event\BeforeUserDeleteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Core\Group\Service\User
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserEventSubscriber implements EventSubscriberInterface
{

    protected GroupMembershipService $groupMembershipService;

    public function __construct(GroupMembershipService $groupMembershipService)
    {
        $this->groupMembershipService = $groupMembershipService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function beforeDelete(BeforeUserDeleteEvent $beforeUserDeleteEvent): bool
    {
        return $this->getGroupMembershipService()->unsubscribeUserFromAllGroups($beforeUserDeleteEvent->getUser());
    }

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->groupMembershipService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeUserDeleteEvent::class => 'beforeDelete'
        ];
    }
}