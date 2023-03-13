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
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 3;
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_VERSION = 'version';

    public function initializeColumns()
    {
        $typeGlyph = new FontAwesomeGlyph('folder', [], Translation::get('Type'));

        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE, $typeGlyph->render()));

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );

        if (!$this->get_component()->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            $this->addColumn(
                new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_OWNER_ID)
            );
        }

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE)
        );

        $versionGlyph = new FontAwesomeGlyph('undo', [], Translation::get('Versions'));

        $this->addColumn(new StaticTableColumn(self::PROPERTY_VERSION, $versionGlyph->render()));
    }
}
