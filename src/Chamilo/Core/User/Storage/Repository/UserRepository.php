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
 * The repository wrapper for the user data manager
 *
 * @package user
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserRepository implements UserRepositoryInterface
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     * @var \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    private $searchQueryConditionGenerator;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator $searchQueryConditionGenerator
     */
    public function __construct(
        DataClassRepository $dataClassRepository, SearchQueryConditionGenerator $searchQueryConditionGenerator
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->searchQueryConditionGenerator = $searchQueryConditionGenerator;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countUsers(Condition $condition = null)
    {
        return $this->getDataClassRepository()->count(User::class, new DataClassCountParameters($condition));
    }

    /**
     * @param string $searchQuery
     *
     * @return int
     */
    public function countUsersForSearchQuery(string $searchQuery = null)
    {
        return $this->getDataClassRepository()->count(
            User::class, new DataClassCountParameters($this->getUserConditionForSearchQuery($searchQuery))
        );
    }

    /**
     * @param string $searchQuery
     * @param int $userIdentifiers
     *
     * @return int
     */
    public function countUsersForSearchQueryAndUserIdentifiers(
        string $searchQuery = null, array $userIdentifiers = []
    )
    {
        return $this->getDataClassRepository()->count(
            User::class, new DataClassCountParameters(
                $this->getUserConditionForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers)
            )
        );
    }

    public function create(DataClass $dataClass)
    {
        return $dataClass->create();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     * @throws \Exception
     */
    public function createUser(User $user)
    {
        return $this->getDataClassRepository()->create($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return bool
     * @throws \Exception
     */
    public function createUserSetting(UserSetting $userSetting)
    {
        return $this->getDataClassRepository()->create($userSetting);
    }

    public function delete(DataClass $dataClass)
    {
        return $dataClass->delete();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function deleteUser(User $user)
    {
        // TODO: $user->delete() still implements some business logic
        // return $this->getDataClassRepository()->delete($user);
        return $user->delete();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return bool
     */
    public function deleteUserSetting(UserSetting $userSetting)
    {
        return $this->getDataClassRepository()->delete($userSetting);
    }

    /**
     * @param int $status
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findActiveUsersByStatus($status)
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
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findPlatformAdministrators()
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
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_ID),
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

    /**
     * @param string $email
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByEmail($email)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL), new StaticConditionVariable($email)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param int $id
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findUserByIdentifier($id)
    {
        return $this->getDataClassRepository()->retrieveById(User::class, $id);
    }

    /**
     * @param string $officialCode
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByOfficialCode($officialCode)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
            new StaticConditionVariable($officialCode)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param string $securityToken
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserBySecurityToken($securityToken)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_SECURITY_TOKEN),
            new StaticConditionVariable($securityToken)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param string $username
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsername($username)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), new StaticConditionVariable($username)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param string $usernameOrEmail
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
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
     * @return int
     * @throws \Exception
     */
    public function findUserIdentifiers()
    {
        $retrieveProperties = new RetrieveProperties();
        $retrieveProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_ID));

        return $this->getDataClassRepository()->distinct(
            User::class, new DataClassDistinctParameters(null, $retrieveProperties)
        );
    }

    /**
     * @param string[] $officialCodes
     *
     * @return int[]
     * @throws \Exception
     */
    public function findUserIdentifiersByOfficialCodes(array $officialCodes)
    {
        $condition =
            new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE), $officialCodes);

        return $this->getDataClassRepository()->distinct(
            User::class, new DataClassDistinctParameters(
                $condition, new RetrieveProperties(
                    array(
                        new PropertyConditionVariable(
                            User::class, User::PROPERTY_ID
                        )
                    )
                )
            )
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
     * @param int $userIdentifiers
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersByIdentifiersOrderedByName($userIdentifiers)
    {
        $orderBy = new OrderBy();

        $orderBy->add(new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)));
        $orderBy->add(new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME)));

        $condition = new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $userIdentifiers);

        return $this->getDataClassRepository()->retrieves(
            User::class, new DataClassRetrievesParameters($condition, null, null, $orderBy)
        );
    }

    /**
     * @param string $searchQuery
     * @param int $offset
     * @param int $count
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersForSearchQuery(
        string $searchQuery = null, int $offset = null, int $count = null
    )
    {
        $orderProperties = array(
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME))
        );

        $parameters = new DataClassRetrievesParameters(
            $this->getUserConditionForSearchQuery($searchQuery), $count, $offset, new OrderBy($orderProperties)
        );

        return $this->getDataClassRepository()->retrieves(User::class, $parameters);
    }

    /**
     * @param string $searchQuery
     * @param int $userIdentifiers
     * @param int $offset
     * @param int $count
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersForSearchQueryAndUserIdentifiers(
        string $searchQuery = null, array $userIdentifiers = [], int $offset = null, int $count = null
    )
    {
        $orderProperties = array(
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME))
        );

        $parameters = new DataClassRetrievesParameters(
            $this->getUserConditionForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers), $count, $offset,
            new OrderBy($orderProperties)
        );

        return $this->getDataClassRepository()->retrieves(User::class, $parameters);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
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

    protected function getUserConditionForSearchQuery(string $searchQuery = null)
    {
        $conditions = [];

        // Set the conditions for the search query
        if ($searchQuery && $searchQuery != '')
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, array(
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)
                )
            );
        }

        // Only include active users
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), new StaticConditionVariable(1)
        );

        return new AndCondition($conditions);
    }

    /**
     * @param string $searchQuery
     * @param int $userIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getUserConditionForSearchQueryAndUserIdentifiers(
        string $searchQuery = null, array $userIdentifiers = []
    )
    {
        $conditions = [];

        $conditions[] = $this->getUserConditionForSearchQuery($searchQuery);
        $conditions[] =
            new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $userIdentifiers);

        return new AndCondition($conditions);
    }

    /**
     * @param \Chamilo\Configuration\Storage\DataClass\Setting $setting
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\User\Storage\DataClass\UserSetting
     */
    public function getUserSettingForSettingAndUser(Setting $setting, User $user)
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

    public function update(DataClass $dataClass)
    {
        return $dataClass->update();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function updateUser(User $user)
    {
        return $this->getDataClassRepository()->update($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return bool
     */
    public function updateUserSetting(UserSetting $userSetting)
    {
        return $this->getDataClassRepository()->update($userSetting);
    }
}