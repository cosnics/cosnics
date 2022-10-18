<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserProgressTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 3;

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));

        $showEmail = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'show_email_addresses'));

        if($showEmail)
        {
            $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }

        $this->addColumn(new SortableStaticTableColumn('progress'));
        $this->addColumn(new SortableStaticTableColumn('completed'));
        $this->addColumn(new SortableStaticTableColumn('started'));
    }
}