<?php
namespace Chamilo\Core\Repository\Share\Table\Group;

use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Share\Manager;
use Chamilo\Core\Repository\Share\Table\ShareRightColumn;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * Cell renderer for the content object Group share rights browser
 *
 * @author Pieterjan Broekaert
 */
class GroupRightsTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $group)
    {
        if ($column instanceof ShareRightColumn)
        {
            $location = RepositoryRights :: get_instance()->get_location_by_identifier_from_users_subtree(
                RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                array_pop($this->get_component()->get_content_objects())->get_id(),
                array_pop($this->get_component()->get_content_objects())->get_owner_id());
            $rights = RepositoryRights :: get_instance()->get_granted_rights_for_rights_entity_item(
                \Chamilo\Core\Repository\Manager :: context(),
                PlatformGroupEntity :: ENTITY_TYPE,
                $group->get_id(),
                $location);

            $copy_right = array_search(RepositoryRights :: COPY_RIGHT, $rights);

            if ($copy_right)
            {
                array_splice($rights, $copy_right, 1);
                if ($column->get_right_id() == RepositoryRights :: COPY_RIGHT)
                {
                    return Theme :: getInstance()->getCommonImage('action_setting_true', 'png');
                }
            }

            if ($column->get_right_id() <= max($rights))
            {
                return Theme :: getInstance()->getCommonImage('action_setting_true', 'png');
            }
            else
            {
                return Theme :: getInstance()->getCommonImage('action_setting_false', 'png');
            }
        }

        return parent :: render_cell($column, $group);
    }

    public function get_actions($group)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ContentObjectGroupShareEditor'),
                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE_ENTITY,
                        Manager :: PARAM_TARGET_GROUPS => $group->get_id()),
                    $group->get_id(),
                    null),
                ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ContentObjectGroupShareDeleter'),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_REMOVE_ENTITY,
                        Manager :: PARAM_TARGET_GROUPS => $group->get_id()),
                    $group->get_id(),
                    null),
                ToolbarItem :: DISPLAY_ICON));
        return $toolbar->as_html();
    }
}
