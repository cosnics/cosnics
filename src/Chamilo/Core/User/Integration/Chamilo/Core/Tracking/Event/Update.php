<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Event;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Update extends Event
{

    /**
     *
     * @see \Chamilo\Core\Tracking\Storage\DataClass\Event::getTrackerClasses()
     */
    public function getTrackerClasses()
    {
        return array(Change::class);
    }
}