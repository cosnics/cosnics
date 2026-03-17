<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Form\GroupForm;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @throws \Throwable
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
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
                self::PARAM_ACTION => ActionEnum::CREATE->value,
                self::PARAM_GROUP_ID => $parentGroupIdentifier
            ]
        )
        );

        if ($form->validate()) {
            dump($this->getRequest());
            exit;
            $success = $form->createGroupFromForm();

            if ($success) {
                $group = $form->getGroup();

                $this->getAlertsManager()->addAlert(
                    new Alert(
                        $translator->trans(
                            'ObjectCreated', ['%Object%' => $translator->trans('Group', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        )
                    )
                );

                return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                    self::PARAM_GROUP_ID => $group->getId()
                ]));
            }
            else {
                $this->getAlertsManager()->addAlert(
                    new Alert(
                        $translator->trans(
                            'ObjectNotCreated', ['%Object%' => $translator->trans('Group', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        ), AlertEnum::DANGER
                    )
                );

                return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                    self::PARAM_GROUP_ID => $parentGroupIdentifier
                ]));
            }
        }
        else {
            $html = [];

            $html[] = $this->renderHeader($currentUser);
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
    }
}
