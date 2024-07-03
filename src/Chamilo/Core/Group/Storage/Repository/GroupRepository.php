<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
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
 * @package Chamilo\Core\Group\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupRepository
{
    private NestedSetDataClassRepository $nestedSetDataClassRepository;

    private SearchQueryConditionGenerator $searchQueryConditionGenerator;

    public function __construct(
        NestedSetDataClassRepository $nestedSetDataClassRepository,
        SearchQueryConditionGenerator $searchQueryConditionGenerator
    )
    {
        $this->nestedSetDataClassRepository = $nestedSetDataClassRepository;
        $this->searchQueryConditionGenerator = $searchQueryConditionGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function countGroups(?Condition $condition = null): int
    {
        return $this->getNestedSetDataClassRepository()->count(
            Group::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function countSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false): int
    {
        return $this->getNestedSetDataClassRepository()->countDescendants($group, $recursiveSubgroups);
    }

    /**
     * @throws \Throwable
     */
    public function createGroup(Group $group): bool
    {
        return $this->getNestedSetDataClassRepository()->create($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return  \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Throwable
     */
    public function deleteGroup(Group $group): ArrayCollection
    {
        return $this->getNestedSetDataClassRepository()->delete($group);
    }

    /**
     * @param string $userIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findDirectlySubscribedGroupNestingValuesForUserIdentifier(string $userIdentifier): ArrayCollection
    {
        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_LEFT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_RIGHT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID));
        $properties->add(new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID));

        $joinConditions = [];

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)
        );

        $joins = new Joins([new Join(GroupRelUser::class, new AndCondition($joinConditions))]);

        $parameters = new StorageParameters(joins: $joins, retrieveProperties: $properties);

        return $this->getNestedSetDataClassRepository()->records(Group::class, $parameters);
    }

    /**
     * @param string $userIdentifier
     *
     * @return ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findDirectlySubscribedGroupsForUserIdentifier(string $userIdentifier): ArrayCollection
    {
        $join = new Join(
            GroupRelUser::class, new AndCondition(
                [
                    new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                        new StaticConditionVariable($userIdentifier)
                    ),

                    new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                        new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)
                    )
                ]
            )
        );

        $joins = new Joins([$join]);

        $parameters = new StorageParameters(joins: $joins);

        return $this->getNestedSetDataClassRepository()->retrieves(Group::class, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupByCode(string $groupCode): ?Group
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), new StaticConditionVariable($groupCode)
        );

        return $this->getNestedSetDataClassRepository()->retrieve(
            Group::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupByCodeAndParentIdentifier(string $groupCode, string $parentIdentifier): ?Group
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), new StaticConditionVariable($groupCode)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        return $this->getNestedSetDataClassRepository()->retrieve(
            Group::class, new StorageParameters(condition: new AndCondition($conditions))
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupByIdentifier(string $groupId): ?Group
    {
        return $this->getNestedSetDataClassRepository()->retrieveById(Group::class, $groupId);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<string[]> $directlySubscribedGroupNestingValues
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupIdentifiersForDirectlySubscribedGroupNestingValues(
        ArrayCollection $directlySubscribedGroupNestingValues
    ): array
    {
        $parameters = new StorageParameters(
            condition: $this->getDirectlySubscribedGroupNestingValuesConditions($directlySubscribedGroupNestingValues),
            retrieveProperties: new RetrieveProperties(
                [new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)]
            )
        );

        return $this->getNestedSetDataClassRepository()->distinct(Group::class, $parameters);
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroups(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $parameters = new StorageParameters(condition: $condition, orderBy: $orderBy, count: $count, offset: $offset);

        return $this->getNestedSetDataClassRepository()->retrieves(Group::class, $parameters);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupsByIdentifiersOrderedByName(array $groupIdentifiers): ArrayCollection
    {
        $orderBy = OrderBy::generate(Group::class, Group::PROPERTY_NAME);

        $condition =
            new InCondition(new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID), $groupIdentifiers);

        return $this->getNestedSetDataClassRepository()->retrieves(
            Group::class, new StorageParameters(
                condition: $condition, orderBy: $orderBy
            )
        );
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<string[]> $directlySubscribedGroupNestingValues
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupsForDirectlySubscribedGroupNestingValues(
        ArrayCollection $directlySubscribedGroupNestingValues
    ): ArrayCollection
    {
        $parameters = new StorageParameters(
            condition: $this->getDirectlySubscribedGroupNestingValuesConditions($directlySubscribedGroupNestingValues)
        );

        return $this->getNestedSetDataClassRepository()->retrieves(Group::class, $parameters);
    }

    /**
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupsForParentIdentifier(string $parentIdentifier = '0'): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        return $this->getNestedSetDataClassRepository()->retrieves(
            Group::class, new StorageParameters(
                condition: $condition, orderBy: new OrderBy(
                [new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))]
            )
            )
        );
    }

    /**
     * @param ?string $searchQuery
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupsForSearchQueryAndParentIdentifier(
        ?string $searchQuery = null, string $parentIdentifier = '0'
    ): ArrayCollection
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
            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        $condition = new AndCondition($conditions);

        return $this->getNestedSetDataClassRepository()->retrieves(
            Group::class, new StorageParameters(
                condition: $condition, orderBy: new OrderBy(
                [new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))]
            )
            )
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findParentGroupIdentifiersForGroup(Group $group, bool $includeSelf = true): array
    {
        return $this->getNestedSetDataClassRepository()->findAncestorIdentifiers($group, $includeSelf);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findParentGroupsForGroup(Group $group, bool $includeSelf = true): ArrayCollection
    {
        return $this->getNestedSetDataClassRepository()->findAncestors($group, $includeSelf);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findRootGroup(): ?Group
    {
        return $this->getNestedSetDataClassRepository()->retrieve(
            Group::class, new StorageParameters(
                condition: new EqualityCondition(
                    new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)
                )
            )
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @todo This could be generalized to the NestedSetDataClassRepository
     */
    public function findSubGroupIdentifiersForGroup(Group $group, bool $recursiveSubgroups = false): array
    {
        if ($recursiveSubgroups)
        {
            $childrenCondition = [];

            $childrenCondition[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_LEFT_VALUE),
                ComparisonCondition::GREATER_THAN, new StaticConditionVariable($group->getLeftValue())
            );

            $childrenCondition[] = new ComparisonCondition(
                new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_RIGHT_VALUE),
                ComparisonCondition::LESS_THAN, new StaticConditionVariable($group->getRightValue())
            );

            $childrenCondition = new AndCondition($childrenCondition);
        }
        else
        {
            $childrenCondition = new EqualityCondition(
                new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
                new StaticConditionVariable($group->getId())
            );
        }

        return $this->getNestedSetDataClassRepository()->distinct(
            Group::class, new StorageParameters(
                condition: $childrenCondition, retrieveProperties: new RetrieveProperties(
                [new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)]
            )
            )
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $recursiveSubgroups
     *
     * @return  \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false): ArrayCollection
    {
        return $this->getNestedSetDataClassRepository()->findDescendants($group, $recursiveSubgroups);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<string[]> $directlySubscribedGroupNestingValues
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    protected function getDirectlySubscribedGroupNestingValuesConditions(
        ArrayCollection $directlySubscribedGroupNestingValues
    ): OrCondition
    {
        $treeConditions = [];
        $alreadyIncludedParents = [];
        $directGroupIds = [];

        foreach ($directlySubscribedGroupNestingValues as $descendent)
        {
            if (!in_array($descendent[NestedSet::PROPERTY_PARENT_ID], $alreadyIncludedParents))
            {

                $treeConditions[] = new AndCondition(
                    [
                        new ComparisonCondition(
                            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_LEFT_VALUE),
                            ComparisonCondition::LESS_THAN_OR_EQUAL,
                            new StaticConditionVariable($descendent[NestedSet::PROPERTY_LEFT_VALUE])
                        ),

                        new ComparisonCondition(
                            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_RIGHT_VALUE),
                            ComparisonCondition::GREATER_THAN_OR_EQUAL,
                            new StaticConditionVariable($descendent[NestedSet::PROPERTY_RIGHT_VALUE])
                        )
                    ]
                );

                $alreadyIncludedParents[] = $descendent[NestedSet::PROPERTY_PARENT_ID];
            }

            $directGroupIds[] = $descendent[DataClass::PROPERTY_ID];
        }

        $treeConditions[] = new InCondition(
            new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID), $directGroupIds
        );

        return new OrCondition($treeConditions);
    }

    /**
     * @param int[] $userGroupIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     */
    protected function getGroupQuotumWithFunctionForUserGroupIdentifiers(int $function, array $userGroupIdentifiers
    ): int
    {
        $condition =
            new InCondition(new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID), $userGroupIdentifiers);

        $parameters = new StorageParameters(
            condition: $condition, retrieveProperties: new RetrieveProperties(
            [
                new FunctionConditionVariable(
                    $function, new PropertyConditionVariable(Group::class, Group::PROPERTY_DISK_QUOTA),
                    Group::PROPERTY_DISK_QUOTA
                )
            ]
        )
        );

        $record = $this->getNestedSetDataClassRepository()->record(Group::class, $parameters);

        return (int) $record[Group::PROPERTY_DISK_QUOTA];
    }

    /**
     * @param int[] $userGroupIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
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
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     */
    public function getLowestGroupQuotumForUserGroupIdentifiers(array $userGroupIdentifiers): int
    {
        return $this->getGroupQuotumWithFunctionForUserGroupIdentifiers(
            FunctionConditionVariable::MIN, $userGroupIdentifiers
        );
    }

    public function getNestedSetDataClassRepository(): NestedSetDataClassRepository
    {
        return $this->nestedSetDataClassRepository;
    }

    public function getSearchQueryConditionGenerator(): SearchQueryConditionGenerator
    {
        return $this->searchQueryConditionGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Throwable
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function moveGroup(Group $group, string $parentGroupIdentifier): bool
    {
        return $this->getNestedSetDataClassRepository()->move($group, $parentGroupIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function updateGroup(Group $group): bool
    {
        return $this->getNestedSetDataClassRepository()->update($group);
    }
}