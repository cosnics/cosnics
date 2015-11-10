<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
abstract class ActiveChangerComponent extends Manager
{

    abstract private function getState();

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $ids = $this->getRequest()->get(self :: PARAM_USER_USER_ID);
        $this->set_parameter(self :: PARAM_USER_USER_ID, $ids);

        $active = $this->getState();
        $this->set_parameter(self :: PARAM_ACTIVE, $active);

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

                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    (int) $id);
                $user->set_active($active);

                if ($user->update())
                {
                    Event :: trigger(
                        'Update',
                        Manager :: context(),
                        array(
                            ChangesTracker :: PROPERTY_REFERENCE_ID => $user->get_id(),
                            ChangesTracker :: PROPERTY_USER_ID => $this->get_user()->get_id()));
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

            $this->redirect(
                $message,
                ($failures > 0),
                array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_USERS));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => TRanslation :: get('User')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_USER_APPROVAL_BROWSER)),
                Translation :: get('UserManagerUserApprovalBrowserComponent')));
        $breadcrumbtrail->add_help('user_active_changer');
    }
}
