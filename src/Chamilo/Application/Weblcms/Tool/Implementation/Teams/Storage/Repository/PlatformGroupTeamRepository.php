<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeamRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class PlatformGroupTeamRepository
{
    const ALIAS_GROUP_NAME = 'GroupName';
    const ALIAS_GROUP_CODE = 'GroupCode';

    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * PlatformGroupTeamRepository constructor.
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
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return bool
     */
    public function createPlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        return $this->dataClassRepository->create($platformGroupTeam);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return bool
     */
    public function updatePlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        return $this->dataClassRepository->update($platformGroupTeam);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeamRelation $platformGroupTeamRelation
     *
     * @return bool
     */
    public function createPlatformGroupTeamRelation(PlatformGroupTeamRelation $platformGroupTeamRelation)
    {
        return $this->dataClassRepository->create($platformGroupTeamRelation);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return bool
     */
    public function deletePlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        return $this->dataClassRepository->delete($platformGroupTeam);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeamRelation $platformGroupTeamRelation
     *
     * @return bool
     */
    public function deletePlatformGroupTeamRelation(PlatformGroupTeamRelation $platformGroupTeamRelation)
    {
        return $this->dataClassRepository->delete($platformGroupTeamRelation);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return bool
     */
    public function deleteRelationsForPlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PlatformGroupTeamRelation::class, PlatformGroupTeamRelation::PROPERTY_PLATFORM_GROUP_TEAM_ID
            ),
            new StaticConditionVariable($platformGroupTeam->getId())
        );

        return $this->dataClassRepository->deletes(PlatformGroupTeamRelation::class, $condition);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPlatformGroupTeamRelationForTeamAndGroup(PlatformGroupTeam $platformGroupTeam, Group $group)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PlatformGroupTeamRelation::class, PlatformGroupTeamRelation::PROPERTY_PLATFORM_GROUP_TEAM_ID
            ),
            new StaticConditionVariable($platformGroupTeam->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PlatformGroupTeamRelation::class, PlatformGroupTeamRelation::PROPERTY_PLATFORM_GROUP_TEAM_ID
            ),
            new StaticConditionVariable($group->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            PlatformGroupTeamRelation::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param int $platformGroupId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|PlatformGroupTeam
     */
    public function findPlatformGroupTeamById(int $platformGroupId)
    {
        return $this->dataClassRepository->retrieveById(PlatformGroupTeam::class, $platformGroupId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     * @throws \Exception
     */
    public function findPlatformGroupTeamsWithPlatformGroupsForCourse(Course $course)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PlatformGroupTeam::class, PlatformGroupTeam::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course->getId())
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                PlatformGroupTeamRelation::class,
                new EqualityCondition(
                    new PropertyConditionVariable(PlatformGroupTeam::class, PlatformGroupTeam::PROPERTY_ID),
                    new PropertyConditionVariable(
                        PlatformGroupTeamRelation::class, PlatformGroupTeamRelation::PROPERTY_PLATFORM_GROUP_TEAM_ID
                    )
                )
            )
        );

        $joins->add(
            new Join(
                Group::class,
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_ID),
                    new PropertyConditionVariable(
                        PlatformGroupTeamRelation::class, PlatformGroupTeamRelation::PROPERTY_GROUP_ID
                    )
                )
            )
        );

        $properties = new DataClassProperties();
        $properties->add(new PropertiesConditionVariable(PlatformGroupTeam::class));

        $properties->add(
            new FixedPropertyConditionVariable(Group::class, Group::PROPERTY_NAME, self::ALIAS_GROUP_NAME)
        );
        $properties->add(
            new FixedPropertyConditionVariable(Group::class, Group::PROPERTY_CODE, self::ALIAS_GROUP_CODE)
        );

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, [], $joins);

        return $this->dataClassRepository->records(PlatformGroupTeam::class, $parameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function findGroupsForPlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PlatformGroupTeamRelation::class, PlatformGroupTeamRelation::PROPERTY_PLATFORM_GROUP_TEAM_ID
            ),
            new StaticConditionVariable($platformGroupTeam->getId())
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                PlatformGroupTeamRelation::class,
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_ID),
                    new PropertyConditionVariable(
                        PlatformGroupTeamRelation::class, PlatformGroupTeamRelation::PROPERTY_GROUP_ID
                    )
                )
            )
        );

        $parameters = new DataClassRetrievesParameters($condition, null, null, [], $joins);

        return $this->dataClassRepository->retrieves(Group::class, $parameters);
    }

}
