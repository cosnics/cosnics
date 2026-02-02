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
class SubscribeComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(): Response
    {
        $groupIdentifier = $this->getRequest()->query->get(self::PARAM_GROUP_ID);

        if (!$this->getUser()->isPlatformAdministrator())
        {
            throw new NotAllowedException();
        }

        $userIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID);

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
                        self::PARAM_GROUP_ID => $groupIdentifier
                    ]
                ), $translator->trans('ViewerComponent', [], Manager::CONTEXT)
            )
        );

        $failures = 0;

        if (!empty($userIdentifiers))
        {
            if (!is_array($userIdentifiers))
            {
                $userIdentifiers = [$userIdentifiers];
            }

            $group = $groupService->findGroupByIdentifier($groupIdentifier);
            $containsDuplicates = false;

            foreach ($userIdentifiers as $user)
            {
                $user = $userService->findUserByIdentifier($user);

                $groupUserRelation = $groupMembershipService->getGroupUserRelationByGroupAndUser($group, $user);

                if (!$groupUserRelation instanceof GroupRelUser)
                {
                    try
                    {
                        $groupMembershipService->subscribeUserToGroup($group, $user);
                    }
                    catch (RuntimeException)
                    {
                        $failures ++;
                    }
                }
                else
                {
                    $containsDuplicates = true;
                }
            }

            if ($failures)
            {
                if (count($userIdentifiers) == 1)
                {
                    $message = 'SelectedUserNotAddedToGroup' . ($containsDuplicates ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedUsersNotAddedToGroup' . ($containsDuplicates ? 'Dupes' : '');
                }
            }
            elseif (count($userIdentifiers) == 1)
            {
                $message = 'SelectedUserAddedToGroup' . ($containsDuplicates ? 'Dupes' : '');
            }
            else
            {
                $message = 'SelectedUsersAddedToGroup' . ($containsDuplicates ? 'Dupes' : '');
            }

            return $this->redirectWithMessage(
                $translator->trans($message), (bool) $failures, [
                    Application::PARAM_CONTEXT => $this->getContext(),
                    Application::PARAM_ACTION => self::ACTION_VIEW,
                    self::PARAM_GROUP_ID => $groupIdentifier
                ]
            );
        }
        else
        {
            return new Response(
                $this->displayErrorPage(
                    htmlentities($translator->trans('NoObjectSelected', [], StringUtilities::LIBRARIES))
                )
            );
        }
    }

}
