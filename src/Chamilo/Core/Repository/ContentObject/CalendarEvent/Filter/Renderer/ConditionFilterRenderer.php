<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Render the parameters set via FilterData as conditions
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConditionFilterRenderer extends \Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer
{

    /*
     * (non-PHPdoc) @see \core\repository\FilterRenderer::render()
     */
    public function render()
    {
        $filter_data = $this->get_filter_data();
        $general_condition = parent::render();
        
        $conditions = [];
        
        if ($general_condition instanceof Condition)
        {
            $conditions[] = $general_condition;
        }
        
        // Start date
        if ($filter_data->has_date(FilterData::FILTER_START_DATE))
        {
            $creation_date_conditions = [];
            $creation_date_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class, CalendarEvent::PROPERTY_START_DATE),
                ComparisonCondition::GREATER_THAN_OR_EQUAL,
                new StaticConditionVariable(strtotime($filter_data->get_start_date(FilterData::FILTER_FROM_DATE))));
            $creation_date_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class, CalendarEvent::PROPERTY_START_DATE),
                ComparisonCondition::LESS_THAN_OR_EQUAL,
                new StaticConditionVariable(strtotime($filter_data->get_start_date(FilterData::FILTER_TO_DATE))));
            $conditions[] = new AndCondition($creation_date_conditions);
        }
        else
        {
            if ($filter_data->get_start_date(FilterData::FILTER_FROM_DATE))
            {
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(CalendarEvent::class, CalendarEvent::PROPERTY_START_DATE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable(strtotime($filter_data->get_start_date(FilterData::FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_start_date(FilterData::FILTER_TO_DATE))
            {
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(CalendarEvent::class, CalendarEvent::PROPERTY_START_DATE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable(strtotime($filter_data->get_start_date(FilterData::FILTER_TO_DATE))));
            }
        }
        
        // End date
        if ($filter_data->has_date(FilterData::FILTER_END_DATE))
        {
            $modification_date_conditions = [];
            $modification_date_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class, CalendarEvent::PROPERTY_END_DATE),
                ComparisonCondition::GREATER_THAN_OR_EQUAL,
                new StaticConditionVariable(strtotime($filter_data->get_end_date(FilterData::FILTER_FROM_DATE))));
            $modification_date_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CalendarEvent::class, CalendarEvent::PROPERTY_END_DATE),
                ComparisonCondition::LESS_THAN_OR_EQUAL,
                new StaticConditionVariable(strtotime($filter_data->get_end_date(FilterData::FILTER_TO_DATE))));
            $conditions[] = new AndCondition($modification_date_conditions);
        }
        else
        {
            if ($filter_data->get_end_date(FilterData::FILTER_FROM_DATE))
            {
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(CalendarEvent::class, CalendarEvent::PROPERTY_END_DATE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable(strtotime($filter_data->get_end_date(FilterData::FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_end_date(FilterData::FILTER_TO_DATE))
            {
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(CalendarEvent::class, CalendarEvent::PROPERTY_END_DATE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable(strtotime($filter_data->get_end_date(FilterData::FILTER_TO_DATE))));
            }
        }
        
        if ($filter_data->has_filter_property(FilterData::FILTER_FREQUENCY))
        {
            $frequency_type = $filter_data->get_filter_property(FilterData::FILTER_FREQUENCY);
            
            if ($frequency_type == - 1)
            {
                $frequency_type = 0;
            }
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CalendarEvent::class, CalendarEvent::PROPERTY_FREQUENCY),
                new StaticConditionVariable($frequency_type));
        }
        
        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }
}