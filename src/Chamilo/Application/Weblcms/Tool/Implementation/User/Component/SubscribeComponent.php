<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: subscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class SubscribeComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $course_id = $this->get_course_id();
        $users = $this->getRequest()->get(self :: PARAM_OBJECTS);
        if (isset($users) && ! is_array($users))
        {
            $users = array($users);
        }

        if (isset($course_id))
        {
            if (isset($users) && count($users) > 0)
            {
                $failures = 0;

                $course_management_rights = CourseManagementRights :: get_instance();

                foreach ($users as $user_id)
                {
                    $status = Request :: get(self :: PARAM_STATUS) ? Request :: get(self :: PARAM_STATUS) : 5;

                    if (\Chamilo\Application\Weblcms\Course\Storage\DataManager :: is_user_direct_subscribed_to_course(
                        $user_id,
                        $course_id) || (! $this->get_user()->is_platform_admin() && ! $course_management_rights->is_allowed(
                        CourseManagementRights :: TEACHER_DIRECT_SUBSCRIBE_RIGHT,
                        $course_id,
                        CourseManagementRights :: TYPE_COURSE,
                        $user_id)))
                    {
                        $failures ++;
                        continue;
                    }

                    if (! \Chamilo\Application\Weblcms\Course\Storage\DataManager :: subscribe_user_to_course(
                        $course_id,
                        $status,
                        $user_id))
                    {
                        $failures ++;
                    }
                }

                if ($failures == 0)
                {
                    $success = true;

                    if (count($users) == 1)
                    {
                        $message = 'UserSubscribedToCourse';
                    }
                    else
                    {
                        $message = 'UsersSubscribedToCourse';
                    }
                }
                elseif ($failures == count($users))
                {
                    $success = false;

                    if (count($users) == 1)
                    {
                        $message = 'UserNotSubscribedToCourse';
                    }
                    else
                    {
                        $message = 'UsersNotSubscribedToCourse';
                    }
                }
                else
                {
                    $success = false;
                    $message = 'PartialUsersNotSubscribedToCourse';
                }

                $this->redirect(
                    Translation :: get($message),
                    ($success ? false : true),
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_BROWSER));
            }
        }
    }
}
