<?php
namespace Chamilo\Core\Repository\Table\Complex;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.repository_manager.component.complex_browser
 */

/**
 * Table column model for the repository browser table
 */
class ComplexTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_TYPE = 'type';

    const SUBITEMS = 'subitems';

    /*
     * (non-PHPdoc) @see \libraries\format\TableColumnModel::initialize_columns()
     */

    /**
     * Adds the basic colummns to the table
     */
    protected function addBasicColumns()
    {
        $typeGlyph = new FontAwesomeGlyph('folder', array(), Translation::get('Type'));

        $this->add_column(new StaticTableColumn(self::PROPERTY_TYPE, $typeGlyph->render()));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE, false)
        );
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION, false)
        );
    }

    public function initialize_columns()
    {
        $this->addBasicColumns();
        $this->add_column(new StaticTableColumn(Translation::get(self::SUBITEMS)));
    }
}
