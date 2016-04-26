<?php
namespace Chamilo\Core\Repository\Table\Complex;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: complex_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component.complex_browser
 */

/**
 * Table column model for the repository browser table
 */
class ComplexTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const SUBITEMS = 'subitems';

    /*
     * (non-PHPdoc) @see \libraries\format\TableColumnModel::initialize_columns()
     */
    public function initialize_columns()
    {
        $this->addBasicColumns();
        $this->add_column(new StaticTableColumn(Translation:: get(self :: SUBITEMS)));
    }

    /**
     * Adds the basic colummns to the table
     */
    protected function addBasicColumns()
    {
        $this->add_column(
            new StaticTableColumn(
                Theme:: getInstance()->getCommonImage(
                    'Action/Category',
                    'png',
                    Translation:: get('Type'),
                    null,
                    ToolbarItem :: DISPLAY_ICON
                )
            )
        );
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject:: class_name(), ContentObject :: PROPERTY_TITLE, false)
        );
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject:: class_name(), ContentObject :: PROPERTY_DESCRIPTION, false)
        );
    }
}
