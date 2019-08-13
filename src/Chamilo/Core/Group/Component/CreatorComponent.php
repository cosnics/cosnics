<?php

namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Utilities\Utilities;

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
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $group = new Group();
        $group->set_parent_id($this->getRequest()->getFromUrl(self::PARAM_GROUP_ID));
        $form = new GroupForm(
            GroupForm::TYPE_CREATE,
            $group,
            $this->get_url(array(self::PARAM_GROUP_ID => $this->getRequest()->getFromUrl(self::PARAM_GROUP_ID))),
            $this->getUser(),
            $this->getGroupService(),
            $this->getExceptionLogger(),
            $this->getTranslator()
        );

        if ($form->validate())
        {
            $success = $form->create_group();

            if ($success)
            {
                $group = $form->get_group();
                $this->redirect(
                    $this->getTranslator()->trans(
                        'ObjectCreated',
                        array('{OBJECT}' => $this->getTranslator()->trans('Group', [], Manager::context())),
                        Utilities::COMMON_LIBRARIES
                    ),
                    (false),
                    array(
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $group->getId()
                    )
                );
            }
            else
            {
                $this->redirect(
                    $this->getTranslator()->trans(
                        'ObjectNotCreated',
                        array('{OBJECT}' => $this->getTranslator()->trans('Group', [], Manager::context())),
                        Utilities::COMMON_LIBRARIES
                    ),
                    (true),
                    array(Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS)
                );
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        return null;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS)),
                $this->getTranslator()->trans('BrowserComponent', [], Manager::context())
            )
        );
        $breadcrumbtrail->add_help('group general');
    }
}
