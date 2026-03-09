<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleteComponent extends Manager
{
    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function run(?User $currentUser = null): Response
    {
        $translator = $this->getTranslator();
        $groupService = $this->getGroupService();
        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_ID);

        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => ActionEnum::BROWSE->value,
                        self::PARAM_GROUP_ID => $this->getRequest()->query->get(self::PARAM_GROUP_ID)
                    ]
                ), $translator->trans('ViewerComponent', [], Manager::CONTEXT)
            )
        );

        $failures = 0;

        if (!empty($ids)) {
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            foreach ($ids as $id) {
                $group = $groupService->findGroupByIdentifier($id);

                if (!$groupService->deleteGroup($group)) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($ids) == 1) {
                    $message = $translator->trans(
                        'ObjectNotDeleted', ['%Object%' => $translator->trans('SelectedGroup', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
                else {
                    $message = $translator->trans(
                        'ObjectsNotDeleted', ['%Object%' => $translator->trans('SelectedGroups', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
            }
            elseif (count($ids) == 1) {
                $message = $translator->trans(
                    'ObjectDeleted', ['%Object%' => $translator->trans('SelectedGroup', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }
            else {
                $message = $translator->trans(
                    'ObjectsDeleted', ['%Object%' => $translator->trans('SelectedGroups', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }

            $this->getNotificationMessageManager()->addMessage(
                new NotificationMessage(
                    $message, $failures ? NotificationMessage::TYPE_DANGER : NotificationMessage::TYPE_SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => ActionEnum::BROWSE->value
            ]));
        }
        else {
            return new Response(
                $this->getErrorPageRenderer()->render(
                    $this, htmlentities($translator->trans('NoObjectsSelected', [], StringUtilities::LIBRARIES)),
                    $currentUser
                )
            );
        }
    }
}
