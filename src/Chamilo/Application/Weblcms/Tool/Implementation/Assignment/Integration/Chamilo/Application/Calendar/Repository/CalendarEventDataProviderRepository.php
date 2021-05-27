<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Integration\Chamilo\Application\Calendar\Repository;

use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

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
        return 'Assignment';
    }

    /**
     *
     * @return string
     */
    protected function getContentObjectClassName()
    {
        return Assignment::class;
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
        $conditions = [];
        
        if (! empty($fromDate))
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Assignment::class, Assignment::PROPERTY_END_TIME),
                ComparisonCondition::GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable($fromDate));
        }
        
        if (! empty($toDate))
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Assignment::class, Assignment::PROPERTY_END_TIME),
                ComparisonCondition::LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable($toDate));
        }
        
        return new AndCondition($conditions);
    }
}