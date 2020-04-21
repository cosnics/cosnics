<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Integration\Chamilo\Application\Calendar\Repository;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Repository to retrieve calendar events for the assignment tool based on the due date of assignments
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CalendarEventDataProviderRepository extends \Chamilo\Application\Weblcms\Integration\Chamilo\Application\Calendar\Repository\CalendarEventDataProviderRepository
{

    /**
     *
     * @return string
     */
    protected function getToolName()
    {
        return 'Calendar';
    }

    /**
     *
     * @return string
     */
    protected function getContentObjectClassName()
    {
        return CalendarEvent::class_name();
    }

    /**
     *
     * @param int $fromDate
     * @param int $toDate
     *
     * @return Condition
     */
    protected function getSpecificContentObjectConditions($fromDate, $toDate)
    {
        return DataManager::getCalendarEventConditionsBetweenFromAndToDate(
            $fromDate, 
            $toDate);
    }
}