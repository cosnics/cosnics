<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Utilities\StringUtilities;
use RuntimeException;

/**
 * @package Chamilo\Core\Group\Component
 */
class UnsubscriberComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $user = $this->getUser();

        if (!$user->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $groupUserRelationIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_REL_USER_ID);
        $this->set_parameter(self::PARAM_GROUP_ID, $groupUserRelationIdentifiers);

        $groupMembershipService = $this->getGroupMembershipService();
        $userService = $this->getUserService();
        $groupService = $this->getGroupService();
        $translator = $this->getTranslator();

        $failures = 0;

        if (!empty($groupUserRelationIdentifiers))
        {
            if (!is_array($groupUserRelationIdentifiers))
            {
                $groupUserRelationIdentifiers = [$groupUserRelationIdentifiers];
            }

            foreach ($groupUserRelationIdentifiers as $groupUserRelationIdentifier)
            {
                $groupUserRelation =
                    $groupMembershipService->findGroupRelUserByIdentifier($groupUserRelationIdentifier);

                if (!$groupUserRelation instanceof GroupRelUser)
                {
                    continue;
                }

                $group = $groupService->findGroupByIdentifier($groupUserRelation->get_group_id());
                $user = $userService->findUserByIdentifier($groupUserRelation->get_user_id());

                try
                {
                    $groupMembershipService->unsubscribeUserFromGroup($group, $user);
                }
                catch (RuntimeException)
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($groupUserRelationIdentifiers) == 1)
                {
                    $message = 'SelectedGroupRelUserNotDeleted';
                }
                else
                {
                    $message = 'SelectedGroupRelUsersNotDeleted';
                }
            }
            elseif (count($groupUserRelationIdentifiers) == 1)
            {
                $message = 'SelectedGroupRelUserDeleted';
            }
            else
            {
                $message = 'SelectedGroupRelUsersDeleted';
            }

            $this->redirectWithMessage(
                $translator->trans($message, [], Manager::CONTEXT), (bool) $failures, [
                    Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                    self::PARAM_GROUP_ID => $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_ID)
                ]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities($translator->trans('NoObjectSelected', [], StringUtilities::LIBRARIES))
            );
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $translator = $this->getTranslator();

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                $translator->trans('BrowserComponent', [], Manager::CONTEXT)
            )
        );

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $this->getRequest()->query->get(self::PARAM_GROUP_ID)
                    ]
                ), $translator->trans('ViewerComponent', [], Manager::CONTEXT)
            )
        );
    }
}
