<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TruncateComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(): Response
    {
        if (!$this->getUser()->isPlatformAdministrator())
        {
            throw new NotAllowedException();
        }

        $groupIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_ID);

        $groupMembershipService = $this->getGroupMembershipService();
        $groupService = $this->getGroupService();
        $translator = $this->getTranslator();

        $failures = 0;

        if (!empty($groupIdentifiers))
        {
            if (!is_array($groupIdentifiers))
            {
                $groupIdentifiers = [$groupIdentifiers];
            }

            foreach ($groupIdentifiers as $groupIdentifier)
            {
                $group = $groupService->findGroupByIdentifier($groupIdentifier);

                try
                {
                    $groupMembershipService->emptyGroup($group);
                }
                catch (RuntimeException)
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($groupIdentifiers) == 1)
                {
                    $message = 'SelectedGroupNotEmptied';
                }
                else
                {
                    $message = 'SelectedGroupsNotEmptied';
                }
            }
            elseif (count($groupIdentifiers) == 1)
            {
                $message = 'SelectedGroupEmptied';
            }
            else
            {
                $message = 'SelectedGroupsEmptied';
            }

            if (count($groupIdentifiers) == 1)
            {
                return $this->redirectWithMessage(
                    $translator->trans($message, [], Manager::CONTEXT), (bool) $failures, [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => self::ACTION_BROWSE,
                        self::PARAM_GROUP_ID => $groupIdentifiers[0]
                    ]
                );
            }
            else
            {
                return $this->redirectWithMessage(
                    $translator->trans($message, [], Manager::CONTEXT), (bool) $failures, [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => self::ACTION_BROWSE
                    ]
                );
            }
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
