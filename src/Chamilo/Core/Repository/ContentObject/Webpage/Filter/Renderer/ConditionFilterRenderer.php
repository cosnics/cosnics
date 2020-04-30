<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\Webpage\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
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
        $general_condition = parent::render();
        
        $conditions = array();
        
        if ($general_condition instanceof Condition)
        {
            $conditions[] = $general_condition;
        }
        
        if ($filter_data->has_filter_property(FilterData::FILTER_FILESIZE))
        {
            $format = $filter_data->get_filter_property(FilterData::FILTER_FORMAT);
            $filesize = $filter_data->get_filter_property(FilterData::FILTER_FILESIZE);
            $filesize_bytes = $filesize * pow(1024, $format);
            $compare = $filter_data->get_filter_property(FilterData::FILTER_COMPARE);
            
            if ($compare == ComparisonCondition::EQUAL)
            {
                $equality_conditions = array();
                $equality_conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Webpage::class, Webpage::PROPERTY_FILESIZE), 
                    InequalityCondition::GREATER_THAN_OR_EQUAL, 
                    new StaticConditionVariable($filesize_bytes * 0.9));
                $equality_conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Webpage::class, Webpage::PROPERTY_FILESIZE), 
                    InequalityCondition::LESS_THAN_OR_EQUAL, 
                    new StaticConditionVariable($filesize_bytes * 1.1));
                $conditions[] = new AndCondition($equality_conditions);
            }
            else
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Webpage::class, Webpage::PROPERTY_FILESIZE), 
                    $compare, 
                    new StaticConditionVariable($filesize_bytes));
            }
        }
        
        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }
}