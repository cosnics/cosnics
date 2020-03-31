<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Table;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class RepositoryTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;

    const DEFAULT_ORDER_COLUMN_INDEX = 3;

    const PROPERTY_TYPE = 'type';

    const PROPERTY_VERSION = 'version';

    public function initialize_columns()
    {
        $typeGlyph = new FontAwesomeGlyph('folder', array(), Translation::get('Type'));

        $this->add_column(new StaticTableColumn(self::PROPERTY_TYPE, $typeGlyph->render()));

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE)
        );
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION)
        );

        if (!$this->get_component()->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            $this->add_column(
                new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID)
            );
        }

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_MODIFICATION_DATE)
        );

        $versionGlyph = new FontAwesomeGlyph('undo', array(), Translation::get('Versions'));

        $this->add_column(new StaticTableColumn(self::PROPERTY_VERSION, $versionGlyph->render()));
    }
}
