<?php
namespace Chamilo\Libraries\Calendar\Event\Interfaces;

/**
 *
 * @package libraries\calendar\event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface ActionSupport
{

    /**
     * Get the actions available in the renderer for the given event
     * 
     * @param Event $event
     * @return ToolbarItem[]
     */
    public function get_calendar_event_actions($event);
}