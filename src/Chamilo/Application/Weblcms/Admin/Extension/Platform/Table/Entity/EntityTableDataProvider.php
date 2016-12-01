<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\Entity;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTableDataProvider extends RecordTableDataProvider
{

    /**
     * Returns the data as a resultset
     * 
     * @param \libraries\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \libraries\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $helper_class = $this->get_table()->get_helper_class_name();
        return $helper_class::retrieve_table_data($condition, $count, $offset, $order_property);
    }

    /**
     * Counts the data
     * 
     * @param \libraries\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        $helper_class = $this->get_table()->get_helper_class_name();
        return $helper_class::count_table_data($condition);
    }
}