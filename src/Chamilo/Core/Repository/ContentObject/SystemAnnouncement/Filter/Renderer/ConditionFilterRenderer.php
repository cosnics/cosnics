<?php
namespace Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
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
        
        if ($filter_data->has_filter_property(FilterData :: FILTER_ICON))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(SystemAnnouncement :: class_name(), SystemAnnouncement :: PROPERTY_ICON), 
                new StaticConditionVariable($filter_data->get_filter_property(FilterData :: FILTER_ICON)));
        }
        
        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }
}