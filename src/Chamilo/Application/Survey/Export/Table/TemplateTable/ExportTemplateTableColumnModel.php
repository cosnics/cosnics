<?php
namespace Chamilo\Application\Survey\Export\Table\TemplateTable;

use Chamilo\Application\Survey\Export\Storage\DataClass\ExportTemplate;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class ExportTemplateTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(ExportTemplate::class_name(), ExportTemplate::PROPERTY_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(ExportTemplate::class_name(), ExportTemplate::PROPERTY_DESCRIPTION));
    }
}
?>