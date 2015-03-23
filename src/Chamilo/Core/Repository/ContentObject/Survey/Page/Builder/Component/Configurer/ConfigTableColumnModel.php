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
        $columns = array();
        $columns[] = new DataClassPropertyTableColumn(PageConfig :: CLASS_NAME, PageConfig :: PROPERTY_NAME);
        $columns[] = new DataClassPropertyTableColumn(
            PageConfig :: CLASS_NAME, 
            PageConfig :: PROPERTY_DESCRIPTION);
        $columns[] = new DataClassPropertyTableColumn(
            PageConfig :: CLASS_NAME, 
            PageConfig :: PROPERTY_CONFIG_CREATED);
        $columns[] = new DataClassPropertyTableColumn(
            PageConfig :: CLASS_NAME, 
            PageConfig :: PROPERTY_CONFIG_UPDATED);
        
        return $columns;
    }
}
?>