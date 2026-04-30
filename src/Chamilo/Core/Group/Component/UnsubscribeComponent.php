<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UnsubscribeComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $groupUserRelationIdentifiers = $this->getRequest()->getFromRequestOrQuery(DataClass::PROPERTY_ID);

        $failures = 0;

        if (!empty($groupUserRelationIdentifiers)) {
            if (!is_array($groupUserRelationIdentifiers)) {
                $groupUserRelationIdentifiers = [$groupUserRelationIdentifiers];
            }

            foreach ($groupUserRelationIdentifiers as $groupUserRelationIdentifier) {
                $groupUserRelation =
                    $this->groupMembershipService->retrieveGroupMembershipByIdentifier($groupUserRelationIdentifier);

                if (!$groupUserRelation instanceof GroupRelUser) {
                    continue;
                }

                $group = $this->groupService->findGroupByIdentifier($groupUserRelation->getGroupId());
                $userToUnsubscribe = $this->userService->findUserByIdentifier($groupUserRelation->getUserId());

                try {
                    $this->groupMembershipService->deleteGroupMembershipByGroupAndUser($group, $userToUnsubscribe, $currentUser);
                }
                catch (RuntimeException) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($groupUserRelationIdentifiers) == 1) {
                    $message = 'SelectedGroupRelUserNotDeleted';
                }
                else {
                    $message = 'SelectedGroupRelUsersNotDeleted';
                }
            }
            elseif (count($groupUserRelationIdentifiers) == 1) {
                $message = 'SelectedGroupRelUserDeleted';
            }
            else {
                $message = 'SelectedGroupRelUsersDeleted';
            }

            $this->alertsManager->addAlert(
                new Alert(
                    $this->translator->trans($message, [], Manager::CONTEXT),
                    $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            $groupIdentifier =
                isset($group) && $group instanceof Group ? $group->getId() : $this->getRootGroup()->getId();

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                DataClass::PROPERTY_ID => $groupIdentifier
            ]));
        }
        else {
            throw new NoSuchParameterException(DataClass::PROPERTY_ID);
        }
    }
}
