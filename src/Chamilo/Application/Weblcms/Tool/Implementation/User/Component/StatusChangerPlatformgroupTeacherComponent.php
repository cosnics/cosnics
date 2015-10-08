<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseGroupRelation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class StatusChangerPlatformgroupTeacherComponent extends StatusChangerComponent
{
    const STATUS = 1; // 1 = teacher, 5 = student
    public function get_relation()
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroupRelation :: class_name(), CourseGroupRelation :: PROPERTY_COURSE_ID), 
            new StaticConditionVariable($this->get_course_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroupRelation :: class_name(), CourseGroupRelation :: PROPERTY_GROUP_ID), 
            new StaticConditionVariable($this->object));
        
        $condition = new AndCondition($conditions);
        
        return $course_group_relations = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            CourseGroupRelation :: class_name(), 
            $condition)->next_result();
    }

    public function get_status()
    {
        return self :: STATUS;
    }
}
