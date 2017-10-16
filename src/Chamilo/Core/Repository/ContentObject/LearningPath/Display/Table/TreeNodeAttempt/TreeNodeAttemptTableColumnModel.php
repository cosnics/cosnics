<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeAttempt;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Table\TableColumnModel;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeAttemptTableColumnModel extends TableColumnModel implements TableColumnModelActionsColumnSupport
{
    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn('last_start_time'));
        $this->add_column(new StaticTableColumn('status'));

        if($this->get_component()->getCurrentTreeNode()->supportsScore())
        {
            $this->add_column(new StaticTableColumn('score'));
        }

        $this->add_column(new StaticTableColumn('time'));
    }
}