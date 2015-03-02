<?php
namespace Chamilo\Core\Metadata\Element\Table\Element;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

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
            case ElementTableColumnModel :: COLUMN_PREFIX :
                return $result->get_namespace();
                break;
            case ElementTableColumnModel :: COLUMN_CONTROLLED_VOCABULARY :
                $has_controlled_vocabulary = DataManager :: element_has_controlled_vocabulary($result->get_id()) ? 'true' : 'false';

                return Theme :: getInstance()->getCommonImage('action_setting_' . $has_controlled_vocabulary);
                break;
        }

        return parent :: render_cell($column, $result);
    }

    /**
     * Returns the actions toolbar
     *
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($result->is_fixed())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_edit_na.png',
                    null,
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('DeleteNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_delete_na.png',
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_edit.png',
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE,
                            Manager :: PARAM_ELEMENT_ID => $result->get_id())),
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_delete.png',
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
                            Manager :: PARAM_ELEMENT_ID => $result->get_id())),
                    ToolbarItem :: DISPLAY_ICON,
                    true));
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ManageControlledVocabulary', null, 'core\metadata'),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Metadata\Element', 'Action/controlled_vocabulary'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_VOCABULATE,
                        Manager :: PARAM_ELEMENT_ID => $result->get_id())),
                ToolbarItem :: DISPLAY_ICON));

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('MetadataDefaultValues', null, 'core\metadata'),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Metadata\Element', 'Action/default'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => null,
                        \Chamilo\Core\Metadata\Manager :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_VALUE,
                        \Chamilo\Core\Metadata\Value\Manager :: PARAM_ACTION => \Chamilo\Core\Metadata\Value\Manager :: ACTION_ELEMENT,
                        Manager :: PARAM_ELEMENT_ID => $result->get_id())),
                ToolbarItem :: DISPLAY_ICON));

        $limit = DataManager :: get_display_order_total_for_schema($result->get_schema_id());

        // show move up button
        if ($result->get_display_order() != "1" && $limit != "1")
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveUp', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_up.png',
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_MOVE,
                            Manager :: PARAM_ELEMENT_ID => $result->get_id(),
                            Manager :: PARAM_MOVE => - 1)),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveUpNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_up_na.png',
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        // show move down button
        if ($result->get_display_order() < $limit)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveDown', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_down.png',
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_MOVE,
                            Manager :: PARAM_MOVE => 1,
                            Manager :: PARAM_ELEMENT_ID => $result->get_id())),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveDownNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_down_na.png',
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}