<?php
namespace Chamilo\Core\Metadata\Element\Table\Element;

use Chamilo\Core\Metadata\Storage\DataClass\Element;
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
class ElementTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_PREFIX = 'prefix';
    const COLUMN_VALUE_FREE = 'value_free';
    const COLUMN_VALUE_VOCABULARY_PREDEFINED = 'value_vocabulary_predefined';
    const COLUMN_VALUE_VOCABULARY_USER = 'value_vocabulary_user';

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
                self :: COLUMN_PREFIX,
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(self :: COLUMN_PREFIX)->upperCamelize(),
                    null,
                    $this->get_component()->package())));

        $this->add_column(
            new DataClassPropertyTableColumn(
                Element :: class_name(),
                Element :: PROPERTY_NAME,
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(Element :: PROPERTY_NAME)->upperCamelize(),
                    null,
                    $this->get_component()->package())));

        $this->add_column(
            new DataClassPropertyTableColumn(
                Element :: class_name(),
                Element :: PROPERTY_DISPLAY_NAME,
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(Element :: PROPERTY_DISPLAY_NAME)->upperCamelize(),
                    null,
                    $this->get_component()->package()),
                false));

        $this->add_column(
            new StaticTableColumn(
                self :: COLUMN_VALUE_FREE,
                Theme :: getInstance()->getImage(
                    'Action/Value/Free',
                    'png',
                    Translation :: get('PredefinedValues', null, $this->get_component()->package()),
                    null,
                    ToolbarItem :: DISPLAY_ICON,
                    false,
                    $this->get_component()->package())));

        $this->add_column(
            new StaticTableColumn(
                self :: COLUMN_VALUE_VOCABULARY_PREDEFINED,
                Theme :: getInstance()->getImage(
                    'Action/Value/VocabularyPredefined',
                    'png',
                    Translation :: get('VocabularyPredefinedValues', null, $this->get_component()->package()),
                    null,
                    ToolbarItem :: DISPLAY_ICON,
                    false,
                    $this->get_component()->package())));

        $this->add_column(
            new StaticTableColumn(
                self :: COLUMN_VALUE_VOCABULARY_USER,
                Theme :: getInstance()->getImage(
                    'Action/Value/VocabularyUser',
                    'png',
                    Translation :: get('VocabularyUserValues', null, $this->get_component()->package()),
                    null,
                    ToolbarItem :: DISPLAY_ICON,
                    false,
                    $this->get_component()->package())));
    }
}