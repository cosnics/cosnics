<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Utilities\StringUtilities;
use RuntimeException;

/**
 * @package Chamilo\Core\Group\Component
 */
class TruncaterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $groupIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_ID);
        $this->set_parameter(self::PARAM_GROUP_ID, $groupIdentifiers);

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
                $this->redirectWithMessage(
                    $translator->trans($message, [], Manager::CONTEXT), (bool) $failures,
                    [Application::PARAM_ACTION => self::ACTION_VIEW_GROUP, self::PARAM_GROUP_ID => $groupIdentifiers[0]]
                );
            }
            else
            {
                $this->redirectWithMessage(
                    $translator->trans($message, [], Manager::CONTEXT), (bool) $failures,
                    [Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]
                );
            }
        }
        else
        {
            return $this->display_error_page(
                htmlentities($translator->trans('NoObjectSelected', [], StringUtilities::LIBRARIES))
            );
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
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
