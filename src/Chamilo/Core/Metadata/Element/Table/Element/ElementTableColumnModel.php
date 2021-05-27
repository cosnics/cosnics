<?php
namespace Chamilo\Core\Metadata\Element\Table\Element;

use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
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
                self::COLUMN_PREFIX, Translation::get(
                (string) StringUtilities::getInstance()->createString(self::COLUMN_PREFIX)->upperCamelize(), null,
                $this->get_component()->package()
            )
            )
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                Element::class, Element::PROPERTY_NAME, Translation::get(
                (string) StringUtilities::getInstance()->createString(Element::PROPERTY_NAME)->upperCamelize(), null,
                $this->get_component()->package()
            )
            )
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                Element::class, Element::PROPERTY_DISPLAY_NAME, Translation::get(
                (string) StringUtilities::getInstance()->createString(Element::PROPERTY_DISPLAY_NAME)->upperCamelize(),
                null, $this->get_component()->package()
            ), false
            )
        );

        $glyph = new FontAwesomeGlyph(
            'pen-nib', [], Translation::get('FreeValues', null, $this->get_component()->package()), 'fas'
        );

        $this->add_column(
            new StaticTableColumn(
                self::COLUMN_VALUE_FREE, $glyph->render()
            )
        );

        $glyph = new FontAwesomeGlyph(
            'globe', [], Translation::get('PredefinedValues', null, $this->get_component()->package()), 'fas'
        );

        $this->add_column(
            new StaticTableColumn(
                self::COLUMN_VALUE_VOCABULARY_PREDEFINED, $glyph->render()
            )
        );

        $glyph = new FontAwesomeGlyph(
            'users', [], Translation::get('UserValues', null, $this->get_component()->package()), 'fas'
        );

        $this->add_column(
            new StaticTableColumn(
                self::COLUMN_VALUE_VOCABULARY_USER, $glyph->render()
            )
        );
    }
}