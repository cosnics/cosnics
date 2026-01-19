<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\UserInterface\Form\GroupForm;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 */
class EditorComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run(): Response
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $groupIdentifier = $this->getRequest()->query->get(self::PARAM_GROUP_ID);

        if ($groupIdentifier)
        {
            $group = $this->getGroupService()->findGroupByIdentifier($groupIdentifier);

            if (!$this->getUser()->isPlatformAdmin())
            {
                throw new NotAllowedException();
            }

            $form = new GroupForm(
                GroupForm::TYPE_EDIT, $group, $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => self::ACTION_EDIT_GROUP,
                    self::PARAM_GROUP_ID => $groupIdentifier
                ]
            )
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

                return $this->redirectWithMessage(
                    $message, !$success, [
                        Application::PARAM_CONTEXT => $this->getContext(),
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $group->getId()
                    ]
                );
            }
            else
            {
                $html = [];

                $html[] = $this->renderHeader();
                $html[] = $form->render();
                $html[] = $this->renderFooter();

                return new Response(implode(PHP_EOL, $html));
            }
        }
        else
        {
            return new Response(
                $this->display_error_page(
                    htmlentities($translator->trans('NoObjectSelected', [], StringUtilities::LIBRARIES))
                )
            );
        }
    }
}
