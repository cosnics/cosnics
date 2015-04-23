<?php
namespace Chamilo\Application\Calendar\Storage;

use Chamilo\Application\Calendar\Manager;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'calendar_';

    /**
     *
     * @param Renderer $renderer
     * @param int $from_date
     * @param int $to_date
     * @return Event[]
     */
    public static function get_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $from_date, $to_date)
    {
        $events = array();
        $registrations = \Chamilo\Configuration\Storage\DataManager :: get_integrating_contexts(Manager :: context());

        foreach ($registrations as $registration)
        {
            $context = $registration->get_context();
            $class_name = $context . '\Manager';

            if (class_exists($class_name))
            {
                $implementor = new $class_name();
                $events = array_merge($events, $implementor->get_events($renderer, $from_date, $to_date));
            }
        }

        return $events;
    }
}
