<?php

namespace Chamilo\Application\Weblcms\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseSubscriptionRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * CourseSubscriptionRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation $courseEntityRelation
     *
     * @return bool
     */
    public function createCourseEntityRelation(CourseEntityRelation $courseEntityRelation)
    {
        return $this->dataClassRepository->create($courseEntityRelation);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation $courseEntityRelation
     *
     * @return bool
     */
    public function deleteCourseEntityRelation(CourseEntityRelation $courseEntityRelation)
    {
        return $this->dataClassRepository->delete($courseEntityRelation);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findCourseEntityRelationForCourseAndGroup(Group $group, Course $course)
    {
        $parameters = new DataClassRetrieveParameters(
            $this->getCourseEntityRelationCondition($course, $group->getId(), CourseEntityRelation::ENTITY_TYPE_GROUP)
        );

        return $this->dataClassRepository->retrieve(CourseEntityRelation::class, $parameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $entityId
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getCourseEntityRelationCondition(Course $course, int $entityId, int $entityType)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_TYPE
            ),
            new StaticConditionVariable($entityType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course->getId())
        );

        return new AndCondition($conditions);
    }

}