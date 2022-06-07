<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupMoveForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package group.lib.group_manager.component
 */
class MoverComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $group_id = Request::get(self::PARAM_GROUP_ID);
        $this->set_parameter(self::PARAM_GROUP_ID, $group_id);

        $group = $this->retrieve_group(intval(Request::get(self::PARAM_GROUP_ID)));

        // TODO: only show groups you can actually move to (where you have create rights)
        $form = new GroupMoveForm($group, $this->get_url(array(self::PARAM_GROUP_ID => $group_id)), $this->get_user());

        if ($form->validate())
        {
            $success = $form->move_group();
            $parent = $form->get_new_parent();
            $message = $success ? Translation::get(
                'ObjectMoved',
                array('OBJECT' => Translation::get('Group')),
                StringUtilities::LIBRARIES) : Translation::get(
                'ObjectNotMoved',
                array('OBJECT' => Translation::get('Group')),
                StringUtilities::LIBRARIES);
            $this->redirect(
                $message, !$success || false,
                array(Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS, self::PARAM_GROUP_ID => $parent));
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = Translation::get('Group') . ': ' . $group->get_name();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS)),
                Translation::get('BrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => Request::get(self::PARAM_GROUP_ID))),
                Translation::get('ViewerComponent')));
        $breadcrumbtrail->add_help('group general');
    }
}
