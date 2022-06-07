<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\Vocabulary;

use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table cell renderer for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VocabularyTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     *
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                        Manager::PARAM_VOCABULARY_ID => $result->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_VOCABULARY_ID => $result->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->as_html();
    }

    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param mixed $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case VocabularyTableColumnModel::COLUMN_DEFAULT :
                $translationVariable = $result->isDefault() ? 'Default' : 'DefaultNa';

                $extraClasses = $result->isDefault() ? [] : array('text-muted');

                $glyph = new FontAwesomeGlyph(
                    'check-circle', $extraClasses,
                    Translation::get($translationVariable, null, $this->get_component()->package()), 'fas'
                );

                $link = $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DEFAULT,
                        Manager::PARAM_VOCABULARY_ID => $result->get_id()
                    )
                );

                if ($result->isDefault())
                {
                    return $glyph->render();
                }
                else
                {
                    return '<a href="' . $link . '">' . $glyph->render() . '</a>';
                }

                break;
        }

        return parent::render_cell($column, $result);
    }
}