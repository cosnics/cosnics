<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Event;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AfterGroupMoveEvent extends AbstractGroupEvent
{
    public function __construct(
        Group $group, public string $oldParentGroupIdentifier, public string $newParentGroupIdentifier,
        ?User $executingUser = null
    )
    {
        parent::__construct($group, $executingUser);
    }
}