<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Event;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\AdminUserVisit;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Enter extends Event
{

    /**
     *
     * @see \Chamilo\Core\Tracking\Storage\DataClass\Event::getTrackerClasses()
     */
    public function getTrackerClasses()
    {
        return array(Visit::class_name(), AdminUserVisit::class);
    }

    public function getType()
    {
        return Visit::TYPE_ENTER;
    }

    public function run($parameters)
    {
        $parameters['event'] = $this->get_name();
        $data = array();

        $trackers = $this->get_trackers();
        foreach ($trackers as $tracker)
        {
            $tracker->set_event($this);
            $tracker->run($parameters);

            if($tracker instanceof Visit)
            {
                $parameters['user_visit_id'] = $tracker->getId();
            }

            $data[] = $tracker;
        }

        return $data;
    }
}