<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Group\Component
 */
class CreatorComponent extends Manager
{

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $parentGroupIdentifier = $this->getRequest()->query->get(self::PARAM_GROUP_ID, '0');

        $group = new Group();
        $group->setParentId($parentGroupIdentifier);

        $form = new GroupForm(
            GroupForm::TYPE_CREATE, $group, $this->get_url([self::PARAM_GROUP_ID => $parentGroupIdentifier]),
            $this->getUser()
        );

        if ($form->validate())
        {
            $success = $form->create_group();

            if ($success)
            {
                $group = $form->get_group();
                $this->redirectWithMessage(
                    $translator->trans(
                        'ObjectCreated', ['OBJECT' => $translator->trans('Group', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    ), (false), [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $group->get_id()
                    ]
                );
            }
            else
            {
                $this->redirectWithMessage(
                    $translator->trans(
                        'ObjectNotCreated', ['OBJECT' => $translator->trans('Group', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    ), (true), [Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]
                );
            }
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                $this->getTranslator()->trans('BrowserComponent', [], Manager::CONTEXT)
            )
        );
    }
}
