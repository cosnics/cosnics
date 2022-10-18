<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeAttempt;

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
    public function initializeColumns()
    {
        $this->addColumn(new StaticTableColumn('last_start_time'));
        $this->addColumn(new StaticTableColumn('status'));

        if($this->get_component()->getCurrentTreeNode()->supportsScore())
        {
            $this->addColumn(new StaticTableColumn('score'));
        }

        $this->addColumn(new StaticTableColumn('time'));
    }
}