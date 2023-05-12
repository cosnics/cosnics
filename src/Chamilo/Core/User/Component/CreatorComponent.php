<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\UserForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package user.lib.user_manager.component
 */
class CreatorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        $user = $this->get_user();
        $user_id = $user->get_id();

        if (! $user->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $user = new User();
        $user->set_platformadmin(0);
        $user->set_password(1);

        $user_info = $this->get_user();
        $user->set_creator_id($user_info->get_id());

        $form = new UserForm(UserForm::TYPE_CREATE, $user, $this->get_user(), $this->get_url());

        if ($form->validate())
        {
            $success = $form->create_user();
            if ($success == 1)
            {
                $this->redirectWithMessage(
                    Translation::get($success ? 'UserCreated' : 'UserNotCreated'), !$success,
                    array(Application::PARAM_ACTION => self::ACTION_BROWSE_USERS));
            }
            else
            {
                Request::set_get('error_message', Translation::get('UsernameNotAvailable'));

                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_USERS)),
                Translation::get('AdminUserBrowserComponent')));
    }
}
