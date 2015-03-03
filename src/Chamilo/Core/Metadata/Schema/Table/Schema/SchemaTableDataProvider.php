<?php
namespace Chamilo\Core\Metadata\Schema\Table\Schema;

use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Core\Metadata\Schema\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * Table data provider for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SchemaTableDataProvider extends DataClassTableDataProvider
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
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        
        return DataManager :: retrieves(Schema :: class_name(), $parameters);
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
        return DataManager :: count(Schema :: class_name(), $condition);
    }
}