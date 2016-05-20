<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\UserForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: updater.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
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
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $id = Request :: get(self :: PARAM_USER_USER_ID);
        $this->set_parameter(self :: PARAM_USER_USER_ID, $id);

        if ($id)
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                (int) $id);

            $form = new UserForm(
                UserForm :: TYPE_EDIT,
                $user,
                $this->get_user(),
                $this->get_url(array(self :: PARAM_USER_USER_ID => $id)));

            if ($form->validate())
            {
                $success = $form->update_user();
                $this->redirect(
                    Translation :: get($success ? 'UserUpdated' : 'UserNotUpdated'),
                    ($success ? false : true),
                    array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_USERS));
            }
            else
            {
                $html = array();

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
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('User')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_USERS)),
                Translation :: get('UserManagerAdminUserBrowserComponent')));
        $breadcrumbtrail->add_help('user_updater');
    }
}
