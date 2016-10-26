<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * $Id: change_user.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
class ChangeUserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');
        
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $id = Request :: get(self :: PARAM_USER_USER_ID);
        $this->set_parameter(self :: PARAM_USER_USER_ID, $id);

        if ($id)
        {

            $checkurl = \Chamilo\Libraries\Platform\Session\Session :: retrieve('checkChamiloURL');
            \Chamilo\Libraries\Platform\Session\Session :: clear();
            \Chamilo\Libraries\Platform\Session\Session :: register('_uid', $id);
            \Chamilo\Libraries\Platform\Session\Session :: register('_as_admin', $this->get_user_id());
            \Chamilo\Libraries\Platform\Session\Session :: register('checkChamiloURL', $checkurl);

            $loginApplication = Configuration :: get('Chamilo\Core\Admin', 'page_after_login');
            $response = new RedirectResponse($this->get_link(array(Application :: PARAM_CONTEXT => $loginApplication)));
            $response->send();
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
                Translation :: get('AdminUserBrowserComponent')));

        $breadcrumbtrail->add_help('user_changer');
    }
}
