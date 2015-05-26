<?php
namespace Chamilo\Core\Repository\Table\ImpactView;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Description of impact_view_table_column_model
 * 
 * @author Pieterjan Broekaert Hogeschool Gent
 */
class ImpactViewTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_SAFE_DELETE = 'safe_delete';
    const COLUMN_CATEGORY = 'category';

    public function initialize_columns()
    {
        $content_object_table_alias = DataManager :: get_alias(ContentObject :: get_table_name());
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObject :: class_name(), 
                ContentObject :: PROPERTY_TYPE, 
                true, 
                $content_object_table_alias, 
                true));
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObject :: class_name(), 
                self :: COLUMN_CATEGORY, 
                true, 
                $content_object_table_alias, 
                true));
        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObject :: class_name(), 
                self :: COLUMN_SAFE_DELETE, 
                true, 
                $content_object_table_alias, 
                true));
    }
}
