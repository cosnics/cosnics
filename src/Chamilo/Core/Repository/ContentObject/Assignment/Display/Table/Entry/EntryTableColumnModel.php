<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_FEEDBACK_COUNT = 'feedback_count';
    const DEFAULT_ORDER_COLUMN_INDEX = 2;
    const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE)
        );

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION)
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                $this->getEntryTableParameters()->getEntryClassName(), Entry::PROPERTY_SUBMITTED, Translation::get('Submitted')
            )
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                $this->getEntryTableParameters()->getScoreClassName(), Score::PROPERTY_SCORE, 'Score'
            )
        );

        $this->add_column(new StaticTableColumn(self::PROPERTY_FEEDBACK_COUNT));
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