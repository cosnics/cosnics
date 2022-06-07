<?php
namespace Chamilo\Core\Group\Table\SubscribeUser;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package groups.lib.group_manager.component.subscribe_user_browser
 */

/**
 * Cell rendere for the learning object browser table
 */
class SubscribeUserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    // Inherited
    /**
     * Gets the action links to display
     *
     * @param User $user The user for which the action links should be returned
     *
     * @return string A HTML representation of the action links
     */
    public function get_actions($user)
    {
        $group = $this->get_component()->get_group();

        $toolbar = new Toolbar();
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Subscribe'), new FontAwesomeGlyph('plus-circle'),
                $this->get_component()->get_group_rel_user_subscribing_url($group, $user), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->as_html();
    }

    public function render_cell($column, $user)
    {

        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case User::PROPERTY_STATUS :

                if ($user->get_status() == 1)
                {
                    return Translation::get('CourseAdmin', null, Manager::context());
                }
                else
                {
                    return Translation::get('Student', null, Manager::context());
                }
            case User::PROPERTY_PLATFORMADMIN :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation::get('ConfirmTrue', null, StringUtilities::LIBRARIES);
                }
                else
                {
                    return Translation::get('ConfirmFalse', null, StringUtilities::LIBRARIES);
                }
            case User::PROPERTY_EMAIL :
                return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a>';
        }

        return parent::render_cell($column, $user);
    }
}
