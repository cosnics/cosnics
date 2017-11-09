<?php
namespace Chamilo\Core\Repository\Table\ImpactView;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

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
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TYPE));
        
        $this->add_column(
            new StaticTableColumn(
                self::COLUMN_CATEGORY, 
                Translation::getInstance()->getTranslation('Category', array(), Manager::context())));
        
        $this->add_column(
            new StaticTableColumn(
                self::COLUMN_SAFE_DELETE, 
                Translation::getInstance()->getTranslation('SafeDelete', array(), Manager::context())));
    }
}
