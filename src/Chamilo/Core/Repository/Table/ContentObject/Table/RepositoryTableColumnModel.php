<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Table;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class RepositoryTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_TYPE = 'type';
    const PROPERTY_VERSION = 'version';
    const DEFAULT_ORDER_COLUMN_INDEX = 3;
    const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;

    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
                self::PROPERTY_TYPE, 
                Theme::getInstance()->getCommonImage(
                    'Action/Category', 
                    'png', 
                    Translation::get('Type'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON)));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION));
        
        if (! $this->get_component()->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            $this->add_column(
                new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID));
        }
        
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_MODIFICATION_DATE));
        $this->add_column(new StaticTableColumn(self::PROPERTY_VERSION, ContentObject::get_version_header()));
    }
}
