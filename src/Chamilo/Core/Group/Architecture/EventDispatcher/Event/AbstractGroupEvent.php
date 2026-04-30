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
    public function __construct(public Group $group, public ?User $executingUser = null)
    {
    }
}