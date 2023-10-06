<?php
namespace Chamilo\Core\Group\EventDispatcher\Event;

use Chamilo\Core\Group\Storage\DataClass\Group;

/**
 * @package Chamilo\Core\Group\EventDispatcher\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AfterGroupMoveEvent extends AbstractGroupEvent
{
    protected Group $newParentGroup;

    protected Group $oldParentGroup;

    public function __construct(Group $group, Group $oldParentGroup, Group $newParentGroup)
    {
        parent::__construct($group);

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