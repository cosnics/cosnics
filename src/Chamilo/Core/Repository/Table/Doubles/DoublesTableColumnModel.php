<?php
namespace Chamilo\Core\Repository\Table\Doubles;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class DoublesTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DUPLICATES = 'Duplicates';
    const PROPERTY_TYPE = 'type';

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

        if (!$this->getTable()->is_detail())
        {
            $this->addColumn(new StaticTableColumn(self::DUPLICATES));
        }
    }
}
