<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableColumnModel::initializeColumns()
     */
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(Workspace::class, Workspace::PROPERTY_NAME));
        $this->addColumn(
            new DataClassPropertyTableColumn(Workspace::class, Workspace::PROPERTY_CREATOR_ID, null, false));
        $this->addColumn(
            new DataClassPropertyTableColumn(Workspace::class, Workspace::PROPERTY_CREATION_DATE));
    }
}
