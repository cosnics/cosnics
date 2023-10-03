<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\UserUpdateForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Component
 */
class UpdaterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $selectedUserIdentifier = $this->getRequest()->query->get(self::PARAM_USER_USER_ID);

        if ($selectedUserIdentifier)
        {
            $selectedUser = $this->getUserService()->findUserByIdentifier($selectedUserIdentifier);
            $isLockoutRisk = $this->getUser()->getId() == $selectedUser->getId() && $selectedUser->isPlatformAdmin();

            $form = new UserUpdateForm(
                $selectedUser, $isLockoutRisk, $this->get_url([self::PARAM_USER_USER_ID => $id])
            );

            if ($form->validate())
            {
                //                $success = $form->update_user();
                //                $this->redirectWithMessage(
                //                    Translation::get($success ? 'UserUpdated' : 'UserNotUpdated'), !$success,
                //                    [Application::PARAM_ACTION => self::ACTION_BROWSE_USERS]
                //                );
            }
            else
            {
                $html = [];

                $html[] = $this->renderHeader();
                $html[] = $form->render();
                $html[] = $this->renderFooter();

                return implode(PHP_EOL, $html);
            }
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
                $this->getTranslator()->trans('AdminUserBrowserComponent', [], Manager::CONTEXT)
            )
        );
    }
}
