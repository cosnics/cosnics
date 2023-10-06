<?php
namespace Chamilo\Core\Group\EventDispatcher\Event;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Group\EventDispatcher\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractGroupMembershipEvent extends AbstractGroupEvent
{
    protected User $user;

    public function __construct(Group $group, User $user)
    {
        parent::__construct($group);

        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}