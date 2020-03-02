<?php
namespace Chamilo\Core\Repository\Table\ContentObject\RecycleBin;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class RecycleBinTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const ORIGINAL_LOCATION = 'OriginalLocation';

    const PROPERTY_TYPE = 'type';

    public function initialize_columns()
    {
        $glyph = new FontAwesomeGlyph('folder', array(), Translation::get('Type'));
        $this->add_column(
            new StaticTableColumn(self::PROPERTY_TYPE, $glyph->render())
        );
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE)
        );
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION)
        );
        $this->add_column(new StaticTableColumn(Translation::get(self::ORIGINAL_LOCATION)));
    }
}
