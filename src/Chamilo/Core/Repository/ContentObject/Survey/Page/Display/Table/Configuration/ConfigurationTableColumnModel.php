<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Table\Configuration;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Configuration;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class ConfigurationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Configuration :: class_name(), Configuration :: PROPERTY_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(Configuration :: class_name(), Configuration :: PROPERTY_DESCRIPTION));
        $this->add_column(
            new DataClassPropertyTableColumn(Configuration :: class_name(), Configuration :: PROPERTY_CREATED));
        $this->add_column(
            new DataClassPropertyTableColumn(Configuration :: class_name(), Configuration :: PROPERTY_UPDATED));
    }
}
?>