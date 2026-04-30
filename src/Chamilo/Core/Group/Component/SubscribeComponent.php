<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscribeComponent extends Manager
{
    public const string PARAM_USER_ID = 'user_id';

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     */
    public function run(?User $currentUser = null): Response
    {
        $groupIdentifier = $this->getRequest()->query->get(DataClass::PROPERTY_ID);

        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $userIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID);

        $this->breadcrumbTrail->add(
            new Breadcrumb($this->translator->trans('ViewerComponent', [], Manager::CONTEXT),
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => ActionEnum::BROWSE->value,
                        DataClass::PROPERTY_ID => $groupIdentifier
                    ]
                ))
        );

        $failures = 0;

        if (!empty($userIdentifiers)) {
            if (!is_array($userIdentifiers)) {
                $userIdentifiers = [$userIdentifiers];
            }

            $group = $this->groupService->findGroupByIdentifier($groupIdentifier);
            $containsDuplicates = false;

            foreach ($userIdentifiers as $userIdentifier) {
                $userToSubscribe = $this->userService->findUserByIdentifier($userIdentifier);

                $groupUserRelation =
                    $this->groupMembershipService->retrieveGroupMembershipByGroupAndUser($group, $userToSubscribe);

                if (!$groupUserRelation instanceof GroupRelUser) {
                    try {
                        $this->groupMembershipService->subscribeUserToGroup($group, $userToSubscribe, $currentUser);
                    }
                    catch (RuntimeException) {
                        $failures ++;
                    }
                }
                else {
                    $containsDuplicates = true;
                }
            }

            if ($failures) {
                if (count($userIdentifiers) == 1) {
                    $message = 'SelectedUserNotAddedToGroup' . ($containsDuplicates ? 'Dupes' : '');
                }
                else {
                    $message = 'SelectedUsersNotAddedToGroup' . ($containsDuplicates ? 'Dupes' : '');
                }
            }
            elseif (count($userIdentifiers) == 1) {
                $message = 'SelectedUserAddedToGroup' . ($containsDuplicates ? 'Dupes' : '');
            }
            else {
                $message = 'SelectedUsersAddedToGroup' . ($containsDuplicates ? 'Dupes' : '');
            }

            $this->alertsManager->addAlert(
                new Alert(
                    $this->translator->trans($message, [], Manager::CONTEXT),
                    $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

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
