<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: quota_viewer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
/**
 * User manager component which displays the quota to the user.
 * This component displays two progress-bars. The first one
 * displays the used disk space and the second one the number of learning objects in the users user.
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class QuotaViewerComponent extends Manager
{

    private $selected_user;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $selected_user_id = Request :: get(self :: PARAM_USER_USER_ID);
        if (! $selected_user_id)
        {
            $this->selected_user = $this->get_user();
        }
        else
        {
            $this->selected_user = \Chamilo\Core\User\Storage\DataManager :: retrieve(
                User :: class_name(),
                (int) $selected_user_id);
        }
        $this->calculator = new Calculator($this->selected_user);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->display_action_bar();

        $html[] = '<h3>' . htmlentities(Translation :: get('UsedDiskSpace')) . '</h3>';
        $html[] = Calculator :: get_bar(
            $this->calculator->get_user_disk_quota_percentage(),
            Filesystem :: format_file_size($this->calculator->get_used_user_disk_quota()) . ' / ' . Filesystem :: format_file_size(
                $this->calculator->get_maximum_user_disk_quota()));
        $html[] = '<div style="clear: both;">&nbsp;</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    private function display_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('EditUser'),
                Theme :: getInstance()->getCommonImagePath('action_edit'),
                $this->get_url(
                    array(
                        Application :: PARAM_ACTION => self :: ACTION_UPDATE_USER,
                        self :: PARAM_USER_USER_ID => $this->selected_user->get_id())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar->as_html();
    }
}
