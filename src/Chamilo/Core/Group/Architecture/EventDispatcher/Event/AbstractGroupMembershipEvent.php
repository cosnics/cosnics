<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Event;

use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractGroupMembershipEvent extends Event
{
    public function __construct(
        public string $groupIdentifier, public string $userIdentifier, public ?User $executingUser = null
    )
    {
    }
}