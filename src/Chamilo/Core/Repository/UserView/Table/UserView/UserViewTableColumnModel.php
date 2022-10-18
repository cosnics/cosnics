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
     * @see \libraries\format\TableColumnModel::initializeColumns()
     */
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(UserView::class, UserView::PROPERTY_NAME));
        $this->addColumn(new DataClassPropertyTableColumn(UserView::class, UserView::PROPERTY_DESCRIPTION));
    }
}
