<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
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
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\CaseConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository implements UserRepositoryInterface
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

    public function countUsers(?Condition $condition = null): int
    {
        return $this->getDataClassRepository()->count(User::class, new DataClassCountParameters($condition));
    }

    public function countUsersForSearchQuery(?string $searchQuery = null): int
    {
        return $this->getDataClassRepository()->count(
            User::class, new DataClassCountParameters($this->getUserConditionForSearchQuery($searchQuery))
        );
    }

    /**
     * @param string[] $userIdentifiers
     */
    public function countUsersForSearchQueryAndUserIdentifiers(
        ?string $searchQuery = null, array $userIdentifiers = []
    ): int
    {
        return $this->getDataClassRepository()->count(
            User::class, new DataClassCountParameters(
                $this->getUserConditionForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers)
            )
        );
    }

    /**
     * @throws \Exception
     * @deprecated Use dedicated create-methods in the UserRepository instead
     */
    public function create(DataClass $dataClass): bool
    {
        return $dataClass->create();
    }

    public function createUser(User $user): bool
    {
        return $this->getDataClassRepository()->create($user);
    }

    public function createUserSetting(UserSetting $userSetting): bool
    {
        return $this->getDataClassRepository()->create($userSetting);
    }

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Use dedicated delete-methods in the UserRepository instead
     */
    public function delete(DataClass $dataClass): bool
    {
        return $dataClass->delete();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     */
    public function deleteUser(User $user): bool
    {
        // TODO: $user->delete() still implements some business logic
        // return $this->getDataClassRepository()->delete($user);
        return $user->delete();
    }

    public function deleteUserSetting(UserSetting $userSetting): bool
    {
        return $this->getDataClassRepository()->delete($userSetting);
    }

    public function deleteUserSettingsForSettingIdentifier(string $settingIdentifier): bool
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID),
            new StaticConditionVariable($settingIdentifier)
        );

        return $this->getDataClassRepository()->deletes(UserSetting::class, $condition);
    }

    /**
     * @param int $status
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findActiveUsersByStatus(int $status): ArrayCollection
    {
        $conditions = [];
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_STATUS), ComparisonCondition::EQUAL,
            new StaticConditionVariable($status)
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), ComparisonCondition::EQUAL,
            new StaticConditionVariable(1)
        );

        return $this->getDataClassRepository()->retrieves(
            User::class, new DataClassRetrievesParameters(new AndCondition($conditions))
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findPlatformAdministrators(): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_PLATFORMADMIN), new StaticConditionVariable(1)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), new StaticConditionVariable(1)
        );

        return $this->getDataClassRepository()->retrieves(
            User::class, new DataClassRetrievesParameters(new AndCondition($conditions))
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findSettingsForUser(User $user): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_USER_SETTING),
            new StaticConditionVariable(1)
        );

        $joinConditions = [];
        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );
        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, DataClass::PROPERTY_ID),
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID)
        );

        $join = new Join(UserSetting::class, new AndCondition($joinConditions), Join::TYPE_LEFT);

        $retrieveProperties = [];
        $retrieveProperties[] = new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID);
        $retrieveProperties[] = new PropertyConditionVariable(Setting::class, Setting::PROPERTY_CONTEXT);
        $retrieveProperties[] = new PropertyConditionVariable(Setting::class, Setting::PROPERTY_VARIABLE);

        $caseElements = [];
        $caseElements[] = new CaseElementConditionVariable(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_VALUE), new OrCondition(
                [
                    new EqualityCondition(
                        new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_VALUE), null
                    ),
                    new EqualityCondition(
                        new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_VALUE),
                        new StaticConditionVariable('')
                    )
                ]
            )
        );
        $caseElements[] = new CaseElementConditionVariable(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_VALUE)
        );

        $retrieveProperties[] = new CaseConditionVariable($caseElements, UserSetting::PROPERTY_VALUE);

        return $this->getDataClassRepository()->records(
            Setting::class, new RecordRetrievesParameters(
                new RetrieveProperties($retrieveProperties), $condition, null, null, null, new Joins([$join])
            )
        );
    }

    public function findUserByEmail($email): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL), new StaticConditionVariable($email)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    public function findUserByIdentifier(string $identifier): ?User
    {
        return $this->getDataClassRepository()->retrieveById(User::class, $identifier);
    }

    public function findUserByOfficialCode(string $officialCode): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
            new StaticConditionVariable($officialCode)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    public function findUserBySecurityToken(string $securityToken): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_SECURITY_TOKEN),
            new StaticConditionVariable($securityToken)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    public function findUserByUsername(string $username): ?User
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), new StaticConditionVariable($username)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

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
            User::class, new DataClassRetrieveParameters(new OrCondition($conditions))
        );
    }

    /**
     * @return string[]
     */
    public function findUserIdentifiers(): array
    {
        $retrieveProperties = new RetrieveProperties();
        $retrieveProperties->add(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID));

        return $this->getDataClassRepository()->distinct(
            User::class, new DataClassDistinctParameters(null, $retrieveProperties)
        );
    }

    /**
     * @param string[] $officialCodes
     *
     * @return string[]
     */
    public function findUserIdentifiersByOfficialCodes(array $officialCodes): array
    {
        $condition =
            new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE), $officialCodes);

        return $this->getDataClassRepository()->distinct(
            User::class, new DataClassDistinctParameters(
                $condition, new RetrieveProperties(
                    [
                        new PropertyConditionVariable(
                            User::class, DataClass::PROPERTY_ID
                        )
                    ]
                )
            )
        );
    }

    public function findUserSettingForSettingAndUser(Setting $setting, User $user): ?UserSetting
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID),
            new StaticConditionVariable($setting->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            UserSetting::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsers(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return $this->getDataClassRepository()->retrieves(User::class, $parameters);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersByIdentifiersOrderedByName(array $userIdentifiers): ArrayCollection
    {
        $orderBy = new OrderBy();

        $orderBy->add(new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)));
        $orderBy->add(new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME)));

        $condition =
            new InCondition(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers);

        return $this->getDataClassRepository()->retrieves(
            User::class, new DataClassRetrievesParameters($condition, null, null, $orderBy)
        );
    }

    /**
     * @param ?string $searchQuery
     * @param ?int $offset
     * @param ?int $count
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersForSearchQuery(
        ?string $searchQuery = null, ?int $offset = null, ?int $count = null
    ): ArrayCollection
    {
        $orderProperties = [
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME))
        ];

        $parameters = new DataClassRetrievesParameters(
            $this->getUserConditionForSearchQuery($searchQuery), $count, $offset, new OrderBy($orderProperties)
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersForSearchQueryAndUserIdentifiers(
        ?string $searchQuery = null, array $userIdentifiers = [], ?int $offset = null, ?int $count = null
    ): ArrayCollection
    {
        $orderProperties = [
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME))
        ];

        $parameters = new DataClassRetrievesParameters(
            $this->getUserConditionForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers), $count, $offset,
            new OrderBy($orderProperties)
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

    protected function getUserConditionForSearchQuery(string $searchQuery = null): AndCondition
    {
        $conditions = [];

        // Set the conditions for the search query
        if ($searchQuery && $searchQuery != '')
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, [
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)
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

    public function setSearchQueryConditionGenerator(SearchQueryConditionGenerator $searchQueryConditionGenerator): void
    {
        $this->searchQueryConditionGenerator = $searchQueryConditionGenerator;
    }

    /**
     * @throws \Exception
     * @deprecated Use dedicated update-methods in the UserRepository instead
     */
    public function update(DataClass $dataClass): bool
    {
        return $dataClass->update();
    }

    public function updateUser(User $user): bool
    {
        return $this->getDataClassRepository()->update($user);
    }

    public function updateUserSetting(UserSetting $userSetting): bool
    {
        return $this->getDataClassRepository()->update($userSetting);
    }
}