<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActiveComponent extends Manager
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

        $userService = $this->getUserService();
        $translator = $this->getTranslator();

        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID);

        $active = $this->getState();

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        if (count($ids) > 0) {
            $failures = 0;

            foreach ($ids as $id) {
                if (!$currentUser->isPlatformAdministrator()) {
                    $failures ++;
                    continue;
                }

                $userToActivate = $userService->findUserByIdentifier($id);
                $userToActivate->setActive($active);

                if (!$userService->updateUser($userToActivate)) {
                    $failures ++;
                }
            }

            if ($active == 0) {
                $message = $this->getResult(
                    $failures, count($ids), 'UserNotDeactivated', 'UsersNotDeactivated', 'UserDeactivated',
                    'UsersDeactivated'
                );
            }
            else {
                $message = $this->getResult(
                    $failures, count($ids), 'UserNotActivated', 'UsersNotActivated', 'UserActivated', 'UsersActivated'
                );
            }

            return $this->redirectWithMessage(
                $message, ($failures > 0), [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => self::ACTION_BROWSE
                ]
            );
        }
        else {
            return new Response(
                $this->displayErrorPage(
                    htmlentities(
                        $translator->trans(
                            'NoObjectSelected', ['%Object%' => $translator->trans('User', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        )
                    )
                )
            );
        }
    }

    protected function getState(): bool
    {
        return (bool) $this->getRequest()->getFromQueryOrRequest(Manager::PARAM_ACTIVE, 0);
    }
}
