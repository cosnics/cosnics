<?php
namespace Chamilo\Application\Survey\Table\Share;

use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class ShareTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Publication::class_name(), Publication::PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID, null, false));
        $this->add_column(
            new DataClassPropertyTableColumn(Publication::class_name(), Publication::PROPERTY_PUBLISHED));
    }
}
