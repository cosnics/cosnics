<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\Task\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
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
        $general_condition = parent :: render();
        
        $conditions = array();
        
        if ($general_condition instanceof Condition)
        {
            $conditions[] = $general_condition;
        }
        
        // Start date
        if ($filter_data->has_date(FilterData :: FILTER_START_DATE))
        {
            $creation_date_conditions = array();
            $creation_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_START_DATE), 
                InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_start_date(FilterData :: FILTER_FROM_DATE))));
            $creation_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_START_DATE), 
                InequalityCondition :: LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_start_date(FilterData :: FILTER_TO_DATE))));
            $conditions[] = new AndCondition($creation_date_conditions);
        }
        else
        {
            if ($filter_data->get_start_date(FilterData :: FILTER_FROM_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_START_DATE), 
                    InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                    new StaticConditionVariable(strtotime($filter_data->get_start_date(FilterData :: FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_start_date(FilterData :: FILTER_TO_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_START_DATE), 
                    InequalityCondition :: LESS_THAN_OR_EQUAL, 
                    new StaticConditionVariable(strtotime($filter_data->get_start_date(FilterData :: FILTER_TO_DATE))));
            }
        }
        
        // End date
        if ($filter_data->has_date(FilterData :: FILTER_DUE_DATE))
        {
            $modification_date_conditions = array();
            $modification_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_DUE_DATE), 
                InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_due_date(FilterData :: FILTER_FROM_DATE))));
            $modification_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_DUE_DATE), 
                InequalityCondition :: LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_due_date(FilterData :: FILTER_TO_DATE))));
            $conditions[] = new AndCondition($modification_date_conditions);
        }
        else
        {
            if ($filter_data->get_due_date(FilterData :: FILTER_FROM_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_DUE_DATE), 
                    InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                    new StaticConditionVariable(strtotime($filter_data->get_due_date(FilterData :: FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_due_date(FilterData :: FILTER_TO_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_DUE_DATE), 
                    InequalityCondition :: LESS_THAN_OR_EQUAL, 
                    new StaticConditionVariable(strtotime($filter_data->get_due_date(FilterData :: FILTER_TO_DATE))));
            }
        }
        
        if ($filter_data->has_filter_property(FilterData :: FILTER_FREQUENCY))
        {
            $frequency_type = $filter_data->get_filter_property(FilterData :: FILTER_FREQUENCY);
            
            if ($frequency_type == - 1)
            {
                $frequency_type = 0;
            }
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_FREQUENCY), 
                new StaticConditionVariable($frequency_type));
        }
        
        if ($filter_data->has_filter_property(FilterData :: FILTER_CATEGORY))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_CATEGORY), 
                new StaticConditionVariable($filter_data->get_filter_property(FilterData :: FILTER_CATEGORY)));
        }
        
        if ($filter_data->has_filter_property(FilterData :: FILTER_PRIORITY))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Task :: class_name(), Task :: PROPERTY_PRIORITY), 
                new StaticConditionVariable($filter_data->get_filter_property(FilterData :: FILTER_PRIORITY)));
        }
        
        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }
}