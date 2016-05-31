<?php
namespace Chamilo\Configuration\Category\Table\Browser;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * $Id: category_browser_table_data_provider.class.php 191 2009-11-13 11:50:28Z chellee $
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

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $category_class_name = get_class($this->get_component()->get_parent()->get_category());
        $order_property[] = new OrderBy(
            new PropertyConditionVariable(
                $category_class_name :: class_name(), 
                $category_class_name :: PROPERTY_DISPLAY_ORDER));
        return $this->get_component()->get_parent()->retrieve_categories($condition, $offset, $count, $order_property);
    }

    /**
     * Gets the number of learning objects in the table
     * 
     * @return int
     */
    public function count_data($condition)
    {
        return $this->get_component()->get_parent()->count_categories($condition);
    }
}
