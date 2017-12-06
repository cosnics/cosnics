<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupPublicationCategory;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupPublicationCategoryRepository extends CommonDataClassRepository
{
    /**
     * Finds the publication categories for a given course group, optionally limiting them by a tool
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $toolName
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory[]
     */
    public function findPublicationCategoriesForCourseGroup(CourseGroup $courseGroup, $toolName = null)
    {
        $joins = $this->getJoinsWithPublicationCategory(CourseGroupPublicationCategory::class);
        $condition = $this->getConditionsForCourseGroupAndTool($courseGroup, $toolName);

        return $this->dataClassRepository->retrieves(
            ContentObjectPublicationCategory::class,
            new DataClassRetrievesParameters($condition, null, null, [], $joins)
        );
    }

    /**
     * Finds the publication categories for a given course group, optionally limiting them by a tool
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $toolName
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | CourseGroupPublicationCategory[]
     */
    public function findCourseGroupPublicationCategoriesForCourseGroup(CourseGroup $courseGroup, $toolName = null)
    {
        $joins = $this->getJoinsWithPublicationCategory(ContentObjectPublicationCategory::class);
        $condition = $this->getConditionsForCourseGroupAndTool($courseGroup, $toolName);

        return $this->dataClassRepository->retrieves(
            CourseGroupPublicationCategory::class,
            new DataClassRetrievesParameters($condition, null, null, [], $joins)
        );
    }

    /**
     * Returns the conditions for a course group and optionally a tool
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $toolName
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getConditionsForCourseGroupAndTool(CourseGroup $courseGroup, $toolName)
    {
        $conditions = [
            new EqualityCondition(
                new PropertyConditionVariable(
                    CourseGroupPublicationCategory::class,
                    CourseGroupPublicationCategory::PROPERTY_COURSE_GROUP_ID
                ),
                new StaticConditionVariable($courseGroup->getId())
            )
        ];

        if (!empty($toolName))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory::class,
                    ContentObjectPublicationCategory::PROPERTY_TOOL
                ),
                new StaticConditionVariable($toolName)
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * @param string $joinClass
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    protected function getJoinsWithPublicationCategory($joinClass)
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $joinClass, new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseGroupPublicationCategory::class,
                        CourseGroupPublicationCategory::PROPERTY_PUBLICATION_CATEGORY_ID
                    ),
                    new PropertyConditionVariable(
                        ContentObjectPublicationCategory::class,
                        ContentObjectPublicationCategory::PROPERTY_ID
                    )
                )
            )
        );

        return $joins;
    }
}
