<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Dataclass repository for the group application
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository
     */
    private $nestedSetDataClassRepository;

    /**
     * @var \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    private $searchQueryConditionGenerator;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository $nestedSetDataClassRepository
     * @param \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator $searchQueryConditionGenerator
     */
    public function __construct(
        NestedSetDataClassRepository $nestedSetDataClassRepository,
        SearchQueryConditionGenerator $searchQueryConditionGenerator
    )
    {
        $this->nestedSetDataClassRepository = $nestedSetDataClassRepository;
        $this->searchQueryConditionGenerator = $searchQueryConditionGenerator;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countGroups(Condition $condition = null)
    {
        return $this->getNestedSetDataClassRepository()->count(Group::class, new DataClassCountParameters($condition));
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $recursiveSubgroups
     *
     * @return int
     */
    public function countSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        return $this->getNestedSetDataClassRepository()->countDescendants($group, $recursiveSubgroups);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return bool
     * @throws \Exception
     */
    public function createGroup(Group $group)
    {
        return $this->getNestedSetDataClassRepository()->create($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|ArrayCollection|\Chamilo\Libraries\Storage\DataClass\NestedSet[]
     * @throws \Exception
     */
    public function deleteGroup(Group $group)
    {
        return $this->getNestedSetDataClassRepository()->delete($group);
    }

    /**
     * @return ArrayCollection
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findDirectlySubscribedGroupNestingValuesForUserIdentifier(string $userIdentifier)
    {
        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID));
        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID));

        $joinConditions = [];

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)
        );

        $joins = new Joins([new Join(GroupRelUser::class, new AndCondition($joinConditions))]);

        $parameters = new RecordRetrievesParameters($properties, null, null, null, null, $joins);

        return $this->getNestedSetDataClassRepository()->records(Group::class, $parameters);
    }

    /**
     * Finds a group object by a given group code
     *
     * @param string $groupCode
     *
     * @return DataClass | Group
     */
    public function findGroupByCode($groupCode)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), new StaticConditionVariable($groupCode)
        );

        return $this->getNestedSetDataClassRepository()->retrieve(
            Group::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Finds a group object by a given group code and parent identifier
     *
     * @param string $groupCode
     * @param int $parentIdentifier
     *
     * @return Group
     * @throws \Exception
     */
    public function findGroupByCodeAndParentIdentifier($groupCode, $parentIdentifier)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), new StaticConditionVariable($groupCode)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        return $this->getNestedSetDataClassRepository()->retrieve(
            Group::class, new DataClassRetrieveParameters(new AndCondition($conditions))
        );
    }

    /**
     * @param int $groupId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | Group
     */
    public function findGroupByIdentifier($groupId)
    {
        return $this->getNestedSetDataClassRepository()->retrieveById(Group::class, $groupId);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|string[][] $directlySubscribedGroupNestingValues
     *
     * @return string[]
     * @throws \Exception
     */
    public function findGroupIdentifiersForDirectlySubscribedGroupNestingValues(
        ArrayCollection $directlySubscribedGroupNestingValues
    )
    {
        $parameters = new DataClassDistinctParameters(
            $this->getDirectlySubscribedGroupNestingValuesConditions($directlySubscribedGroupNestingValues),
            new RetrieveProperties([new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)])
        );

        return $this->getNestedSetDataClassRepository()->distinct(Group::class, $parameters);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|ArrayCollection
     */
    public function findGroups(
        Condition $condition = null, int $count = null, int $offset = null, ?OrderBy $orderBy = null
    )
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return $this->getNestedSetDataClassRepository()->retrieves(Group::class, $parameters);
    }

    /**
     * @param $groupIdentifiers
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|ArrayCollection
     */
    public function findGroupsByIdentifiersOrderedByName($groupIdentifiers)
    {
        $orderBy = OrderBy::generate(Group::class, Group::PROPERTY_NAME);

        $condition =
            new InCondition(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), $groupIdentifiers);

        return $this->getNestedSetDataClassRepository()->retrieves(
            Group::class, new DataClassRetrievesParameters($condition, null, null, $orderBy)
        );
    }

    /**
     * @param string[][] $directlySubscribedGroupNestingValues
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|ArrayCollection
     */
    public function findGroupsForDirectlySubscribedGroupNestingValues(
        ArrayCollection $directlySubscribedGroupNestingValues
    )
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getDirectlySubscribedGroupNestingValuesConditions($directlySubscribedGroupNestingValues)
        );

        return $this->getNestedSetDataClassRepository()->retrieves(Group::class, $parameters);
    }

    /**
     * @param string $searchQuery
     * @param int $parentIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|ArrayCollection
     */
    public function findGroupsForSearchQueryAndParentIdentifier(string $searchQuery = null, int $parentIdentifier = 0)
    {
        $conditions = [];

        if ($searchQuery && $searchQuery != '')
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, [
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME),
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE)
                ]
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        $condition = new AndCondition($conditions);

        return $this->getNestedSetDataClassRepository()->retrieves(
            Group::class, new DataClassRetrievesParameters(
                $condition, null, null,
                new OrderBy([new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))])
            )
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return string[]|int
     * @throws \Exception
     */
    public function findParentGroupIdentifiersForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->getNestedSetDataClassRepository()->findAncestorIdentifiers($group, $includeSelf);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|ArrayCollection|\Chamilo\Libraries\Storage\DataClass\NestedSet[]
     */
    public function findParentGroupsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->getNestedSetDataClassRepository()->findAncestors($group, $includeSelf);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $recursiveSubgroups
     *
     * @return int|string[]
     * @todo This could be generalized to the NestedSetDataClassRepository
     */
    public function findSubGroupIdentifiersForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        if ($recursiveSubgroups)
        {
            $childrenCondition = [];

            $childrenCondition[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                ComparisonCondition::GREATER_THAN, new StaticConditionVariable($group->getLeftValue())
            );

            $childrenCondition[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                ComparisonCondition::LESS_THAN, new StaticConditionVariable($group->getRightValue())
            );

            $childrenCondition = new AndCondition($childrenCondition);
        }
        else
        {
            $childrenCondition = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                new StaticConditionVariable($group->getId())
            );
        }

        return $this->getNestedSetDataClassRepository()->distinct(
            Group::class, new DataClassDistinctParameters(
                $childrenCondition,
                new RetrieveProperties([new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)])
            )
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $recursiveSubgroups
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|ArrayCollection
     */
    public function findSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        return $this->getNestedSetDataClassRepository()->findDescendants($group, $recursiveSubgroups);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|string[][] $directlySubscribedGroupNestingValues
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    protected function getDirectlySubscribedGroupNestingValuesConditions(
        ArrayCollection $directlySubscribedGroupNestingValues
    )
    {
        $treeConditions = [];
        $alreadyIncludedParents = [];
        $directGroupIds = [];

        foreach ($directlySubscribedGroupNestingValues as $descendent)
        {
            if (!in_array($descendent[Group::PROPERTY_PARENT_ID], $alreadyIncludedParents))
            {

                $treeConditions[] = new AndCondition(
                    [
                        new ComparisonCondition(
                            new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                            ComparisonCondition::LESS_THAN_OR_EQUAL,
                            new StaticConditionVariable($descendent[Group::PROPERTY_LEFT_VALUE])
                        ),

                        new ComparisonCondition(
                            new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                            ComparisonCondition::GREATER_THAN_OR_EQUAL,
                            new StaticConditionVariable($descendent[Group::PROPERTY_RIGHT_VALUE])
                        )
                    ]
                );

                $alreadyIncludedParents[] = $descendent[Group::PROPERTY_PARENT_ID];
            }

            $directGroupIds[] = $descendent[Group::PROPERTY_ID];
        }

        $treeConditions[] = new InCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), $directGroupIds
        );

        return new OrCondition($treeConditions);
    }

    /**
     * @param int $function
     * @param int[] $userGroupIdentifiers
     *
     * @return int
     */
    protected function getGroupQuotumWithFunctionForUserGroupIdentifiers(int $function, array $userGroupIdentifiers
    ): int
    {
        $condition =
            new InCondition(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), $userGroupIdentifiers);

        $parameters = new RecordRetrieveParameters(
            new RetrieveProperties(
                [
                    new FunctionConditionVariable(
                        $function, new PropertyConditionVariable(Group::class, Group::PROPERTY_DISK_QUOTA),
                        Group::PROPERTY_DISK_QUOTA
                    )
                ]
            ), $condition
        );

        $record = $this->getNestedSetDataClassRepository()->record(Group::class, $parameters);

        return (int) $record[Group::PROPERTY_DISK_QUOTA];
    }

    /**
     * @param int $userGroupIdentifiers
     *
     * @return int
     */
    public function getHighestGroupQuotumForUserGroupIdentifiers(array $userGroupIdentifiers): int
    {
        return $this->getGroupQuotumWithFunctionForUserGroupIdentifiers(
            FunctionConditionVariable::MAX, $userGroupIdentifiers
        );
    }

    /**
     * @param int[] $userGroupIdentifiers
     *
     * @return int
     */
    public function getLowestGroupQuotumForUserGroupIdentifiers(array $userGroupIdentifiers): int
    {
        return $this->getGroupQuotumWithFunctionForUserGroupIdentifiers(
            FunctionConditionVariable::MIN, $userGroupIdentifiers
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository
     */
    public function getNestedSetDataClassRepository(): NestedSetDataClassRepository
    {
        return $this->nestedSetDataClassRepository;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    public function getSearchQueryConditionGenerator(): SearchQueryConditionGenerator
    {
        return $this->searchQueryConditionGenerator;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param int $parentGroupIdentifier
     *
     * @return bool
     * @throws \Exception
     */
    public function moveGroup(Group $group, int $parentGroupIdentifier)
    {
        return $this->getNestedSetDataClassRepository()->move($group, $parentGroupIdentifier);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository $nestedSetDataClassRepository
     */
    public function setNestedSetDataClassRepository(NestedSetDataClassRepository $nestedSetDataClassRepository): void
    {
        $this->nestedSetDataClassRepository = $nestedSetDataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator $searchQueryConditionGenerator
     */
    public function setSearchQueryConditionGenerator(SearchQueryConditionGenerator $searchQueryConditionGenerator): void
    {
        $this->searchQueryConditionGenerator = $searchQueryConditionGenerator;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return bool
     */
    public function updateGroup(Group $group)
    {
        return $this->getNestedSetDataClassRepository()->update($group);
    }
}