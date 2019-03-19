<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
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
    const DEFAULT_ORDER_COLUMN_INDEX = 3;

    const COLUMN_LAST_SCORE = 'last_score';

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_FIRSTNAME));

        $showEmail = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'show_email_addresses'));

        if($showEmail)
        {
            $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_EMAIL));
        }

        $this->add_column(new SortableStaticTableColumn('progress'));
        $this->add_column(new SortableStaticTableColumn('completed'));
        $this->add_column(new SortableStaticTableColumn('started'));

        if($this->getCurrentTreeNode()->supportsScore())
        {
            $this->add_column(new StaticTableColumn(self::COLUMN_LAST_SCORE));
        }
    }

    /**
     * @return TreeNode
     */
    protected function getCurrentTreeNode()
    {
        return $this->get_component()->getCurrentTreeNode();
    }
}