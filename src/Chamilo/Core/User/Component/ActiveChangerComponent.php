<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ActiveChangerComponent extends Manager
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

        $userService = $this->getUserService();
        $translator = $this->getTranslator();

        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_USER_ID);
        $this->set_parameter(self::PARAM_USER_USER_ID, $ids);

        $active = $this->getState();
        $this->set_parameter(self::PARAM_ACTIVE, $active);

        if (!is_array($ids))
        {
            $ids = [$ids];
        }

        if (count($ids) > 0)
        {
            $failures = 0;

            foreach ($ids as $id)
            {
                if (!$this->getUser()->isPlatformAdmin())
                {
                    $failures ++;
                    continue;
                }

                $user = $userService->findUserByIdentifier($id);
                $user->set_active($active);

                if ($userService->updateUser($user))
                {
                    Event::trigger(
                        'Update', Manager::CONTEXT, [
                            ChangesTracker::PROPERTY_REFERENCE_ID => $user->getId(),
                            ChangesTracker::PROPERTY_USER_ID => $this->getUser()->getId()
                        ]
                    );
                }
                else
                {
                    $failures ++;
                }
            }

            if ($active == 0)
            {
                $message = $this->get_result(
                    $failures, count($ids), 'UserNotDeactivated', 'UsersNotDeactivated', 'UserDeactivated',
                    'UsersDeactivated'
                );
            }
            else
            {
                $message = $this->get_result(
                    $failures, count($ids), 'UserNotActivated', 'UsersNotActivated', 'UserActivated', 'UsersActivated'
                );
            }

            $this->redirectWithMessage(
                $message, ($failures > 0), [Application::PARAM_ACTION => self::ACTION_BROWSE_USERS]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['OBJECT' => $translator->trans('User', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_USER_APPROVAL_BROWSER]),
                $this->getTranslator()->trans('UserApprovalBrowserComponent')
            )
        );
    }

    abstract protected function getState(): int;
}
