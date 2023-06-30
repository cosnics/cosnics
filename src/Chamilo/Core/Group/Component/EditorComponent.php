<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Group\Component
 */
class EditorComponent extends Manager
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

        $groupIdentifier = $this->getRequest()->query->get(self::PARAM_GROUP_ID);

        if ($groupIdentifier)
        {
            $group = $this->retrieve_group($groupIdentifier);

            if (!$this->getUser()->isPlatformAdmin())
            {
                throw new NotAllowedException();
            }

            $form = new GroupForm(
                GroupForm::TYPE_EDIT, $group, $this->get_url([self::PARAM_GROUP_ID => $groupIdentifier]),
                $this->getUser()
            );

            if ($form->validate())
            {
                $success = $form->update_group();
                $group = $form->get_group();
                $message = $success ? $translator->trans(
                    'ObjectUpdated', ['OBJECT' => $translator->trans('Group', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                ) : $translator->trans(
                    'ObjectNotUpdated', ['OBJECT' => $translator->trans('Group', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );

                $this->redirectWithMessage(
                    $message, !$success, [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $group->get_id()
                    ]
                );
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
        else
        {
            return $this->display_error_page(
                htmlentities($translator->trans('NoObjectSelected', [], StringUtilities::LIBRARIES))
            );
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $translator = $this->getTranslator();

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                $translator->trans('BrowserComponent')
            )
        );

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $this->getRequest()->query->get(self::PARAM_GROUP_ID)
                    ]
                ), $translator->trans('ViewerComponent')
            )
        );
    }
}
