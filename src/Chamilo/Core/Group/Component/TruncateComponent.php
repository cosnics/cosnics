<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TruncateComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $groupIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_ID);

        $groupMembershipService = $this->getGroupMembershipService();
        $groupService = $this->getGroupService();
        $translator = $this->getTranslator();

        $failures = 0;

        if (!empty($groupIdentifiers)) {
            if (!is_array($groupIdentifiers)) {
                $groupIdentifiers = [$groupIdentifiers];
            }

            foreach ($groupIdentifiers as $groupIdentifier) {
                $group = $groupService->findGroupByIdentifier($groupIdentifier);

                try {
                    $groupMembershipService->emptyGroup($group);
                }
                catch (RuntimeException) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($groupIdentifiers) == 1) {
                    $message = 'SelectedGroupNotEmptied';
                }
                else {
                    $message = 'SelectedGroupsNotEmptied';
                }
            }
            elseif (count($groupIdentifiers) == 1) {
                $message = 'SelectedGroupEmptied';
            }
            else {
                $message = 'SelectedGroupsEmptied';
            }

            $this->getNotificationMessageManager()->addMessage(
                new NotificationMessage(
                    $translator->trans($message, [], Manager::CONTEXT),
                    $failures ? NotificationMessage::TYPE_DANGER : NotificationMessage::TYPE_SUCCESS
                )
            );

            if (count($groupIdentifiers) == 1) {
                $redirectUrl = $this->getUrlGenerator()->fromParameters([
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => ActionEnum::BROWSE->value,
                        self::PARAM_GROUP_ID => $groupIdentifiers[0]
                    ]);
            }
            else {
                $redirectUrl = $this->getUrlGenerator()->fromParameters([
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => ActionEnum::BROWSE->value
                    ]);
            }

            return new RedirectResponse($redirectUrl);
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
