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
    protected Group $newParentGroup;

    protected Group $oldParentGroup;

    public function __construct(Group $group, Group $oldParentGroup, Group $newParentGroup, ?User $executingUser = null)
    {
        parent::__construct($group, $executingUser);

        $this->oldParentGroup = $oldParentGroup;
        $this->newParentGroup = $newParentGroup;
    }

    public function getNewParentGroup(): Group
    {
        return $this->newParentGroup;
    }

    public function getOldParentGroup(): Group
    {
        return $this->oldParentGroup;
    }

}