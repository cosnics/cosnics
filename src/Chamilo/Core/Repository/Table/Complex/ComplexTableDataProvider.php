<?php
namespace Chamilo\Core\Repository\Table\Complex;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * $Id: complex_browser_table_data_provider.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib.repository_manager.component.complex_browser
 */
class ComplexTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $order_property[] = new OrderBy(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: class_name(), 
                ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER));
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        
        return DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(), 
            $parameters);
    }

    /**
     * Gets the number of content objects in the table
     * 
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager :: count_complex_content_object_items(ComplexContentObjectItem :: class_name(), $condition);
    }
}
