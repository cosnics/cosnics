<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Event;

use Chamilo\Core\Tracking\Storage\DataClass\Event;

/**
 *
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Activity extends Event
{

    /**
     *
     * @return string[]
     */
    public function getTrackerClasses()
    {
        return array(
            \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity::class
        );
    }
}