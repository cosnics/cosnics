<?php
namespace Chamilo\Core\Metadata\Element\Table\Element;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
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
class ElementTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     *
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($element)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if ($element->is_fixed())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('EditNA', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('pencil-alt', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DeleteNA', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('times', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                            Manager::PARAM_ELEMENT_ID => $element->get_id()
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
                            Manager::PARAM_ELEMENT_ID => $element->get_id()
                        )
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        $limit = DataManager::get_display_order_total_for_schema($element->get_schema_id());

        // show move up button
        if ($element->get_display_order() != 1 && $limit != 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUp', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-up'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_MOVE,
                            Manager::PARAM_ELEMENT_ID => $element->get_id(), Manager::PARAM_MOVE => - 1
                        )
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUpNA', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-up', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        // show move down button
        if ($element->get_display_order() < $limit)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDown', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-down'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_MOVE, Manager::PARAM_MOVE => 1,
                            Manager::PARAM_ELEMENT_ID => $element->get_id()
                        )
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDownNA', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-down', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case ElementTableColumnModel::COLUMN_PREFIX :
                return $result->get_namespace();
                break;
            case ElementTableColumnModel::COLUMN_VALUE_VOCABULARY_PREDEFINED :
                $link = $result->isVocabularyPredefined() ? $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_VOCABULARY,
                        \Chamilo\Core\Metadata\Vocabulary\Manager::PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Manager::ACTION_BROWSE,
                        Manager::PARAM_ELEMENT_ID => $result->get_id()
                    )
                ) : null;

                $extraClasses = $result->isVocabularyPredefined() ? [] : array('text-muted');
                $glyph = new FontAwesomeGlyph(
                    'globe', $extraClasses,
                    Translation::get('PredefinedValues', null, $this->get_component()->package()), 'fas'
                );

                if ($link)
                {
                    return '<a href="' . $link . '">' . $glyph->render() . '</a>';
                }
                else
                {
                    return $glyph->render();
                }
                break;
            case ElementTableColumnModel::COLUMN_VALUE_VOCABULARY_USER :
                $link = $result->isVocabularyUserDefined() ? $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_VOCABULARY,
                        \Chamilo\Core\Metadata\Vocabulary\Manager::PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Manager::ACTION_USER,
                        Manager::PARAM_ELEMENT_ID => $result->get_id()
                    )
                ) : null;

                $extraClasses = $result->isVocabularyUserDefined() ? [] : array('text-muted');
                $glyph = new FontAwesomeGlyph(
                    'users', $extraClasses, Translation::get('UserValues', null, $this->get_component()->package()),
                    'fas'
                );

                if ($link)
                {
                    return '<a href="' . $link . '">' . $glyph->render() . '</a>';
                }
                else
                {
                    return $glyph->render();
                }
                break;
        }

        return parent::render_cell($column, $result);
    }
}