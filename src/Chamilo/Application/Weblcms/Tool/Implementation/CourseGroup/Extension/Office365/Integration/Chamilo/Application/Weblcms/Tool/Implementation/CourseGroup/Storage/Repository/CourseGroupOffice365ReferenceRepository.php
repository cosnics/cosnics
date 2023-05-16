<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage CourseGroupOffice365Reference objects
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365ReferenceRepository extends CommonDataClassRepository
{
    /**
     * Finds a reference by a given course group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @return CourseGroupOffice365Reference | \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findByCourseGroup(CourseGroup $courseGroup)
    {
        $condition = $this->getConditionByCourseGroup($courseGroup);

        return $this->dataClassRepository->retrieve(
            CourseGroupOffice365Reference::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Creates a new reference object
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference $courseGroupOffice365Reference
     *
     * @return bool
     */
    public function createReference(CourseGroupOffice365Reference $courseGroupOffice365Reference)
    {
        $reference = $this->dataClassRepository->create($courseGroupOffice365Reference);
        $this->dataClassRepository->getDataClassRepositoryCache()->truncate(CourseGroupOffice365Reference::class);

        return $reference;
    }

    /**
     * Updates an existing reference object
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference $courseGroupOffice365Reference
     *
     * @return bool
     */
    public function updateReference(CourseGroupOffice365Reference $courseGroupOffice365Reference)
    {
        return $this->dataClassRepository->update($courseGroupOffice365Reference);
    }

    /**
     * Removes a reference by a given course group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @return bool
     */
    public function removeReferenceForCourseGroup(CourseGroup $courseGroup)
    {
        $condition = $this->getConditionByCourseGroup($courseGroup);
        return $this->dataClassRepository->deletes(CourseGroupOffice365Reference::class, $condition);
    }

    /**
     * Builds the condition for a reference by a given course group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    protected function getConditionByCourseGroup(CourseGroup $courseGroup)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupOffice365Reference::class, CourseGroupOffice365Reference::PROPERTY_COURSE_GROUP_ID
            ),
            new StaticConditionVariable($courseGroup->getId())
        );
    }
}