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
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActiveComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        $identifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID);

        $active = $this->getState();

        if (!is_array($identifiers)) {
            $identifiers = [$identifiers];
        }

        if (count($identifiers) > 0) {
            $failures = 0;

            foreach ($identifiers as $identifier) {
                if (!$currentUser->isPlatformAdministrator()) {
                    $failures ++;
                    continue;
                }

                $userToActivate = $this->userService->retrieveUserByIdentifier($identifier);
                $userToActivate->setActive($active);

                try {
                    $this->userService->updateUser($userToActivate, $currentUser);
                }
                catch (Throwable) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($identifiers) == 1) {
                    $message = $active ? 'UserNotActivated' : 'UserNotDeactivated';
                }
                else {
                    $message = $active ? 'UsersNotActivated' : 'UsersNotDeactivated';
                }
            }
            elseif (count($identifiers) == 1) {
                $message = $active ? 'UserActivated' : 'UserDeactivated';
            }
            else {
                $message = $active ? 'UsersActivated' : 'UsersDeactivated';
            }

            $this->alertsManager->addAlert(
                new Alert(
                    $this->translator->trans($message, [], \Chamilo\Core\Group\Manager::CONTEXT),
                    $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
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

    protected function getState(): bool
    {
        return (bool) $this->getRequest()->getFromQueryOrRequest(Manager::PARAM_ACTIVE, 0);
    }
}
