<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package user.lib.user_manager.component
 */
abstract class ActiveChangerComponent extends Manager
{

    abstract protected function getState();

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->checkAuthorization(Manager::context(), 'ManageUsers');

        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_USER_ID);
        $this->set_parameter(self::PARAM_USER_USER_ID, $ids);

        $active = $this->getState();
        $this->set_parameter(self::PARAM_ACTIVE, $active);

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
                    $failures ++;
                    continue;
                }

                $user = DataManager::retrieve_by_id(
                    User::class,
                    (int) $id);
                $user->set_active($active);

                if ($user->update())
                {
                    Event::trigger(
                        'Update',
                        Manager::context(),
                        array(
                            ChangesTracker::PROPERTY_REFERENCE_ID => $user->get_id(),
                            ChangesTracker::PROPERTY_USER_ID => $this->get_user()->get_id()));
                }
                else
                {
                    $failures ++;
                }
            }

            if ($active == 0)
                $message = $this->get_result(
                    $failures,
                    count($ids),
                    'UserNotDeactivated',
                    'UsersNotDeactivated',
                    'UserDeactivated',
                    'UsersDeactivated');
            else
                $message = $this->get_result(
                    $failures,
                    count($ids),
                    'UserNotActivated',
                    'UsersNotActivated',
                    'UserActivated',
                    'UsersActivated');

            $this->redirectWithMessage(
                $message,
                ($failures > 0),
                array(Application::PARAM_ACTION => self::ACTION_BROWSE_USERS));
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

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_USER_APPROVAL_BROWSER)),
                Translation::get('UserApprovalBrowserComponent')));
    }
}
