<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Version;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class VersionTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_TYPE = 'type';

    const USER = 'User';

    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TYPE)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );

        $this->addColumn(
            new StaticTableColumn(Translation::get(self::USER, null, Manager::context()))
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_COMMENT)
        );

        $glyph = new FontAwesomeGlyph('folder', [], Translation::get('Type'));
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_TYPE, $glyph->render())
        );
    }
}
