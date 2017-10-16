<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package user.lib.user_manager.component
 */
class EmailerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');

        $ids = $this->getRequest()->get(self::PARAM_USER_USER_ID);
        $this->set_parameter(self::PARAM_USER_USER_ID, $ids);

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        if (count($ids) > 0)
        {
            $failures = 0;

            foreach ($ids as $id)
            {
                if (! $this->get_user()->is_platform_admin())
                {
                    $users[] = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                        \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                        (int) $id);
                }
            }

            $application = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\User\Email\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $application->set_target_users($users);
            $application->set_parameter(self::PARAM_USER_USER_ID, $ids);
            return $application->run();
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation::get('User')),
                        Utilities::COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_USERS)),
                Translation::get('AdminUserBrowserComponent')));
        $breadcrumbtrail->add_help('user_emailer');
    }
}
