<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\Select;

use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Table column model for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SelectTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_TYPE = 'type';

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $glyph = new FontAwesomeGlyph(
            'folder', [], Translation::get('Type', null, $this->get_component()->package()), 'fas'
        );

        $this->addColumn(
            new StaticTableColumn(self::COLUMN_TYPE, $glyph->render())
        );

        $this->addColumn(new DataClassPropertyTableColumn(Vocabulary::class, Vocabulary::PROPERTY_VALUE));
    }
}