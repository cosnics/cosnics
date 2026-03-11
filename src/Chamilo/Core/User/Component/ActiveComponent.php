<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

            if ($failures) {
                if (count($ids) == 1) {
                    $message = $active ? 'UserNotActivated' : 'UserNotDeactivated';
                }
                else {
                    $message = $active ? 'UsersNotActivated' : 'UsersNotDeactivated';
                }
            }
            elseif (count($ids) == 1) {
                $message = $active ? 'UserActivated' : 'UserDeactivated';
            }
            else {
                $message = $active ? 'UsersActivated' : 'UsersDeactivated';
            }

            $this->getNotificationMessageManager()->addAlert(
                new Alert(
                    $translator->trans($message, [], \Chamilo\Core\Group\Manager::CONTEXT),
                    $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
            ]));
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

    protected function getState(): bool
    {
        return (bool) $this->getRequest()->getFromQueryOrRequest(Manager::PARAM_ACTIVE, 0);
    }
}
