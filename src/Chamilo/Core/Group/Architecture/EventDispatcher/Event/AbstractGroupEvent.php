<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Event;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractGroupEvent extends Event
{
    protected ?User $executingUser;

    protected Group $group;

    public function __construct(Group $group, ?User $executingUser = null)
    {
        $this->group = $group;
        $this->executingUser = $executingUser;
    }

    public function getExecutingUser(): ?User
    {
        return $this->executingUser;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }
}