<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Storage;

use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
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
    public static function getTaskConditionsBetweenFromAndToDate($fromDate, $toDate)
    {
        $conditions = [];
        
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
        $nonRepeatableConditions = [];
        
        $nonRepeatableConditions[] = new EqualityCondition(
            new PropertyConditionVariable(Task::class, Task::PROPERTY_FREQUENCY), 
            new StaticConditionVariable(Task::FREQUENCY_NONE));
        
        $startConditions = [];
        
        if (! empty($fromDate))
        {
            $startConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Task::class, Task::PROPERTY_START_DATE), 
                ComparisonCondition::GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable($fromDate));
        }
        
        if (! empty($toDate))
        {
            $startConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Task::class, Task::PROPERTY_START_DATE), 
                ComparisonCondition::LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable($toDate));
        }
        
        $startCondition = new AndCondition($startConditions);
        
        $endConditions = [];
        
        if (! empty($fromDate))
        {
            $endConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Task::class, Task::PROPERTY_DUE_DATE), 
                ComparisonCondition::GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable($fromDate));
        }
        
        if (! empty($toDate))
        {
            $endConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Task::class, Task::PROPERTY_DUE_DATE), 
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
        $repeatableConditions = [];
        
        $repeatableConditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Task::class, Task::PROPERTY_FREQUENCY), 
                new StaticConditionVariable(Task::FREQUENCY_NONE)));
        
        if (! empty($toDate))
        {
            $repeatableConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Task::class, Task::PROPERTY_START_DATE), 
                ComparisonCondition::LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable($toDate));
        }
        
        $untilConditions = [];
        
        $untilConditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Task::class, Task::PROPERTY_UNTIL), 
                new StaticConditionVariable(0)));
        
        if (! empty($fromDate))
        {
            $untilConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Task::class, Task::PROPERTY_UNTIL), 
                ComparisonCondition::GREATER_THAN, 
                new StaticConditionVariable($fromDate));
        }
        
        $untilCondition = new AndCondition($untilConditions);
        
        $foreverCondition = new EqualityCondition(
            new PropertyConditionVariable(Task::class, Task::PROPERTY_UNTIL), 
            new StaticConditionVariable(0));
        
        $repeatableConditions[] = new OrCondition(array($untilCondition, $foreverCondition));
        
        return new AndCondition($repeatableConditions);
    }
}
