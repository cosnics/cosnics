<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package Chamilo\Application\Portfolio\Table\User
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
    }
}