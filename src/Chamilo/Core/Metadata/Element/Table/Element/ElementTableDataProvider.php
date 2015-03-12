<?php
namespace Chamilo\Core\Metadata\Element\Table\Element;

use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Table data provider for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Returns the data as a resultset
     * 
     * @param \libraries\storage\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $order_property[] = new OrderBy(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_DISPLAY_ORDER));
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        
        return DataManager :: retrieves(Element :: class_name(), $parameters);
    }

    /**
     * Counts the data
     * 
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager :: count(Element :: class_name(), $condition);
    }
}