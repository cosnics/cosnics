<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: course_group_self_subscriber.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.course_group.component
 */
class SelfSubscriberComponent extends Manager
{

    public function run()
    {
        $course_group = $this->get_course_group();
        
        if ($course_group->is_self_registration_allowed() && ($course_group->count_members() <
             $course_group->get_max_number_of_members() || $course_group->get_max_number_of_members() == 0) &&
             ! $course_group->is_member($this->get_user()) && DataManager :: more_subscriptions_allowed_for_user_in_group(
                $course_group->get_parent_id(), 
                $this->get_user_id()))
        {
            $course_group->subscribe_users($this->get_user());
            $this->redirect(
                Translation :: get('UserSubscribed'), 
                false, 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE, 
                    self :: PARAM_COURSE_GROUP => $course_group->get_id()));
        }
        else
        {
            $this->redirect(
                Translation :: get('UserNotSubscribed'), 
                true, 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: DEFAULT_ACTION, 
                    self :: PARAM_COURSE_GROUP => $course_group->get_parent_id()));
        }
    }
}
