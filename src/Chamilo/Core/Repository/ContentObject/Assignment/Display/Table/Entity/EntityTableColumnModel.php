<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity;

use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_NAME = 'name';
    const PROPERTY_FIRST_ENTRY_DATE = 'first_entry_date';
    const PROPERTY_LAST_ENTRY_DATE = 'last_entry_date';
    const PROPERTY_ENTRY_COUNT = 'entry_count';
    const PROPERTY_FEEDBACK_COUNT = 'feedback_count';
    const PROPERTY_LAST_SCORE = 'last_score';
    const PROPERTY_MEMBERS = 'members';

    const DEFAULT_ORDER_COLUMN_INDEX = 1;
    const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Format\Table\Table $table
     */
    public function __construct($table)
    {
        parent::__construct($table);

        if($this->getAssignmentServiceBridge()->canEditAssignment())
        {
            $this->set_default_order_column(4);
        }
    }

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->addEntityColumns();

        if($this->getAssignmentServiceBridge()->canEditAssignment())
        {
            $this->add_column(new SortableStaticTableColumn(self::PROPERTY_FIRST_ENTRY_DATE));
            $this->add_column(new SortableStaticTableColumn(self::PROPERTY_LAST_ENTRY_DATE));
            $this->add_column(new SortableStaticTableColumn(self::PROPERTY_ENTRY_COUNT));
            $this->add_column(new StaticTableColumn(self::PROPERTY_FEEDBACK_COUNT));
            $this->add_column(new StaticTableColumn(self::PROPERTY_LAST_SCORE));
        }
    }

    protected function addEntityColumns()
    {
        $entityProperties = $this->getEntityTableParameters()->getEntityProperties();
        foreach ($entityProperties as $entityProperty)
        {
            $this->add_column(
                new DataClassPropertyTableColumn($this->getEntityTableParameters()->getEntityClass(), $entityProperty)
            );
        }

        if($this->getEntityTableParameters()->hasEntityMultipleMembers())
        {
            $this->add_column(new StaticTableColumn(self::PROPERTY_MEMBERS), 1);
        }
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
