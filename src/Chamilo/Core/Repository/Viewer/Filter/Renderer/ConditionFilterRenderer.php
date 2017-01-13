<?php
namespace Chamilo\Core\Repository\Viewer\Filter\Renderer;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\Filter\FilterData;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Custom ConditionFilterRenderer for repository viewer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ConditionFilterRenderer extends \Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer
{
    /**
     * Renders the filterdata as condition
     */
    public function render()
    {
        $conditions = array();

        $parentCondition = parent::render();
        if ($parentCondition instanceof Condition)
        {
            $conditions[] = $parentCondition;
        }

        $excludedContentObjectIds = $this->get_filter_data()->getExcludedContentObjectIds();
        if (is_array($excludedContentObjectIds) && !empty($excludedContentObjectIds))
        {
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                    $excludedContentObjectIds
                )
            );
        }
        
        if(count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }

    /**
     * @return FilterData
     */
    public function get_filter_data()
    {
        return parent::get_filter_data();
    }
}