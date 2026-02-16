<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Form\GroupForm;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreateComponent extends Manager
{
    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectNotExistException
     * @throws \Throwable
     */
    public function run(): Response
    {
        if (!$this->getUser()->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $parentGroupIdentifier = $this->getRequest()->query->get(self::PARAM_GROUP_ID, DataClass::EMPTY_UUID);

        $group = new Group();
        $group->setParentId($parentGroupIdentifier);

        $form = new GroupForm(
            GroupForm::TYPE_CREATE, $group, $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_CREATE,
                self::PARAM_GROUP_ID => $parentGroupIdentifier
            ]
        )
        );

        if ($form->validate()) {
            $success = $form->createGroupFromForm();

            if ($success) {
                $group = $form->getGroup();

                return $this->redirectWithMessage(
                    $translator->trans(
                        'ObjectCreated', ['%Object%' => $translator->trans('Group', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    ), (false), [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => self::ACTION_VIEW,
                        self::PARAM_GROUP_ID => $group->getId()
                    ]
                );
            }
            else {
                return $this->redirectWithMessage(
                    $translator->trans(
                        'ObjectNotCreated', ['%Object%' => $translator->trans('Group', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    ), (true), [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => self::ACTION_BROWSE,
                        self::PARAM_GROUP_ID => $parentGroupIdentifier
                    ]
                );
            }
        }
        else {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
    }
}
