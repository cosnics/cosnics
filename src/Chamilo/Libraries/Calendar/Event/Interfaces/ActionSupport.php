<?php
namespace Chamilo\Libraries\Calendar\Event\Interfaces;

use Chamilo\Libraries\Calendar\Event\Event;

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
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getEventActions(Event $event): array;
}