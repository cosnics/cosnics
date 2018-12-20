<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
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
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;

/**
 * Dataclass repository for the group application
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupRepository
{
    /**
     *
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
     * @return integer
     */
    public function countGroups(Condition $condition = null)
    {
        return $this->getNestedSetDataClassRepository()->count(Group::class, new DataClassCountParameters($condition));
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return boolean
     * @throws \Exception
     */
    public function createGroup(Group $group)
    {
        return $this->getNestedSetDataClassRepository()->create($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     * @throws \Exception
     */
    public function deleteGroup(Group $group)
    {
        return $this->getNestedSetDataClassRepository()->delete($group);
    }

    /**
     * @param integer $userIdentifier
     *
     * @return string[][]
     * @throws \Exception
     */
    public function findDirectlySubscribedGroupNestingValuesForUserIdentifier(int $userIdentifier)
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID));
        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID));

        $joinConditions = array();

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)
        );

        $joins = new Joins(array(new Join(GroupRelUser::class, new AndCondition($joinConditions))));

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
     *
     * @param int $groupId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | Group
     */
    public function findGroupByIdentifier($groupId)
    {
        return $this->getNestedSetDataClassRepository()->retrieveById(Group::class, $groupId);
    }

    /**
     * @param string[][] $directlySubscribedGroupNestingValues
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findGroupIdentifiersForDirectlySubscribedGroupNestingValues(
        DataClassIterator $directlySubscribedGroupNestingValues
    )
    {
        $parameters = new DataClassDistinctParameters(
            $this->getDirectlySubscribedGroupNestingValuesConditions($directlySubscribedGroupNestingValues),
            new DataClassProperties(array(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)))
        );

        return $this->getNestedSetDataClassRepository()->distinct(Group::class, $parameters);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findGroups(
        Condition $condition = null, int $count = null, int $offset = null, array $orderBy = array()
    )
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return $this->getNestedSetDataClassRepository()->retrieves(Group::class, $parameters);
    }

    /**
     * @param $groupIdentifiers
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findGroupsByIdentifiersOrderedByName($groupIdentifiers)
    {
        $orderProperties = array();
        $orderProperties[] = new OrderBy(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME));

        $condition =
            new InCondition(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), $groupIdentifiers);

        return $this->getNestedSetDataClassRepository()->retrieves(
            Group::class, new DataClassRetrievesParameters($condition, null, null, $orderProperties)
        );
    }

    /**
     * @param string[][] $directlySubscribedGroupNestingValues
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findGroupsForDirectlySubscribedGroupNestingValues(
        array $directlySubscribedGroupNestingValues = array()
    )
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getDirectlySubscribedGroupNestingValuesConditions($directlySubscribedGroupNestingValues)
        );

        return $this->getNestedSetDataClassRepository()->retrieves(Group::class, $parameters);
    }

    /**
     * @param string $searchQuery
     * @param integer $parentIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findGroupsForSearchQueryAndParentIdentifier(string $searchQuery = null, int $parentIdentifier = 0)
    {
        $conditions = array();

        if ($searchQuery && $searchQuery != '')
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, array(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME),
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE)
                )
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        $condition = new AndCondition($conditions);

        return $this->getNestedSetDataClassRepository()->retrieves(
            Group::class, new DataClassRetrievesParameters(
                $condition, null, null,
                array(new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME)))
            )
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $recursiveSubgroups
     *
     * @return integer[]
     * @throws \Exception
     * @todo This could be generalized to the NestedSetDataClassRepository
     */
    public function findSubGroupIdentifiersForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        if ($recursiveSubgroups)
        {
            $childrenCondition = array();

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
                new DataClassProperties(array(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)))
            )
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $recursiveSubgroups
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        return $this->getNestedSetDataClassRepository()->findDescendants($group, $recursiveSubgroups);
    }

    /**
     * @param string[][] $directlySubscribedGroupNestingValues
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    protected function getDirectlySubscribedGroupNestingValuesConditions(
        DataClassIterator $directlySubscribedGroupNestingValues
    )
    {
        $treeConditions = array();
        $alreadyIncludedParents = array();
        $directGroupIds = array();

        foreach ($directlySubscribedGroupNestingValues as $descendent)
        {
            if (!in_array($descendent[Group::PROPERTY_PARENT_ID], $alreadyIncludedParents))
            {

                $treeConditions[] = new AndCondition(
                    array(
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
                    )
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
     * @param integer $function
     * @param integer[] $userGroupIdentifiers
     *
     * @return integer
     * @throws \Exception
     */
    protected function getGroupQuotumWithFunctionForUserGroupIdentifiers(int $function, array $userGroupIdentifiers
    ): int
    {
        $condition =
            new InCondition(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), $userGroupIdentifiers);

        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(
                    new FunctionConditionVariable(
                        $function, new PropertyConditionVariable(Group::class, Group::PROPERTY_DISK_QUOTA),
                        Group::PROPERTY_DISK_QUOTA
                    )
                )
            ), $condition
        );

        $record = $this->getNestedSetDataClassRepository()->record(Group::class, $parameters);

        return (int) $record[Group::PROPERTY_DISK_QUOTA];
    }

    /**
     * @param integer[] $userGroupIdentifiers
     *
     * @return integer
     * @throws \Exception
     */
    public function getHighestGroupQuotumForUserGroupIdentifiers(array $userGroupIdentifiers): int
    {
        return $this->getGroupQuotumWithFunctionForUserGroupIdentifiers(
            FunctionConditionVariable::MAX, $userGroupIdentifiers
        );
    }

    /**
     * @param integer[] $userGroupIdentifiers
     *
     * @return integer
     * @throws \Exception
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
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\NestedSetDataClassRepository $nestedSetDataClassRepository
     */
    public function setNestedSetDataClassRepository(NestedSetDataClassRepository $nestedSetDataClassRepository): void
    {
        $this->nestedSetDataClassRepository = $nestedSetDataClassRepository;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    public function getSearchQueryConditionGenerator(): SearchQueryConditionGenerator
    {
        return $this->searchQueryConditionGenerator;
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
     * @param integer $parentGroupIdentifier
     *
     * @return boolean
     * @throws \Exception
     */
    public function moveGroup(Group $group, int $parentGroupIdentifier)
    {
        return $this->getNestedSetDataClassRepository()->move($group, $parentGroupIdentifier);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return boolean
     */
    public function updateGroup(Group $group)
    {
        return $this->getNestedSetDataClassRepository()->update($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $recursiveSubgroups
     *
     * @return integer
     */
    public function countSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        return $this->getNestedSetDataClassRepository()->countDescendants($group, $recursiveSubgroups);
    }
}