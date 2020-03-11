<?php
namespace Chamilo\Core\User\Table\Approval;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Cell renderer for the user object browser table
 */
class UserApprovalTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Gets the action links to display
     *
     * @param User $user The user for which the action links should be returned
     *
     * @return string A HTML representation of the action links
     */
    public function get_actions($user)
    {
        $toolbar = new Toolbar();

        if ($user->is_platform_admin())
        {
            $um = $this->get_table()->get_component();
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Approve'), new FontAwesomeGlyph('check-circle'), $um->get_approve_user_url($user),
                    ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    new FontAwesomeGlyph('times-circle'), $um->get_deny_user_url($user), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
