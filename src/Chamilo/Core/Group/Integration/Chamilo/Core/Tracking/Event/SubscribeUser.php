<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Event;

use Chamilo\Core\Tracking\Storage\DataClass\Event;

/**
 *
 * @package Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubscribeUser extends Event
{

    /**
     *
     * @return multitype:string
     */
    public function getTrackerClasses()
    {
        return array(\Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::class_name());
    }
}