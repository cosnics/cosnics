<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserProgressTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_EMAIL));
        $this->add_column(new StaticTableColumn('progress'));
        $this->add_column(new SortableStaticTableColumn('completed'));
        $this->add_column(new SortableStaticTableColumn('started'));
    }
}