<?php
namespace Chamilo\Libraries\Calendar\Architecture\Interface;

use Chamilo\Libraries\Calendar\Architecture\Domain\Event;

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
     * @return \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button[]
     */
    public function getEventActions(Event $event): array;
}