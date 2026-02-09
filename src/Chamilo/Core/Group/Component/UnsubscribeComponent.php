<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use RuntimeException;
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
     */
    public function run(): Response
    {
        $user = $this->getUser();

        if (!$user->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $groupUserRelationIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_RELATION_ID);

        $groupMembershipService = $this->getGroupMembershipService();
        $userService = $this->getUserService();
        $groupService = $this->getGroupService();
        $translator = $this->getTranslator();

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => self::ACTION_VIEW,
                        self::PARAM_GROUP_ID => $this->getRequest()->query->get(self::PARAM_GROUP_ID)
                    ]
                ), $translator->trans('ViewerComponent', [], Manager::CONTEXT)
            )
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
                $user = $userService->findUserByIdentifier($groupUserRelation->getUserId());

                try {
                    $groupMembershipService->unsubscribeUserFromGroup($group, $user);
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

            return $this->redirectWithMessage(
                $translator->trans($message, [], Manager::CONTEXT), (bool) $failures, [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => self::ACTION_VIEW,
                    self::PARAM_GROUP_ID => $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_ID)
                ]
            );
        }
        else {
            return new Response(
                $this->displayErrorPage(
                    htmlentities($translator->trans('NoObjectSelected', [], StringUtilities::LIBRARIES))
                )
            );
        }
    }
}
