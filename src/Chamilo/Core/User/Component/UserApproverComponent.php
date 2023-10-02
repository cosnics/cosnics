<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Component
 */
class UserApproverComponent extends Manager
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

        $userService = $this->getUserService();
        $translator = $this->getTranslator();

        $userIdentifiers = (array) $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_USER_ID, []);
        $choice = $this->getRequest()->getFromRequestOrQuery(self::PARAM_CHOICE);

        if (count($userIdentifiers) > 0)
        {
            $failures = 0;

            foreach ($userIdentifiers as $id)
            {
                $user = $userService->findUserByIdentifier($id);

                if ($choice == self::CHOICE_APPROVE)
                {
                    if (!$userService->approveUser($this->getUser(), $user))
                    {
                        $failures ++;
                    }
                }
                elseif (!$userService->deleteUser($user))
                {
                    $failures ++;
                }
            }

            if ($choice == self::CHOICE_APPROVE)
            {
                $message = $this->get_result(
                    $failures, count($userIdentifiers), 'UserNotApproved', 'UsersNotApproved', 'UserApproved',
                    'UsersApproved'
                );
            }
            else
            {
                $message = $this->get_result(
                    $failures, count($userIdentifiers), 'UserNotDenied', 'UsersNotDenied', 'UserDenied', 'UsersDenied'
                );
            }

            $this->redirectWithMessage(
                $message, ($failures > 0), [Application::PARAM_ACTION => self::ACTION_USER_APPROVAL_BROWSER]
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
                $this->getTranslator()->trans('UserApprovalBrowserComponent', [], Manager::CONTEXT)
            )
        );
    }
}
