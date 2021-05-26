<?php
namespace Chamilo\Configuration\Form\Table\Element;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 * Table data provider for the schema
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Returns the data as a resultset
     *
     * @param \common\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);

        return DataManager::retrieves(Element::class, $parameters);
    }

    /**
     * Counts the data
     *
     * @param \common\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count(Element::class, new DataClassCountParameters($condition));
    }
}