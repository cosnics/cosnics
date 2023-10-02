<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Component
 */
class DeleterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $userIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_USER_ID);

        $translator = $this->getTranslator();
        $userService = $this->getUserService();

        if (!is_array($userIdentifiers))
        {
            $userIdentifiers = [$userIdentifiers];
        }

        if (count($userIdentifiers) > 0)
        {
            $failures = 0;

            foreach ($userIdentifiers as $userIdentifier)
            {
                $user = $userService->findUserByIdentifier($userIdentifier);

                if (!$userService->deleteUser($user))
                {
                    $failures ++;
                }
            }

            $message = $this->get_result(
                $failures, count($userIdentifiers), 'UserNotDeleted', 'UsersNotDeleted', 'UserDeleted', 'UsersDeleted'
            );

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
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_USERS]),
                $this->getTranslator()->trans('AdminUserBrowserComponent')
            )
        );
    }
}
