<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package user.lib.user_manager.component
 */
class ChangeUserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $id = $this->getRequest()->query->get(self::PARAM_USER_USER_ID);
        $this->set_parameter(self::PARAM_USER_USER_ID, $id);

        if ($id)
        {
            $session = $this->getSession();

            $checkurl = $session->get('checkChamiloURL');
            $session->clear();
            $session->set(Manager::SESSION_USER_ID, $id);
            $session->set('_as_admin', $this->getUser()->getId());
            $session->set('checkChamiloURL', $checkurl);

            $loginApplication = Configuration::get('Chamilo\Core\Admin', 'page_after_login');

            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters([Application::PARAM_CONTEXT => $loginApplication])
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', ['OBJECT' => Translation::get('User')], StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_USERS]),
                Translation::get('AdminUserBrowserComponent')
            )
        );
    }

}
