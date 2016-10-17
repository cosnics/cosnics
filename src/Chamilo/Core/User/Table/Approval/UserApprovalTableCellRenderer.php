<?php
namespace Chamilo\Core\User\Table\Approval;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Hashing\Hashing;
use Chamilo\Libraries\Platform\Translation;

/**
 * Cell renderer for the user object browser table
 */
class UserApprovalTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $user)
    {
        switch ($column->get_name())
        {
            case User :: PROPERTY_PICTURE_URI :
                return $this->render_picture($user);
        }
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     *
     * @param $user The user for which the action links should be returned
     * @return string A HTML representation of the action links
     */
    public function get_actions($user)
    {
        $toolbar = new Toolbar();

        if ($this->get_user()->is_platform_admin())
        {
            $um = new Manager();
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Approve'),
                    Theme :: getInstance()->getCommonImagePath('Action/Activate'),
                    $um->get_approve_user_url($user),
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Deny'),
                    Theme :: getInstance()->getCommonImagePath('Action/Deinstall'),
                    $um->get_deny_user_url($user),
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }

    private function render_picture($user)
    {
        if ($user->has_picture())
        {
            $picture = $user->get_full_picture_path();
            $thumbnail_path = $this->get_thumbnail_path($picture);
            $thumbnail_url = Path :: getInstance()->getTemporaryPath(null, true) . basename($thumbnail_path);
            return '<span style="display:none;">1</span><img src="' . $thumbnail_url . '" alt="' .
                 htmlentities($user->get_fullname()) . '" border="0"/>';
        }
        else
        {
            return '<span style="display:none;">0</span>';
        }
    }

    private function get_thumbnail_path($image_path)
    {
        $thumbnail_path = Path :: getInstance()->getTemporaryPath(null, true) . Hashing :: hash($image_path) .
             basename($image_path);
        if (! is_file($thumbnail_path))
        {
            $thumbnail_creator = ImageManipulation :: factory($image_path);
            $thumbnail_creator->create_thumbnail(20);
            $thumbnail_creator->write_to_file($thumbnail_path);
        }
        return $thumbnail_path;
    }
}
