<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package Chamilo\Core\User\Component
 */
class DeleteComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $userIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID);

        if (!is_array($userIdentifiers)) {
            $userIdentifiers = [$userIdentifiers];
        }

        if (count($userIdentifiers) > 0) {
            $failures = 0;

            foreach ($userIdentifiers as $userIdentifier) {
                $userToDelete = $this->userService->retrieveUserByIdentifier($userIdentifier);

                try {
                    $this->userService->deleteUser($userToDelete, $currentUser);
                }
                catch (Throwable) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($userIdentifiers) == 1) {
                    $message = $this->translator->trans(
                        'UserNotDeleted', [], Manager::CONTEXT
                    );
                }
                else {
                    $message = $this->translator->trans(
                        'UsersNotDeleted', [], Manager::CONTEXT
                    );
                }
            }
            elseif (count($userIdentifiers) == 1) {
                $message = $this->translator->trans(
                    'UserDeleted', [], Manager::CONTEXT
                );
            }
            else {
                $message = $this->translator->trans(
                    'UsersDeleted', [], Manager::CONTEXT
                );
            }

            $this->alertsManager->addAlert(
                new Alert(
                    $message, $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
            ]));
        }
        else {
            throw new NoSuchParameterException(self::PARAM_USER_ID);
        }
    }
}
