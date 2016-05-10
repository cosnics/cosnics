<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\Vocabulary;

use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table column model for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VocabularyTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_DEFAULT = 'default';

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(
                Vocabulary :: class_name(),
                Vocabulary :: PROPERTY_VALUE,
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(Vocabulary :: PROPERTY_VALUE)->upperCamelize(),
                    null,
                    'Chamilo\Core\Metadata')));

        $this->add_column(
            new StaticTableColumn(
                self :: COLUMN_DEFAULT,
                Theme :: getInstance()->getImage(
                    'Action/Default',
                    'png',
                    Translation :: get('Default', null, $this->get_component()->package()),
                    null,
                    ToolbarItem :: DISPLAY_ICON,
                    false,
                    $this->get_component()->package())));
    }
}