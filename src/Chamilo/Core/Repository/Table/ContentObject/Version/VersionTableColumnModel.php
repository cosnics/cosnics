<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Version;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class VersionTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const USER = 'User';

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TYPE)
        );

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE)
        );

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION)
        );

        $this->add_column(
            new StaticTableColumn(Translation::get(self::USER, null, \Chamilo\Core\User\Manager::context()))
        );

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_MODIFICATION_DATE)
        );

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_COMMENT)
        );
        
        $this->add_column(
            new StaticTableColumn(
                Theme::getInstance()->getCommonImage(
                    'Action/Category',
                    'png',
                    Translation::get('Type'),
                    null,
                    ToolbarItem::DISPLAY_ICON
                )
            )
        );
    }
}
