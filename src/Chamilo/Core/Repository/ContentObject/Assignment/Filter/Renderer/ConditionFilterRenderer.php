<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\Assignment\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
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
        if ($filter_data->has_date(FilterData :: FILTER_START_TIME))
        {
            $creation_date_conditions = array();
            $creation_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(Assignment :: class_name(), Assignment :: PROPERTY_START_TIME), 
                InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_start_time(FilterData :: FILTER_FROM_DATE))));
            $creation_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(Assignment :: class_name(), Assignment :: PROPERTY_START_TIME), 
                InequalityCondition :: LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_start_time(FilterData :: FILTER_TO_DATE))));
            $conditions[] = new AndCondition($creation_date_conditions);
        }
        else
        {
            if ($filter_data->get_start_time(FilterData :: FILTER_FROM_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Assignment :: class_name(), Assignment :: PROPERTY_START_TIME), 
                    InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                    new StaticConditionVariable(strtotime($filter_data->get_start_time(FilterData :: FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_start_time(FilterData :: FILTER_TO_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Assignment :: class_name(), Assignment :: PROPERTY_START_TIME), 
                    InequalityCondition :: LESS_THAN_OR_EQUAL, 
                    new StaticConditionVariable(strtotime($filter_data->get_start_time(FilterData :: FILTER_TO_DATE))));
            }
        }
        
        // End date
        if ($filter_data->has_date(FilterData :: FILTER_END_TIME))
        {
            $modification_date_conditions = array();
            $modification_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(Assignment :: class_name(), Assignment :: PROPERTY_END_TIME), 
                InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_end_time(FilterData :: FILTER_FROM_DATE))));
            $modification_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(Assignment :: class_name(), Assignment :: PROPERTY_END_TIME), 
                InequalityCondition :: LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_end_time(FilterData :: FILTER_TO_DATE))));
            $conditions[] = new AndCondition($modification_date_conditions);
        }
        else
        {
            if ($filter_data->get_end_time(FilterData :: FILTER_FROM_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Assignment :: class_name(), Assignment :: PROPERTY_END_TIME), 
                    InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                    new StaticConditionVariable(strtotime($filter_data->get_end_time(FilterData :: FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_end_time(FilterData :: FILTER_TO_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Assignment :: class_name(), Assignment :: PROPERTY_END_TIME), 
                    InequalityCondition :: LESS_THAN_OR_EQUAL, 
                    new StaticConditionVariable(strtotime($filter_data->get_end_time(FilterData :: FILTER_TO_DATE))));
            }
        }
        
        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }
}