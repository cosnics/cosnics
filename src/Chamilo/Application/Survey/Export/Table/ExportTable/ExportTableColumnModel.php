<?php
namespace Chamilo\Application\Survey\Export\Table\TrackerTable;

use Chamilo\Application\Survey\Export\Storage\DataClass\Export;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class ExportTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Export::class_name(), Export::PROPERTY_TEMPLATE_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(Export::class_name(), Export::PROPERTY_TEMPLATE_DESCRIPTION));
        $this->add_column(new DataClassPropertyTableColumn(Export::class_name(), Export::PROPERTY_CREATED));
        $this->add_column(new DataClassPropertyTableColumn(Export::class_name(), Export::PROPERTY_STATUS));
        $this->add_column(new DataClassPropertyTableColumn(Export::class_name(), Export::PROPERTY_FINISHED));
    }
}
?>