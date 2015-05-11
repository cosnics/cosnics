<?php
namespace Chamilo\Core\Repository\Share\Table\User;

use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Share\Manager;
use Chamilo\Core\Repository\Share\Table\ShareRightColumn;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * Cell renderer for the content object user share rights browser
 *
 * @author Pieterjan Broekaert
 */
class UserRightsTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     *
     * @param StaticTableColumn $column
     * @param <type> $registration
     * @return cell content
     */
    public function render_cell($column, $user)
    {
        if ($column instanceof ShareRightColumn)
        {
            $location = RepositoryRights :: get_instance()->get_location_by_identifier_from_users_subtree(
                RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                array_pop($this->get_component()->get_content_objects())->get_id(),
                array_pop($this->get_component()->get_content_objects())->get_owner_id());
            $rights = RepositoryRights :: get_instance()->get_granted_rights_for_rights_entity_item(
                \Chamilo\Core\Repository\Manager :: context(),
                UserEntity :: ENTITY_TYPE,
                $user->get_id(),
                $location);

            $copy_right = array_search(RepositoryRights :: COPY_RIGHT, $rights);

            if ($copy_right)
            {
                array_splice($rights, $copy_right, 1);
                if ($column->get_right_id() == RepositoryRights :: COPY_RIGHT)
                {
                    return Theme :: getInstance()->getCommonImage('Action/SettingTrue', 'png');
                }
            }

            if ($column->get_right_id() <= max($rights))
            {
                return Theme :: getInstance()->getCommonImage('Action/SettingTrue', 'png');
            }
            else
            {
                return Theme :: getInstance()->getCommonImage('Action/SettingFalse', 'png');
            }
        }

        return parent :: render_cell($column, $user);
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableCellRendererActionsColumnSupport::get_actions()
     */
    public function get_actions($user)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ContentObjectUserShareEditor'),
                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE_ENTITY,
                        Manager :: PARAM_TARGET_USERS => $user->get_id()),
                    $user->get_id(),
                    null),
                ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ContentObjectUserShareDeleter'),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_REMOVE_ENTITY,
                        Manager :: PARAM_TARGET_USERS => $user->get_id()),
                    $user->get_id(),
                    null),
                ToolbarItem :: DISPLAY_ICON));
        return $toolbar->as_html();
    }
}
