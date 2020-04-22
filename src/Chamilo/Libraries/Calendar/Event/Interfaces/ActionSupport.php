<?php
namespace Chamilo\Libraries\Calendar\Event\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface ActionSupport
{

    /**
     * Get the actions available in the renderer for the given event
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getEventActions($event);
}