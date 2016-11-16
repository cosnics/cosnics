<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    /**
     * Gets conditions to retrieve calendar events between a from and a to date
     * 
     * @param int $fromDate
     * @param int $toDate
     *
     * @return Condition
     */
    public static function getCalendarEventConditionsBetweenFromAndToDate($fromDate, $toDate)
    {
        $conditions = array();
        
        $conditions[] = self::getNonRepeatableCondition($fromDate, $toDate);
        $conditions[] = self::getRepeatableCondition($fromDate, $toDate);
        
        return new OrCondition($conditions);
    }

    /**
     * Returns the condition for the calendar events that do not repeat
     * 
     * @param int $fromDate
     * @param int $toDate
     *
     * @return AndCondition
     */
    protected static function getNonRepeatableCondition($fromDate, $toDate)
    {
        $nonRepeatableConditions = array();
        
        $nonRepeatableConditions[] = new EqualityCondition(
            new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_FREQUENCY), 
            new StaticConditionVariable(CalendarEvent::FREQUENCY_NONE));
        
        $startConditions = array();
        
        if (! empty($fromDate))
        {
            $startConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_START_DATE), 
                ComparisonCondition::GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable($fromDate));
        }
        
        if (! empty($toDate))
        {
            $startConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_START_DATE), 
                ComparisonCondition::LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable($toDate));
        }
        
        $startCondition = new AndCondition($startConditions);
        
        $endConditions = array();
        
        if (! empty($fromDate))
        {
            $endConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_END_DATE), 
                ComparisonCondition::GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable($fromDate));
        }
        
        if (! empty($toDate))
        {
            $endConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_END_DATE), 
                ComparisonCondition::LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable($toDate));
        }
        
        $endCondition = new AndCondition($endConditions);
        
        $nonRepeatableConditions[] = new OrCondition(array($startCondition, $endCondition));
        
        return new AndCondition($nonRepeatableConditions);
    }

    /**
     * Returns the condition for the calendar events that do repeat
     * 
     * @param int $fromDate
     * @param int $toDate
     *
     * @return AndCondition
     */
    protected static function getRepeatableCondition($fromDate, $toDate)
    {
        $repeatableConditions = array();
        
        $repeatableConditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_FREQUENCY), 
                new StaticConditionVariable(CalendarEvent::FREQUENCY_NONE)));
        
        if (! empty($fromDate))
        {
            $repeatableConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_START_DATE), 
                ComparisonCondition::LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable($toDate));
        }
        
        $untilConditions = array();
        
        $untilConditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_UNTIL), 
                new StaticConditionVariable(0)));
        
        if (! empty($fromDate))
        {
            $untilConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_UNTIL), 
                ComparisonCondition::GREATER_THAN, 
                new StaticConditionVariable($fromDate));
        }
        
        $untilCondition = new AndCondition($untilConditions);
        
        $foreverCondition = new EqualityCondition(
            new PropertyConditionVariable(CalendarEvent::class_name(), CalendarEvent::PROPERTY_UNTIL), 
            new StaticConditionVariable(0));
        
        $repeatableConditions[] = new OrCondition(array($untilCondition, $foreverCondition));
        
        return new AndCondition($repeatableConditions);
    }
}
