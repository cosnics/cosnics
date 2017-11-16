<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\UserStatusChange;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */

/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class UnsubscribeComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $course = $this->get_course();
        $users = $this->getRequest()->get(self::PARAM_OBJECTS);
        if (! is_array($users))
        {
            $users = array($users);
        }
        if (isset($course))
        {
            if (isset($users))
            {
                $failures = 0;

                $course_management_rights = CourseManagementRights::getInstance();

                foreach ($users as $user_id)
                {
                    if (! is_null($user_id) && $user_id != $this->get_user_id())
                    {
                        if (! $this->get_user()->is_platform_admin() && (! $course_management_rights->is_allowed(
                            CourseManagementRights::TEACHER_UNSUBSCRIBE_RIGHT,
                            $course->get_id(),
                            CourseManagementRights::TYPE_COURSE,
                            $user_id) || ! $course->is_course_admin($this->get_user())))
                        {

                            $failures ++;
                            continue;
                        }
                        else
                        {
                            if (! $this->get_parent()->unsubscribe_user_from_course($course, $user_id))
                            {
                                $failures ++;
                            }
                        }

                        $parameters[UserStatusChange::PROPERTY_USER_ID] = $this->get_user_id();
                        $parameters[UserStatusChange::PROPERTY_SUBJECT_ID] = $user_id;
                        $parameters[UserStatusChange::PROPERTY_NEW_STATUS] = 0;
                        $parameters[UserStatusChange::PROPERTY_COURSE_ID] = $course->get_id();
                        $parameters[UserStatusChange::PROPERTY_DATE] = time();
                        Event::trigger('UserStatusChange', \Chamilo\Application\Weblcms\Manager::context(), $parameters);
                    }

                    if ($failures == 0)
                    {
                        $success = true;

                        if (count($users) == 1)
                        {
                            $message = 'UserUnsubscribedFromCourse';
                        }
                        else
                        {
                            $message = 'UsersUnsubscribedFromCourse';
                        }
                    }
                    elseif ($failures == count($users))
                    {
                        $success = false;

                        if (count($users) == 1)
                        {
                            $message = 'UserNotUnsubscribedFromCourse';
                        }
                        else
                        {
                            $message = 'UsersNotUnsubscribedFromCourse';
                        }
                    }
                    else
                    {
                        $success = false;
                        $message = 'PartialUsersNotUnsubscribedFromCourse';
                    }
                }
                $this->redirect(
                    Translation::get($message),
                    ($success ? false : true),
                    array(
                        self::PARAM_ACTION => self::ACTION_UNSUBSCRIBE_BROWSER,
                        self::PARAM_TAB => Request::get(self::PARAM_TAB)));
            }
        }
    }
}
