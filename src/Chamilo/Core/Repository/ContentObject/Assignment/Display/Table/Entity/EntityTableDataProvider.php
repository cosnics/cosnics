<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableDataProvider extends RecordTableDataProvider
{
    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::retrieve_data()
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param null $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Libraries\Storage\Iterator\RecordIterator|\Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->getAssignmentServiceBridge()->findEntitiesByEntityType(
            $this->getEntityTableParameters()->getEntityType(), $this->getFilterParameters()
        );
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::count_data()
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getAssignmentServiceBridge()->countEntitiesByEntityType(
            $this->getEntityTableParameters()->getEntityType(), $this->getFilterParameters()
        );
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters
     */
    protected function getEntityTableParameters()
    {
        return $this->getTable()->getEntityTableParameters();
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table | \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface
     */
    protected function getAssignmentServiceBridge()
    {
        return $this->getEntityTableParameters()->getAssignmentServiceBridge();
    }
}