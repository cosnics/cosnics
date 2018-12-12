<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableDataProvider extends RecordTableDataProvider
{
    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::retrieve_data()
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param null $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $orderProperty = null)
    {
        return $this->getEntryTableParameters()->getAssignmentServiceBridge()->findEntriesForEntityTypeAndId(
            $this->getEntryTableParameters()->getEntityType(),
            $this->getEntryTableParameters()->getEntityId(),
            $condition,
            $offset,
            $count,
            $orderProperty
        );
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        return $this->getEntryTableParameters()->getAssignmentServiceBridge()->countEntriesForEntityTypeAndId(
            $this->getEntryTableParameters()->getEntityType(),
            $this->getEntryTableParameters()->getEntityId(),
            $condition
        );
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters
     */
    protected function getEntryTableParameters()
    {
        return $this->getTable()->getEntryTableParameters();
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table | \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }
}