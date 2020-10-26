<?php
namespace Chamilo\Core\Metadata\Schema\Table\Schema;

use Chamilo\Core\Metadata\Schema\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 * Table data provider for the schema
 *
 * @package Chamilo\Core\Metadata\Schema\Table\Schema
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SchemaTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Returns the data as a resultset
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_property
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);

        return DataManager::retrieves(Schema::class, $parameters);
    }

    /**
     * Counts the data
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return integer
     */
    public function count_data($condition)
    {
        return DataManager::count(Schema::class, new DataClassCountParameters($condition));
    }
}