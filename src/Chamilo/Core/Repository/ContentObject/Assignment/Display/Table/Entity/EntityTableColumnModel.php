<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity;

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
abstract class EntityTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_NAME = 'name';
    const PROPERTY_FIRST_ENTRY_DATE = 'first_entry_date';
    const PROPERTY_LAST_ENTRY_DATE = 'last_entry_date';
    const PROPERTY_ENTRY_COUNT = 'entry_count';
    const PROPERTY_FEEDBACK_COUNT = 'feedback_count';

    const DEFAULT_ORDER_COLUMN_INDEX = 4;
    const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new SortableStaticTableColumn(self::PROPERTY_FIRST_ENTRY_DATE));
        $this->add_column(new SortableStaticTableColumn(self::PROPERTY_LAST_ENTRY_DATE));
        $this->add_column(new SortableStaticTableColumn(self::PROPERTY_ENTRY_COUNT));
        $this->add_column(new StaticTableColumn(self::PROPERTY_FEEDBACK_COUNT));
    }
}