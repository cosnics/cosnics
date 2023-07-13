<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupMoveForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package group.lib.group_manager.component
 */
class MoverComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (!$this->get_user()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $group_id = $this->getRequest()->query->get(self::PARAM_GROUP_ID);
        $this->set_parameter(self::PARAM_GROUP_ID, $group_id);

        $group = $this->retrieve_group(intval($this->getRequest()->query->get(self::PARAM_GROUP_ID)));

        // TODO: only show groups you can actually move to (where you have create rights)
        $form = new GroupMoveForm($group, $this->get_url([self::PARAM_GROUP_ID => $group_id]), $this->get_user());

        if ($form->validate())
        {
            $success = $form->move_group();
            $parent = $form->get_new_parent();
            $message = $success ? Translation::get(
                'ObjectMoved', ['OBJECT' => Translation::get('Group')], StringUtilities::LIBRARIES
            ) : Translation::get(
                'ObjectNotMoved', ['OBJECT' => Translation::get('Group')], StringUtilities::LIBRARIES
            );
            $this->redirectWithMessage(
                $message, !$success || false,
                [Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS, self::PARAM_GROUP_ID => $parent]
            );
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

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                Translation::get('BrowserComponent')
            )
        );
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $this->getRequest()->query->get(self::PARAM_GROUP_ID)
                    ]
                ), Translation::get('ViewerComponent')
            )
        );
    }
}
