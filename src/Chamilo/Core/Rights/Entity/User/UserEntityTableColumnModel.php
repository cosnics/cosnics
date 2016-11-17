<?php
namespace Chamilo\Core\Rights\Entity\User;

use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTableColumnModel;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 * Cell renderer for the user entity browser
 * 
 * @author Sven Vanpoucke
 */
class UserEntityTableColumnModel extends LocationEntityTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_OFFICIAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_USERNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_LASTNAME));
        
        parent::initialize_columns();
    }
}
