<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
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

            if ($failures) {
                if (count($userIdentifiers) == 1) {
                    $message = $translator->trans(
                        'UserNotDeleted', [], Manager::CONTEXT
                    );
                }
                else {
                    $message = $translator->trans(
                        'UsersNotDeleted', [], Manager::CONTEXT
                    );
                }
            }
            elseif (count($userIdentifiers) == 1) {
                $message = $translator->trans(
                    'UserDeleted', [], Manager::CONTEXT
                );
            }
            else {
                $message = $translator->trans(
                    'UsersDeleted', [], Manager::CONTEXT
                );
            }

            return $this->getRedirectResponseWithMessage(
                $message, ($failures > 0), [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => ActionEnum::BROWSE->value
                ]
            );
        }
        else {
            return new Response(
                $this->getErrorPageRenderer()->render(
                    $this, htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['%Object%' => $translator->trans('User', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    )
                ), $currentUser
                )
            );
        }
    }
}
