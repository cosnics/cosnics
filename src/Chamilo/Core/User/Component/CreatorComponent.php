<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\UserCreationForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;

/**
 * @package user.lib.user_manager.component
 */
class CreatorComponent extends Manager
{

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        $currentUser = $this->getUser();
        $translator = $this->getTranslator();

        if (!$currentUser->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $form = new UserCreationForm($currentUser, $this->get_url());

        if ($form->validate())
        {
            //$success = $form->create_user();

            if ($success == 1)
            {
                $this->redirectWithMessage(
                    $translator->trans('UserCreated', [], Manager::CONTEXT), false,
                    [Application::PARAM_ACTION => self::ACTION_BROWSE_USERS]
                );
            }
            else
            {
                $this->getRequest()->request->set(
                    'error_message', $translator->trans('UsernameNotAvailable', [], Manager::CONTEXT)
                );

                $html = [];

                $html[] = $this->renderHeader();
                $html[] = $form->render();
                $html[] = $this->renderFooter();

                return implode(PHP_EOL, $html);
            }
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
