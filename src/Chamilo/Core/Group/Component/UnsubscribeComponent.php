<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
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

        $groupUserRelationIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_RELATION_ID);

        $groupMembershipService = $this->getGroupMembershipService();
        $userService = $this->getUserService();
        $groupService = $this->getGroupService();
        $translator = $this->getTranslator();

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb($translator->trans('ViewerComponent', [], Manager::CONTEXT),
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => ActionEnum::BROWSE->value,
                        self::PARAM_GROUP_ID => $this->getRequest()->query->get(self::PARAM_GROUP_ID)
                    ]
                ))
        );

        $failures = 0;

        if (!empty($groupUserRelationIdentifiers)) {
            if (!is_array($groupUserRelationIdentifiers)) {
                $groupUserRelationIdentifiers = [$groupUserRelationIdentifiers];
            }

            foreach ($groupUserRelationIdentifiers as $groupUserRelationIdentifier) {
                $groupUserRelation =
                    $groupMembershipService->findGroupRelUserByIdentifier($groupUserRelationIdentifier);

                if (!$groupUserRelation instanceof GroupRelUser) {
                    continue;
                }

                $group = $groupService->findGroupByIdentifier($groupUserRelation->getGroupId());
                $userToUnsubscribe = $userService->findUserByIdentifier($groupUserRelation->getUserId());

                try {
                    $groupMembershipService->unsubscribeUserFromGroup($group, $userToUnsubscribe, $currentUser);
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

            $this->getAlertsManager()->addAlert(
                new Alert(
                    $translator->trans($message, [], Manager::CONTEXT),
                    $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                self::PARAM_GROUP_ID => $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_ID)
            ]));
        }
        else {
            throw new NoSuchParameterException(self::PARAM_RELATION_ID);
        }
    }
}
