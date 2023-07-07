<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\UserForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package user.lib.user_manager.component
 */
class UpdaterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (! $this->get_user()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $id = Request::get(self::PARAM_USER_USER_ID);
        $this->set_parameter(self::PARAM_USER_USER_ID, $id);

        if ($id)
        {
            $user = DataManager::retrieve_by_id(
                User::class,
                (int) $id);

            $form = new UserForm(
                UserForm::TYPE_EDIT,
                $user,
                $this->get_user(),
                $this->get_url(array(self::PARAM_USER_USER_ID => $id)));

            if ($form->validate())
            {
                $success = $form->update_user();
                $this->redirectWithMessage(
                    Translation::get($success ? 'UserUpdated' : 'UserNotUpdated'), !$success,
                    array(Application::PARAM_ACTION => self::ACTION_BROWSE_USERS));
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
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation::get('User')),
                        StringUtilities::LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_USERS)),
                Translation::get('AdminUserBrowserComponent')));
    }
}
