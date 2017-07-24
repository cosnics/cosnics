<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\UserAttemptStatusViewer;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;

/**
 * This class represents a column model for a publication candidate table
 */
class UserAttemptStatusViewerTableColumnModel extends DataClassTableColumnModel
{
    const COLUMN_STATUS = 'status';
    const COLUMN_TITLE = 'title';
    const COLUMN_DESCRIPTION = 'description';
    const COLUMN_START_DATE = 'start_date';
    const COLUMN_END_DATE = 'end_date';

    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(self::COLUMN_STATUS));
        $this->add_column(new StaticTableColumn(self::COLUMN_TITLE));
        $this->add_column(new StaticTableColumn(self::COLUMN_DESCRIPTION));
        $this->add_column(new StaticTableColumn(self::COLUMN_START_DATE));
        $this->add_column(new StaticTableColumn(self::COLUMN_END_DATE));
    }
}
