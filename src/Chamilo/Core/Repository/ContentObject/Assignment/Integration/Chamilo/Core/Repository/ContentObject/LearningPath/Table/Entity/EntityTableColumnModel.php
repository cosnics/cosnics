<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

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
    const PROPERTY_FIRSTNAME = 'firstname';
    const PROPERTY_LASTNAME = 'lastname';

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_LASTNAME));

        parent::initialize_columns();
    }
}