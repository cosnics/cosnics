<?php
namespace Chamilo\Application\Survey\Export\Table\RegistrationTable;

use Chamilo\Application\Survey\Export\Storage\DataClass\ExportRegistration;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class ExportRegistrationTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(ExportRegistration :: class_name(), ExportRegistration :: PROPERTY_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(
                ExportRegistration :: class_name(), 
                ExportRegistration :: PROPERTY_DESCRIPTION));
    }
}
?>