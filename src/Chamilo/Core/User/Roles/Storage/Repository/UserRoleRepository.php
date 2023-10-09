<?php
namespace Chamilo\Core\User\Roles\Storage\Repository;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\DataClass\RoleRelation;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\UserRoleRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\Repository\DataManagerRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Roles\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRoleRepository extends DataManagerRepository implements UserRoleRepositoryInterface
{
    protected DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Roles\Storage\DataClass\Role>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRolesForUser(string $userId): ArrayCollection
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                RoleRelation::class, new EqualityCondition(
                    new PropertyConditionVariable(RoleRelation::class, RoleRelation::PROPERTY_ROLE_ID),
                    new PropertyConditionVariable(Role::class, Role::PROPERTY_ID)
                )
            )
        );

        return $this->getDataClassRepository()->retrieves(
            Role::class, new DataClassRetrievesParameters($this->getConditionForUser($userId), null, null, null, $joins)
        );
    }

    public function findUserRoleRelationByRoleAndUser(string $roleId, string $userId): ?RoleRelation
    {
        $conditions = [];

        $conditions[] = $this->getConditionForRole($roleId);
        $conditions[] = $this->getConditionForUser($userId);

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            RoleRelation::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersForRole(string $roleId): ArrayCollection
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                User::class, new EqualityCondition(
                    new PropertyConditionVariable(RoleRelation::class, RoleRelation::PROPERTY_USER_ID),
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID)
                )
            )
        );

        return $this->getDataClassRepository()->retrieves(
            User::class, new DataClassRetrievesParameters($this->getConditionForRole($roleId), null, null, null, $joins)
        );
    }

    protected function getConditionForRole(string $roleId): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(RoleRelation::class, RoleRelation::PROPERTY_ROLE_ID),
            new StaticConditionVariable($roleId)
        );
    }

    protected function getConditionForUser(string $userId): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(RoleRelation::class, RoleRelation::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }
}
