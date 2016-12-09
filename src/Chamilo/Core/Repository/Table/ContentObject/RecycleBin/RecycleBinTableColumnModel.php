<?php
namespace Chamilo\Core\Repository\Table\ContentObject\RecycleBin;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class RecycleBinTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const ORIGINAL_LOCATION = 'OriginalLocation';

    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
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
        $this->add_column(new StaticTableColumn(Translation::get(self::ORIGINAL_LOCATION)));
    }

    public static function get_action_column()
    {
        if (! isset(self::$action_column))
        {
            self::$action_column = new StaticTableColumn('');
        }
        return self::$action_column;
    }
}
