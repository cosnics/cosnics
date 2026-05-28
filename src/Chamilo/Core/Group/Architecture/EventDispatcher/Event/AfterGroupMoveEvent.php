<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Event;

use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AfterGroupMoveEvent extends AbstractGroupEvent
{
    public function __construct(
        Group $group, public Uuid $oldParentGroupIdentifier, public Uuid $newParentGroupIdentifier,
        ?User $executingUser = null, bool $flush = true
    )
    {
        parent::__construct($group, $executingUser, $flush);
    }
}