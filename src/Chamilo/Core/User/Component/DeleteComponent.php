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
 */
class DeleteComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $userIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID);

        $translator = $this->getTranslator();
        $userService = $this->getUserService();

        if (!is_array($userIdentifiers)) {
            $userIdentifiers = [$userIdentifiers];
        }

        if (count($userIdentifiers) > 0) {
            $failures = 0;

            foreach ($userIdentifiers as $userIdentifier) {
                $userToDelete = $userService->findUserByIdentifier($userIdentifier);

                if (!$userService->deleteUser($userToDelete)) {
                    $failures ++;
                }
            }

            $message = $this->getResult(
                $failures, count($userIdentifiers), 'UserNotDeleted', 'UsersNotDeleted', 'UserDeleted', 'UsersDeleted'
            );

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
}
