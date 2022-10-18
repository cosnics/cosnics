<?php
namespace Chamilo\Core\Rights\Entity\User;

use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTableColumnModel;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 * Cell renderer for the user entity browser
 *
 * @author     Sven Vanpoucke
 * @deprecated Should not be needed anymore
 */
class UserEntityTableColumnModel extends LocationEntityTableColumnModel
{

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));

        parent::initializeColumns();
    }
}
