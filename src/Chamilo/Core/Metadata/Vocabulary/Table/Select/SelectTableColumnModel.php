<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\Select;

use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
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
    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
                self::COLUMN_TYPE, 
                Theme::getInstance()->getImage(
                    'Action/Category', 
                    'png', 
                    Translation::get('Type', null, $this->get_component()->package()), 
                    null, 
                    ToolbarItem::DISPLAY_ICON, 
                    false, 
                    'Chamilo\Configuration')));
        
        $this->add_column(new DataClassPropertyTableColumn(Vocabulary::class_name(), Vocabulary::PROPERTY_VALUE));
    }
}