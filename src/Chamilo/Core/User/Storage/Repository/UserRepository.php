<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\ComparisonTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository
{
    private DataClassRepository $dataClassRepository;

    private SearchQueryConditionGenerator $searchQueryConditionGenerator;

    public function __construct(
        DataClassRepository $dataClassRepository, SearchQueryConditionGenerator $searchQueryConditionGenerator
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->searchQueryConditionGenerator = $searchQueryConditionGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsers(?ConditionInterface $condition = null): int
    {
        return $this->getDataClassRepository()->count(User::class, new StorageParameters(condition: $condition));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsersForSearchQuery(?string $searchQuery = null): int
    {
        return $this->getDataClassRepository()->count(
            User::class, new StorageParameters(condition: $this->getUserConditionForSearchQuery($searchQuery))
        );
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsersForSearchQueryAndUserIdentifiers(
        ?string $searchQuery = null, array $userIdentifiers = []
    ): int
    {
        return $this->getDataClassRepository()->count(
            User::class, new StorageParameters(
                condition: $this->getUserConditionForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers)
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createUser(User $user): bool
    {
        return $this->getDataClassRepository()->create($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteUser(User $user): bool
    {
        return $this->getDataClassRepository()->delete($user);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findActiveUsers(
        ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $conditions = [];

        if ($condition) {
            $conditions[] = $condition;
        }

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), ComparisonTypeEnum::EQUAL,
            new StaticConditionVariable(1)
        );

        return $this->getDataClassRepository()->retrieves(
            User::class, new StorageParameters(
                condition: new AndCondition($conditions), orderBy: $orderBy, count: $count, offset: $offset
            )
        );
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findEmailAddressesForUserIdentifiers(array $userIdentifiers): array
    {
        $condition = new InCondition(
            new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers
        );

        $retrieveProperties = [new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL)];

        return $this->findUserProperties($retrieveProperties, $condition);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findPlatformAdministrators(): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_PLATFORM_ADMINISTRATOR),
            new StaticConditionVariable(1)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), new StaticConditionVariable(1)
        );

        return $this->getDataClassRepository()->retrieves(
            User::class, new StorageParameters(condition: new AndCondition($conditions))
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByEmail($email): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL), new StaticConditionVariable($email)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new StorageParameters(condition: $condition));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByIdentifier(string $identifier): ?User
    {
        return $this->getDataClassRepository()->retrieveById(User::class, $identifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByOfficialCode(string $officialCode): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
            new StaticConditionVariable($officialCode)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new StorageParameters(condition: $condition));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserBySecurityToken(string $securityToken): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_SECURITY_TOKEN),
            new StaticConditionVariable($securityToken)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new StorageParameters(condition: $condition));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByUsername(string $username): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), new StaticConditionVariable($username)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new StorageParameters(condition: $condition));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL),
            new StaticConditionVariable($usernameOrEmail)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
            new StaticConditionVariable($usernameOrEmail)
        );

        return $this->getDataClassRepository()->retrieve(
            User::class, new StorageParameters(condition: new OrCondition($conditions))
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUserIdentifiers(): array
    {
        $retrieveProperties = new RetrieveProperties();
        $retrieveProperties->add(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID));

        return $this->getDataClassRepository()->distinct(
            User::class, new StorageParameters(retrieveProperties: $retrieveProperties)
        );
    }

    /**
     * @param string[] $officialCodes
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUserIdentifiersByOfficialCodes(array $officialCodes): array
    {
        $condition =
            new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE), $officialCodes);

        return $this->getDataClassRepository()->distinct(
            User::class, new StorageParameters(
                condition: $condition, retrieveProperties: new RetrieveProperties(
                [
                    new PropertyConditionVariable(
                        User::class, DataClass::PROPERTY_ID
                    )
                ]
            )
            )
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface[] $retrieveProperties
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUserProperties(
        array $retrieveProperties, ?ConditionInterface $condition = null, OrderBy $orderBy = new OrderBy()
    ): array
    {
        return $this->getDataClassRepository()->distinct(
            User::class, new StorageParameters(
                condition: $condition, retrieveProperties: new RetrieveProperties($retrieveProperties),
                orderBy: $orderBy
            )
        );
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface $condition
     * @param ?int $count
     * @param ?int $offset
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsers(
        ?ConditionInterface $condition = null, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        $parameters = new StorageParameters(condition: $condition, orderBy: $orderBy, count: $count, offset: $offset);

        return $this->getDataClassRepository()->retrieves(User::class, $parameters);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersByIdentifiers(array $userIdentifiers, OrderBy $orderBy = new OrderBy()): ArrayCollection
    {
        $condition =
            new InCondition(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers);

        return $this->getDataClassRepository()->retrieves(
            User::class, new StorageParameters(
                condition: $condition, orderBy: $orderBy
            )
        );
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersByIdentifiersOrderedByName(array $userIdentifiers): ArrayCollection
    {
        $orderBy = new OrderBy();

        $orderBy->add(new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)));
        $orderBy->add(new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME)));

        return $this->findUsersByIdentifiers($userIdentifiers, $orderBy);
    }

    /**
     * @param ?string $searchQuery
     * @param ?int $offset
     * @param ?int $count
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersForSearchQuery(
        ?string $searchQuery = null, ?int $offset = null, ?int $count = null
    ): ArrayCollection
    {
        $orderProperties = [
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)),
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME))
        ];

        $parameters = new StorageParameters(
            condition: $this->getUserConditionForSearchQuery($searchQuery), orderBy: new OrderBy($orderProperties),
            count: $count, offset: $offset
        );

        return $this->getDataClassRepository()->retrieves(User::class, $parameters);
    }

    /**
     * @param ?string $searchQuery
     * @param string[] $userIdentifiers
     * @param ?int $offset
     * @param ?int $count
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersForSearchQueryAndUserIdentifiers(
        ?string $searchQuery = null, array $userIdentifiers = [], ?int $offset = null, ?int $count = null
    ): ArrayCollection
    {
        $orderProperties = [
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)),
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME))
        ];

        $parameters = new StorageParameters(
            condition: $this->getUserConditionForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers),
            orderBy: new OrderBy($orderProperties), count: $count, offset: $offset
        );

        return $this->getDataClassRepository()->retrieves(User::class, $parameters);
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    public function getSearchQueryConditionGenerator(): SearchQueryConditionGenerator
    {
        return $this->searchQueryConditionGenerator;
    }

    public function setSearchQueryConditionGenerator(SearchQueryConditionGenerator $searchQueryConditionGenerator): void
    {
        $this->searchQueryConditionGenerator = $searchQueryConditionGenerator;
    }

    protected function getUserConditionForSearchQuery(string $searchQuery = null): AndCondition
    {
        $conditions = [];

        // Set the conditions for the search query
        if ($searchQuery && $searchQuery != '') {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, [
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)
                ]
            );
        }

        // Only include active users
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), new StaticConditionVariable(1)
        );

        return new AndCondition($conditions);
    }

    /**
     * @param string[] $userIdentifiers
     */
    protected function getUserConditionForSearchQueryAndUserIdentifiers(
        ?string $searchQuery = null, array $userIdentifiers = []
    ): AndCondition
    {
        $conditions = [];

        $conditions[] = $this->getUserConditionForSearchQuery($searchQuery);
        $conditions[] =
            new InCondition(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers);

        return new AndCondition($conditions);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUser(User $user): bool
    {
        return $this->getDataClassRepository()->update($user);
    }
}