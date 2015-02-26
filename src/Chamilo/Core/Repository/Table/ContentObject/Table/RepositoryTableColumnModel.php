<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Table;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
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

    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
                self :: PROPERTY_TYPE, 
                Theme :: getInstance()->getCommonImage(
                    'action_category', 
                    'png', 
                    Translation :: get('Type'), 
                    null, 
                    ToolbarItem :: DISPLAY_ICON)));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_MODIFICATION_DATE));
        $this->add_column(new StaticTableColumn(self :: PROPERTY_VERSION, ContentObject :: get_version_header()));
    }
}
