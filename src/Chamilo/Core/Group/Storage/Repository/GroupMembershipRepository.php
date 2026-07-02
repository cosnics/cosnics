<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException;
use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Entity\GroupMembership;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Join;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Joins;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Trait\CommonEntityRepositoryTrait;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Throwable;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMembershipRepository extends EntityRepository
{
    use CommonEntityRepositoryTrait;

    public function __construct(
        EntityManagerInterface $em, ClassMetadata $class, protected QueryBuilderConfigurator $queryBuilderConfigurator
    )
    {
        parent::__construct($em, $class);
    }

    public function countGroupMembershipsByGroupIdentifier(
        Uuid $groupIdentifier, ?ConditionInterface $condition = null
    ): int
    {
        return $this->countGroupMembershipsByGroupIdentifiers([$groupIdentifier], $condition);
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     */
    public function countGroupMembershipsByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null
    ): int
    {
        try {
            $groupCondition = new InCondition(
                new PropertyConditionVariable(
                    Group::class, Group::PROPERTY_IDENTIFIER
                ), new StaticConditionVariable($this->convertUuidsToStrings($groupIdentifiers), ArrayParameterType::STRING)
            );

            if ($condition instanceof ConditionInterface) {
                $condition = new AndCondition([$condition, $groupCondition]);
            }
            else {
                $condition = $groupCondition;
            }

            return $this->countEntities(
                GroupMembership::class, new StorageParameters(
                    condition: $condition, joins: $this->getGroupMembershipJoins(),
                    retrieveProperties: $this->getGroupMembershipRetrieveProperties()
                )
            );
        }
        catch (Throwable) {
            return 0;
        }
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipByGroupCodeAndUserIdentifier(string $groupCode, Uuid $userIdentifier
    ): GroupMembership
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                GroupMembership::class, GroupMembership::PROPERTY_USER_ID
            ), new StaticConditionVariable($userIdentifier, UuidType::NAME)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE),
            new StaticConditionVariable($groupCode, ParameterType::STRING)
        );

        $condition = new AndCondition($conditions);

        try {
            return $this->findEntity(
                GroupMembership::class, new StorageParameters(
                    condition: $condition, joins: $this->getGroupMembershipJoins(),
                    retrieveProperties: $this->getGroupMembershipRetrieveProperties()
                )
            );
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchGroupMembershipException(
                query: $exception->query, message: $exception->getMessage(), code: $exception->getCode(),
                previousException: $exception
            );
        }
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipByGroupIdentifierAndUserIdentifier(Uuid $groupIdentifier, Uuid $userIdentifier
    ): GroupMembership
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupIdentifier, UuidType::NAME)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier, UuidType::NAME)
        );
        $condition = new AndCondition($conditions);

        try {
            return $this->findEntity(
                GroupMembership::class, new StorageParameters(
                    condition: $condition, joins: $this->getGroupMembershipJoins(),
                    retrieveProperties: $this->getGroupMembershipRetrieveProperties()
                )
            );
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchGroupMembershipException(
                query: $exception->query, message: $exception->getMessage(), code: $exception->getCode(),
                previousException: $exception
            );
        }
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function findGroupMembershipByIdentifier(Uuid $identifier): GroupMembership
    {
        try {
            return $this->findEntityByIdentifier(GroupMembership::class, $identifier);
        }
        catch (NoSuchObjectException $exception) {
            throw new NoSuchGroupMembershipException(
                $exception->criteria, $exception->query, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
    }

    /**
     * @return \Symfony\Component\Uid\Uuid[]
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipUserIdentifiersByGroupIdentifier(Uuid $groupIdentifier): array
    {
        return $this->findGroupMembershipUserIdentifiersByGroupIdentifiers([$groupIdentifier]);
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     *
     * @return \Symfony\Component\Uid\Uuid[]
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipUserIdentifiersByGroupIdentifiers(array $groupIdentifiers): array
    {
        $condition = new InCondition(
            $this->getIdentityConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP),
            $groupIdentifiers
        );

        $retrieveProperties = new RetrieveProperties(
            [
                new PropertyConditionVariable(User::class, User::PROPERTY_IDENTIFIER)
            ]
        );

        $joins = new Joins(
            [
                new Join(
                    new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_USER), User::class
                )
            ]
        );

        $parameters =
            new StorageParameters(condition: $condition, joins: $joins, retrieveProperties: $retrieveProperties);

        return $this->distinctEntityProperty(GroupMembership::class, $parameters);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipsByGroupIdentifier(Uuid $groupIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                Group::class, Group::PROPERTY_IDENTIFIER
            ), new StaticConditionVariable($groupIdentifier, UuidType::NAME)
        );

        return $this->findEntities(
            GroupMembership::class, new StorageParameters(
                condition: $condition, joins: $this->getGroupMembershipJoins(),
                retrieveProperties: $this->getGroupMembershipRetrieveProperties()
            )
        );
    }

    /**
     * @param Uuid[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipsByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $groupCondition = new InCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_IDENTIFIER),
            new StaticConditionVariable($this->convertUuidsToStrings($groupIdentifiers), ArrayParameterType::STRING)
        );

        if ($condition instanceof ConditionInterface) {
            $condition = new AndCondition([$condition, $groupCondition]);
        }
        else {
            $condition = $groupCondition;
        }

        return $this->findEntities(
            GroupMembership::class, new StorageParameters(
                condition: $condition, joins: $this->getGroupMembershipJoins(),
                retrieveProperties: $this->getGroupMembershipRetrieveProperties(), orderBy: $orderBy, count: $count,
                offset: $offset
            )
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipsByUserIdentifier(Uuid $userIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                User::class, User::PROPERTY_IDENTIFIER
            ), new StaticConditionVariable($userIdentifier, UuidType::NAME)
        );

        return $this->findEntities(
            GroupMembership::class, new StorageParameters(
                condition: $condition, joins: $this->getGroupMembershipJoins(),
                retrieveProperties: $this->getGroupMembershipRetrieveProperties()
            )
        );
    }

    protected function getGroupMembershipJoins(): Joins
    {
        return new Joins(
            [
                new Join(
                    new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_GROUP), Group::class
                ),
                new Join(
                    new PropertyConditionVariable(GroupMembership::class, GroupMembership::PROPERTY_USER), User::class
                )
            ]
        );
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function getGroupMembershipReference(Uuid $groupIdentifier): GroupMembership
    {
        return $this->getReference(GroupMembership::class, $groupIdentifier);
    }

    protected function getGroupMembershipRetrieveProperties(): RetrieveProperties
    {
        return new RetrieveProperties(
            [
                new PropertiesConditionVariable(GroupMembership::class),
                new PropertiesConditionVariable(Group::class),
                new PropertiesConditionVariable(User::class)
            ]
        );
    }

    public function removeGroupMembership(GroupMembership $groupMembership, bool $flush = true): void
    {
        $this->removeEntity($groupMembership, $flush);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveGroupMembership(GroupMembership $groupMembership, bool $flush = true): void
    {
        $this->saveEntity($groupMembership, $flush);
    }
}