<?php
namespace Chamilo\Core\Group\EventDispatcher\Event;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package Chamilo\Core\Group\EventDispatcher\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractGroupEvent extends Event
{
    protected Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

}