<?php
namespace Chamilo\Configuration\Category\Table\Browser;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
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

    /**
     * Gets the number of learning objects in the table
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->get_component()->get_parent()->count_categories($condition);
    }

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if (is_null($order_property))
        {
            $order_property = new OrderBy();
        }

        $category_class_name = get_class($this->get_component()->get_parent()->getCategory());
        $order_property->add(
            new OrderProperty(
                new PropertyConditionVariable(
                    $category_class_name::class_name(), $category_class_name::PROPERTY_DISPLAY_ORDER
                )
            )
        );

        return $this->get_component()->get_parent()->retrieve_categories($condition, $offset, $count, $order_property);
    }
}
