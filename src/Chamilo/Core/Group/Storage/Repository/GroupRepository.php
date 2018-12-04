<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
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
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;

/**
 * Dataclass repository for the group application
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupRepository extends CommonDataClassRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    private $searchQueryConditionGenerator;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator $searchQueryConditionGenerator
     */
    public function __construct(
        DataClassRepository $dataClassRepository, SearchQueryConditionGenerator $searchQueryConditionGenerator
    )
    {
        parent::__construct($dataClassRepository);

        $this->searchQueryConditionGenerator = $searchQueryConditionGenerator;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countGroups(Condition $condition = null)
    {
        return $this->getDataClassRepository()->count(Group::class, new DataClassCountParameters($condition));
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

        return $this->getDataClassRepository()->records(Group::class, $parameters);
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

        return $this->dataClassRepository->retrieve(Group::class, new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @param int $groupId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | Group
     */
    public function findGroupByIdentifier($groupId)
    {
        return $this->dataClassRepository->retrieveById(Group::class, $groupId);
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

        return $this->getDataClassRepository()->distinct(Group::class, $parameters);
    }

    /**
     *
     * @param int $groupId
     * @param int $userId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|GroupRelUser
     */
    public function findGroupRelUserByGroupAndUserId($groupId, $userId)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupId)
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            GroupRelUser::class, new DataClassRetrieveParameters($condition, array())
        );
    }

    /**
     * Finds a GroupRelUser object by a given group code and user id
     *
     * @param string $groupCode
     * @param int $userId
     *
     * @return GroupRelUser | DataClass
     */
    public function findGroupRelUserByGroupCodeAndUserId($groupCode, $userId)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), new StaticConditionVariable($groupCode)
        );

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                Group::class, new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)
                )
            )
        );

        return $this->dataClassRepository->retrieve(
            GroupRelUser::class, new DataClassRetrieveParameters($condition, array(), $joins)
        );
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

        return $this->getDataClassRepository()->retrieves(Group::class, $parameters);
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

        return $this->dataClassRepository->retrieves(
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

        return $this->getDataClassRepository()->retrieves(Group::class, $parameters);
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

        return $this->getDataClassRepository()->retrieves(
            Group::class, new DataClassRetrievesParameters(
                $condition, null, null,
                array(new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME)))
            )
        );
    }

    /**
     * @param integer $groupIdentifier
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findSubscribedUserIdentifiersForGroupIdentifier(int $groupIdentifier)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupIdentifier)
        );

        $parameters = new DataClassDistinctParameters(
            $condition, new DataClassProperties(
                array(new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID))
            )
        );

        return $this->getDataClassRepository()->distinct(GroupRelUser::class, $parameters);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
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
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function setDataClassRepository(DataClassRepository $dataClassRepository): void
    {
        $this->dataClassRepository = $dataClassRepository;
    }

}