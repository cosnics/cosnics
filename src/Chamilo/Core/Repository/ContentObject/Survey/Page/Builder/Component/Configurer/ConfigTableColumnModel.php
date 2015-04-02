<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Configurer;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\PageConfig;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class ConfigTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(PageConfig :: class_name(), PageConfig :: PROPERTY_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(PageConfig :: class_name(), PageConfig :: PROPERTY_DESCRIPTION));
        $this->add_column(
            new DataClassPropertyTableColumn(PageConfig :: class_name(), PageConfig :: PROPERTY_CONFIG_CREATED));
        $this->add_column(
            new DataClassPropertyTableColumn(PageConfig :: class_name(), PageConfig :: PROPERTY_CONFIG_UPDATED));
    }
}
?>