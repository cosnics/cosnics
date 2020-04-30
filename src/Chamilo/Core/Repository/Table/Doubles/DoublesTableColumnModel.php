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

    public function initialize_columns()
    {
        $typeGlyph = new FontAwesomeGlyph('folder', array(), Translation::get('Type'));

        $this->add_column(new StaticTableColumn(self::PROPERTY_TYPE, $typeGlyph->render()));

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );

        if (!$this->get_table()->is_detail())
        {
            $this->add_column(new StaticTableColumn(self::DUPLICATES));
        }
    }
}
