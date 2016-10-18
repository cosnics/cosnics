<?php
namespace Chamilo\Core\Repository\UserView\Table\UserView;

use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserViewTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     *
     * @see \libraries\format\TableColumnModel::initialize_columns()
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(UserView :: class_name(), UserView :: PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(UserView :: class_name(), UserView :: PROPERTY_DESCRIPTION));
    }
}
