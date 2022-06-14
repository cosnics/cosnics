<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
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
class CreatorComponent extends Manager
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

        $group = new Group();
        $group->set_parent(Request::get(self::PARAM_GROUP_ID));
        $form = new GroupForm(
            GroupForm::TYPE_CREATE,
            $group,
            $this->get_url(array(self::PARAM_GROUP_ID => Request::get(self::PARAM_GROUP_ID))),
            $this->get_user());

        if ($form->validate())
        {
            $success = $form->create_group();

            if ($success)
            {
                $group = $form->get_group();
                $this->redirect(
                    Translation::get(
                        'ObjectCreated',
                        array('OBJECT' => Translation::get('Group')),
                        StringUtilities::LIBRARIES),
                    (false),
                    array(
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $group->get_id()));
            }
            else
            {
                $this->redirect(
                    Translation::get(
                        'ObjectNotCreated',
                        array('OBJECT' => Translation::get('Group')),
                        StringUtilities::LIBRARIES),
                    (true),
                    array(Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS));
            }
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
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
    }
}
