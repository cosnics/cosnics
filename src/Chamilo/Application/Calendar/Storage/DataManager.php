<?php
namespace Chamilo\Application\Calendar\Storage;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Calendar\Service\View\CalendarRenderer;

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
     * @param CalendarRenderer $renderer
     * @param int $from_date
     * @param int $to_date
     *
     * @return Event[]
     */
    public static function getEvents(CalendarRenderer $renderer, $from_date, $to_date)
    {
        $events = [];
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(Manager::package());
        
        foreach ($registrations as $registration)
        {
            $context = $registration[Registration::PROPERTY_CONTEXT];
            $class_name = $context . '\Manager';
            
            if (class_exists($class_name))
            {
                $implementor = new $class_name();
                $events = array_merge($events, $implementor->getEvents($renderer, $from_date, $to_date));
            }
        }
        
        return $events;
    }
}
