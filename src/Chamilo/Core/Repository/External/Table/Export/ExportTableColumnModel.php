<?php
namespace Chamilo\Core\Repository\External\Table\Export;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class ExportTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_TYPE = 'type';

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $glyph = new FontAwesomeGlyph('folder', Translation::get('Type'));
        $this->addColumn(

            new StaticTableColumn(self::PROPERTY_TYPE, $glyph->render())
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );
    }
}
