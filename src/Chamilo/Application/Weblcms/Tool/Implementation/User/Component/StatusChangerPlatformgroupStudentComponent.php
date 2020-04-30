<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class StatusChangerPlatformgroupStudentComponent extends StatusChangerComponent
{
    
    // 1 = teacher, 5 = student
    public function get_relation()
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class,
                CourseEntityRelation::PROPERTY_ENTITY_TYPE), 
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($this->object));
        
        $condition = new AndCondition($conditions);
        
        return DataManager::retrieve(
            CourseEntityRelation::class,
            new DataClassRetrieveParameters($condition));
    }

    public function get_status()
    {
        return CourseEntityRelation::STATUS_STUDENT;
    }
}
