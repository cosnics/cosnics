<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\UserInterface\Form\GroupMoveForm;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MoveComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectNotExistException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Throwable
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $groupIdentifier = $this->getRequest()->query->get(self::PARAM_GROUP_ID);

        $group = $this->getGroupService()->findGroupByIdentifier($this->getRequest()->query->get(self::PARAM_GROUP_ID));

        $form = new GroupMoveForm(
            $group, $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => ActionEnum::MOVE->value,
                self::PARAM_GROUP_ID => $groupIdentifier
            ]
        )
        );

        if ($form->validate()) {
            $success = $form->moveGroup();
            $parent = $form->getNewParent();
            $message = $translator->trans(
                $success ? 'ObjectMoved' : 'ObjectNotMoved', ['%Object%' => $translator->trans('Group')],
                StringUtilities::LIBRARIES
            );

            return $this->getRedirectResponseWithMessage(
                $message, !$success, [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => ActionEnum::BROWSE->value,
                    self::PARAM_GROUP_ID => $parent
                ]
            );
        }
        else {
            $html = [];

            $html[] = $this->renderHeader($currentUser);
            $html[] = $translator->trans('Group') . ': ' . $group->getName();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
    }
}
