<?php
namespace Chamilo\Core\Repository\Ajax\Tables;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\DataTable\Column\DataClassPropertyDataTableColumn;
use Chamilo\Libraries\Format\DataTable\DataTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Ajax\Tables$ContentObjectDataTableColumnModel
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectDataTableColumnModel extends DataTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyDataTableColumn(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE)));
        $this->addColumn(
            new DataClassPropertyDataTableColumn(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION)));
        $this->addColumn(
            new DataClassPropertyDataTableColumn(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_MODIFICATION_DATE)));
    }
}
