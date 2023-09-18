<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupMoveForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Group\Component
 */
class MoverComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $group_id = $this->getRequest()->query->get(self::PARAM_GROUP_ID);
        $this->set_parameter(self::PARAM_GROUP_ID, $group_id);

        $group = $this->retrieve_group(intval($this->getRequest()->query->get(self::PARAM_GROUP_ID)));

        // TODO: only show groups you can actually move to (where you have create rights)
        $form = new GroupMoveForm($group, $this->get_url([self::PARAM_GROUP_ID => $group_id]), $this->getUser());

        if ($form->validate())
        {
            $success = $form->move_group();
            $parent = $form->get_new_parent();
            $message = $translator->trans(
                $success ? 'ObjectMoved' : 'ObjectNotMoved', ['OBJECT' => $translator->trans('Group')],
                StringUtilities::LIBRARIES
            );
            $this->redirectWithMessage(
                $message, !$success || false,
                [Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS, self::PARAM_GROUP_ID => $parent]
            );
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $translator->trans('Group') . ': ' . $group->get_name();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $translator = $this->getTranslator();

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                $translator->trans('BrowserComponent', [], Manager::CONTEXT)
            )
        );
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $this->getRequest()->query->get(self::PARAM_GROUP_ID)
                    ]
                ), $translator->trans('ViewerComponent', [], Manager::CONTEXT)
            )
        );
    }
}
