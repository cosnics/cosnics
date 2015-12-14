<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
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
abstract class EntryTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_FEEDBACK_COUNT = 'feedback_count';

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION));
        $this->add_column(new DataClassPropertyTableColumn(Entry :: class_name(), Entry :: PROPERTY_SUBMITTED));
        $this->add_column(new StaticTableColumn(Score :: PROPERTY_SCORE));
        $this->add_column(new StaticTableColumn(self :: PROPERTY_FEEDBACK_COUNT));
    }
}