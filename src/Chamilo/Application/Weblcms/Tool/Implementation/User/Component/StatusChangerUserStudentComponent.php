<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;

class StatusChangerUserStudentComponent extends StatusChangerComponent
{

    public function get_relation()
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_course_user_relation_by_course_and_user(
            $this->get_course_id(), 
            $this->object);
    }

    public function get_status()
    {
        return CourseEntityRelation::STATUS_STUDENT;
    }
}
