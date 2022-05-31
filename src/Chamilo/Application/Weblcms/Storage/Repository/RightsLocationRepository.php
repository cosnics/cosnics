<?php

namespace Chamilo\Application\Weblcms\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RightsLocationRepository extends CommonDataClassRepository
{
    /**
     * Finds a single rights location in a given course identified by his type and identifier
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $type
     * @param int $identifier
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findRightsLocationInCourse(Course $course, $type = WeblcmsRights::TYPE_ROOT, $identifier = 0)
    {
        $conditions = [
            new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_TREE_TYPE),
                new StaticConditionVariable(WeblcmsRights::TREE_TYPE_COURSE)
            ),
            new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_TREE_IDENTIFIER),
                new StaticConditionVariable($course->getId())
            ),
            new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_TYPE),
                new StaticConditionVariable($type)
            ),
            new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_IDENTIFIER),
                new StaticConditionVariable($identifier)
            )
        ];

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(RightsLocation::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $entityType
     * @param array $entityIds
     *
     * @throws \Exception
     */
    public function removeEntitiesFromRightsByIds(Course $course, int $entityType, array $entityIds = [])
    {
        $locationCondition = new EqualityCondition(
            new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($course->getId())
        );

        $locationIds = $this->dataClassRepository->distinct(
            RightsLocation::class,
            new DataClassDistinctParameters(
                $locationCondition,
                new RetrieveProperties(
                    new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_ID)
                )
            )
        );

        $conditions = [];

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), $locationIds
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable($entityType)
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ENTITY_ID
            ), $entityIds
        );

        $condition = new AndCondition($conditions);

        $this->dataClassRepository->deletes(CourseEntityRelation::class, $condition);
    }

    /**
     * Creates a rights location directly into the database.
     *
     * WARNING: DO NOT USE THIS, DO NOT EVER USE THIS UNLESS YOU KNOW WHAT YOU ARE DOING AND NEED
     * TO BYPASS THE LEFT AND RIGHT VALUES OF THE LOCATION. THIS IS ONLY USED IN SITUATIONS WHERE BATCH OPERATIONS
     * OR ERROR CLEANUPS ARE NEEDED. MAKE SURE TO FIX THE LEFT AND THE RIGHT VALUES OF YOUR LOCATIONS IF YOU DO
     * USE THIS!
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation $rightsLocation
     *
     * @return bool
     */
    public function createRightsLocationDirectlyInDatabase(RightsLocation $rightsLocation)
    {
        return $this->dataClassRepository->create($rightsLocation);
    }

    /**
     * Updates a rights location directly into the database.
     *
     * WARNING: DO NOT USE THIS, DO NOT EVER USE THIS UNLESS YOU KNOW WHAT YOU ARE DOING AND NEED
     * TO BYPASS THE LEFT AND RIGHT VALUES OF THE LOCATION. THIS IS ONLY USED IN SITUATIONS WHERE BATCH OPERATIONS
     * OR ERROR CLEANUPS ARE NEEDED. MAKE SURE TO FIX THE LEFT AND THE RIGHT VALUES OF YOUR LOCATIONS IF YOU DO
     * USE THIS!
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation $rightsLocation
     *
     * @return bool
     */
    public function updateRightsLocationDirectlyInDatabase(RightsLocation $rightsLocation)
    {
        return $this->dataClassRepository->update($rightsLocation);
    }

}