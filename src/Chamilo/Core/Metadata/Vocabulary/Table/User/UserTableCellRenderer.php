<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\User;

use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
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
                Translation::get('Vocabulary', null, Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('language', array(), null, 'fas'), $this->get_component()->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_BROWSE,
                    \Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID => $this->get_component()
                        ->getSelectedElementId(), Manager::PARAM_USER_ID => $result[User::PROPERTY_ID]
                )
            ), ToolbarItem::DISPLAY_ICON
            )
        );

        // if ($result->is_fixed())
        // {
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('EditNA', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/EditNa'),
        // null,
        // ToolbarItem :: DISPLAY_ICON));

        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('DeleteNA', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/DeleteNa'),
        // null,
        // ToolbarItem :: DISPLAY_ICON));
        // }
        // else
        // {
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/Edit'),
        // $this->get_component()->get_url(
        // array(
        // Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE,
        // Manager :: PARAM_ELEMENT_ID => $result->get_id())),
        // ToolbarItem :: DISPLAY_ICON));

        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/Delete'),
        // $this->get_component()->get_url(
        // array(
        // Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
        // Manager :: PARAM_ELEMENT_ID => $result->get_id())),
        // ToolbarItem :: DISPLAY_ICON,
        // true));
        // }

        // $limit = DataManager :: get_display_order_total_for_schema($result->get_schema_id());

        // // show move up button
        // if ($result->get_display_order() != 1 && $limit != 1)
        // {
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('MoveUp', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/Up'),
        // $this->get_component()->get_url(
        // array(
        // Manager :: PARAM_ACTION => Manager :: ACTION_MOVE,
        // Manager :: PARAM_ELEMENT_ID => $result->get_id(),
        // Manager :: PARAM_MOVE => - 1)),
        // ToolbarItem :: DISPLAY_ICON));
        // }
        // else
        // {
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('MoveUpNA', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/UpNa'),
        // null,
        // ToolbarItem :: DISPLAY_ICON));
        // }

        // // show move down button
        // if ($result->get_display_order() < $limit)
        // {
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('MoveDown', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/Down'),
        // $this->get_component()->get_url(
        // array(
        // Manager :: PARAM_ACTION => Manager :: ACTION_MOVE,
        // Manager :: PARAM_MOVE => 1,
        // Manager :: PARAM_ELEMENT_ID => $result->get_id())),
        // ToolbarItem :: DISPLAY_ICON));
        // }
        // else
        // {
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('MoveDownNA', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/DownNa'),
        // null,
        // ToolbarItem :: DISPLAY_ICON));
        // }

        return $toolbar->as_html();
    }
}