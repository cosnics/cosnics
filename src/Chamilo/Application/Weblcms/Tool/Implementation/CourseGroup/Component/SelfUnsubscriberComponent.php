<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class SelfUnsubscriberComponent extends Manager
{

    public function run()
    {
        $course_group = $this->get_course_group();
        $user = $this->get_user();

        if ($course_group->is_self_unregistration_allowed() && $course_group->is_member($user) &&
             $this->get_user()->get_id() == $user->get_id())
        {
            $course_group->unsubscribe_users($this->get_user_id());
            $this->getCourseGroupDecoratorsManager()->unsubscribeUser($course_group, $this->getUser());

            $this->redirect(
                Translation::get('UserUnsubscribed'),
                false,
                array('tool_action' => self::ACTION_GROUP_DETAILS));
        }
        else
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }
    }
}
