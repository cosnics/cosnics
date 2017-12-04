<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableColumnModel
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableColumnModel
{
    const DEFAULT_ORDER_COLUMN_INDEX = 2;

    const PROPERTY_FIRSTNAME = 'firstname';
    const PROPERTY_LASTNAME = 'lastname';

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_LASTNAME));
        $this->add_column(new StaticTableColumn(self::PROPERTY_FIRST_ENTRY_DATE));
        $this->add_column(new StaticTableColumn(self::PROPERTY_LAST_ENTRY_DATE));
        $this->add_column(new StaticTableColumn(self::PROPERTY_ENTRY_COUNT));
        $this->add_column(new StaticTableColumn(self::PROPERTY_FEEDBACK_COUNT));
    }
}