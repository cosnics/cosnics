<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Dataclass repository for the group application
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupMembershipRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param int $groupIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     * @throws \Exception
     */
    public function countSubscribedUsersForGroupIdentifier(int $groupIdentifier, ?Condition $condition = null): int
    {
        return $this->countSubscribedUsersForGroupIdentifiers([$groupIdentifier], $condition);
    }

    /**
     * @param int[] $groupIdentifiers
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countSubscribedUsersForGroupIdentifiers(array $groupIdentifiers, ?Condition $condition = null): int
    {
        $groupCondition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupIdentifiers
        );

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition([$condition, $groupCondition]);
        }
        else
        {
            $condition = $groupCondition;
        }

        $joins = new Joins(
            [
                new Join(
                    GroupRelUser::class, new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                        new PropertyConditionVariable(SubscribedUser::class, SubscribedUser::PROPERTY_ID)
                    )
                )
            ]
        );

        return $this->getDataClassRepository()->count(
            SubscribedUser::class, new DataClassCountParameters($condition, $joins)
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\GroupRelUser $groupUserRelation
     *
     * @return bool
     * @throws \Exception
     */
    public function createGroupUserRelation(GroupRelUser $groupUserRelation)
    {
        return $this->dataClassRepository->create($groupUserRelation);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\GroupRelUser $groupUserRelation
     *
     * @return bool
     */
    public function deleteGroupUserRelation(GroupRelUser $groupUserRelation)
    {
        return $this->dataClassRepository->delete($groupUserRelation);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return bool
     */
    public function emptyGroup(Group $group)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->getId())
        );

        return $this->getDataClassRepository()->deletes(GroupRelUser::class, $condition);
    }

    /**
     * @param int $groupId
     * @param int $userId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|GroupRelUser
     */
    public function findGroupRelUserByGroupAndUserId($groupId, $userId)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupId)
        );

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            GroupRelUser::class, new DataClassRetrieveParameters($condition, null)
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
        $conditions = [];

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

        return $this->getDataClassRepository()->retrieve(
            GroupRelUser::class, new DataClassRetrieveParameters($condition, null, $joins)
        );
    }

    /**
     * @param int $groupIdentifier
     *
     * @return int[]
     * @throws \Exception
     */
    public function findSubscribedUserIdentifiersForGroupIdentifier(int $groupIdentifier)
    {
        return $this->findSubscribedUserIdentifiersForGroupIdentifiers([$groupIdentifier]);
    }

    /**
     * @param int[] $groupIdentifiers
     *
     * @return int[]
     * @throws \Exception
     */
    public function findSubscribedUserIdentifiersForGroupIdentifiers(array $groupIdentifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupIdentifiers
        );

        $parameters = new DataClassDistinctParameters(
            $condition, new RetrieveProperties(
                [new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID)]
            )
        );

        return $this->getDataClassRepository()->distinct(GroupRelUser::class, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findSubscribedUsersForGroupIdentifiers(
        array $groupIdentifiers, ?Condition $condition = null, ?int $offset = null, ?int $count = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $groupCondition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupIdentifiers
        );

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition([$condition, $groupCondition]);
        }
        else
        {
            $condition = $groupCondition;
        }

        $joins = new Joins(
            [
                new Join(
                    GroupRelUser::class, new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                        new PropertyConditionVariable(SubscribedUser::class, SubscribedUser::PROPERTY_ID)
                    )
                )
            ]
        );

        $retrieveProperties = new RetrieveProperties(
            [
                new PropertiesConditionVariable(SubscribedUser::class),
                new PropertyConditionVariable(
                    GroupRelUser::class, DataClass::PROPERTY_ID, SubscribedUser::PROPERTY_RELATION_ID
                ),
                new PropertyConditionVariable(
                    GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID, SubscribedUser::PROPERTY_GROUP_ID
                )
            ]
        );

        return $this->getDataClassRepository()->retrieves(
            SubscribedUser::class, new DataClassRetrievesParameters(
                $condition, $count, $offset, $orderBy, $joins, null, null, $retrieveProperties
            )
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function setDataClassRepository(DataClassRepository $dataClassRepository): void
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param int $groupsIdentifiers
     *
     * @return bool
     */
    public function unsubscribeUsersFromGroupIdentifiers(array $groupsIdentifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groupsIdentifiers
        );

        return $this->getDataClassRepository()->deletes(GroupRelUser::class, $condition);
    }
}