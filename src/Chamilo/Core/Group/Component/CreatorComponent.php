<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Form\GroupForm;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 */
class CreatorComponent extends Manager
{

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Throwable
     */
    public function run(): Response
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
            GroupForm::TYPE_CREATE, $group, $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_CREATOR,
                self::PARAM_GROUP_ID => $parentGroupIdentifier
            ]
        )
        );

        if ($form->validate())
        {
            $success = $form->create_group();

            if ($success)
            {
                $group = $form->get_group();

                return $this->redirectWithMessage(
                    $translator->trans(
                        'ObjectCreated', ['OBJECT' => $translator->trans('Group', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    ), (false), [
                        Application::PARAM_CONTEXT => $this->getContext(),
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $group->getId()
                    ]
                );
            }
            else
            {
                return $this->redirectWithMessage(
                    $translator->trans(
                        'ObjectNotCreated', ['OBJECT' => $translator->trans('Group', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    ), (true), [
                        Application::PARAM_CONTEXT => $this->getContext(),
                        Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS,
                        self::PARAM_GROUP_ID => $parentGroupIdentifier
                    ]
                );
            }
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
}
