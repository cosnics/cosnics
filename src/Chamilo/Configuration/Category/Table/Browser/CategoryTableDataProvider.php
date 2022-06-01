<?php
namespace Chamilo\Configuration\Category\Table\Browser;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package application.common.category_manager.component.category_browser
 */

/**
 * Data provider for a repository browser table.
 * This class implements some functions to allow repository browser tables
 * to retrieve information about the learning objects to display.
 */
class CategoryTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->get_component()->get_parent()->count_categories($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        if (is_null($orderBy))
        {
            $orderBy = new OrderBy();
        }

        $category_class_name = get_class($this->get_component()->get_parent()->getCategory());
        $orderBy->add(
            new OrderProperty(
                new PropertyConditionVariable(
                    $category_class_name, $category_class_name::PROPERTY_DISPLAY_ORDER
                )
            )
        );

        return $this->get_component()->get_parent()->retrieve_categories($condition, $offset, $count, $orderBy);
    }
}
