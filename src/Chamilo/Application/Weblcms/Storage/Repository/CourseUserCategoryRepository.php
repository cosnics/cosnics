<?php

namespace Chamilo\Application\Weblcms\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategoryRelCourse;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseUserCategoryRepository extends CommonDataClassRepository
{
    /**
     * @param int $courseUserCategoryId
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findCourseUserCategoryById($courseUserCategoryId)
    {
        return $this->dataClassRepository->retrieveById(CourseUserCategory::class, $courseUserCategoryId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory $courseUserCategory
     * @param \Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType $courseType
     * @param array $subscribedCourseIds
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Course[]
     */
    public function findCoursesForUserByCourseUserCategoryAndCourseType(
        CourseUserCategory $courseUserCategory, CourseType $courseType, $subscribedCourseIds = []
    )
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_COURSE_USER_CATEGORY_ID
            ), new StaticConditionVariable($courseUserCategory->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_COURSE_TYPE_ID
            ), new StaticConditionVariable($courseType->getId())
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                Course::class, Course::PROPERTY_ID
            ), $subscribedCourseIds
        );

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                CourseTypeUserCategoryRelCourse::class, new EqualityCondition(
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_ID), new PropertyConditionVariable(
                        CourseTypeUserCategoryRelCourse::class, CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_ID
                    )
                )
            )
        );

        $joins->add(
            new Join(
                CourseTypeUserCategory::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseTypeUserCategoryRelCourse::class,
                        CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID
                    ), new PropertyConditionVariable(
                        CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_ID
                    )
                )
            )
        );

        $orderBy =
            OrderBy::generate(CourseTypeUserCategoryRelCourse::class, CourseTypeUserCategoryRelCourse::PROPERTY_SORT);

        return $this->dataClassRepository->retrieves(
            Course::class, new DataClassRetrievesParameters($condition, null, null, $orderBy, $joins)
        );
    }
}
