<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: unsubscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class GroupUnsubscribeComponent extends Manager
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

        $course = $this->get_course();
        $group_ids = $this->getRequest()->get(self :: PARAM_OBJECTS);
        if (! is_array($group_ids))
        {
            $group_ids = array($group_ids);
        }
        if (isset($course))
        {
            if (isset($group_ids))
            {
                $failures = 0;

                $course_management_rights = CourseManagementRights :: getInstance();

                foreach ($group_ids as $group_id)
                {
                    if (! $this->get_user()->is_platform_admin() && ! $course_management_rights->is_allowed_for_platform_group(
                        CourseManagementRights :: TEACHER_UNSUBSCRIBE_RIGHT,
                        $group_id,
                        $course->get_id()))
                    {
                        $failures ++;
                        continue;
                    }

                    if (! $this->get_parent()->unsubscribe_group_from_course($course, $group_id))
                    {
                        $failures ++;
                    }
                }

                if ($failures == 0)
                {
                    $success = true;

                    if (count($group_ids) == 1)
                    {
                        $message = 'GroupUnsubscribedFromCourse';
                    }
                    else
                    {
                        $message = 'GroupsUnsubscribedFromCourse';
                    }
                }
                elseif ($failures == count($group_ids))
                {
                    $success = false;

                    if (count($group_ids) == 1)
                    {
                        $message = 'GroupNotUnsubscribedFromCourse';
                    }
                    else
                    {
                        $message = 'GroupsNotUnsubscribedFromCourse';
                    }
                }
                else
                {
                    $success = false;
                    $message = 'PartialGroupsNotUnsubscribedFromCourse';
                }

                $this->redirect(
                    Translation :: get($message),
                    ($success ? false : true),
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_BROWSER,
                        self :: PARAM_TAB => Request :: get(self :: PARAM_TAB)));
            }
            else
            {
                return $this->display_error_page(
                    htmlentities(
                        Translation :: get(
                            'NoObjectsSelected',
                            array('OBJECT' => Translation :: get('Group')),
                            Utilities :: COMMON_LIBRARIES)));
            }
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('Course')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }
}
