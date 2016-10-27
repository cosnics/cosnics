<?php

namespace Chamilo\Core\User\Roles\Storage\Repository;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\DataClass\RoleRelation;
use Chamilo\Core\User\Roles\Storage\DataManager;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\UserRoleRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage the data for the relations between users and roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserRoleRepository implements UserRoleRepositoryInterface
{
    /**
     * Returns a user role relation for a given role id and user id
     *
     * @param int $roleId
     * @param int $userId
     *
     * @return Role
     */
    public function findUserRoleRelationByRoleAndUser($roleId, $userId)
    {
        $conditions = array();

        $conditions[] = $this->getConditionForRole($roleId);
        $conditions[] = $this->getConditionForUser($userId);

        $condition = new AndCondition($conditions);

        return DataManager::retrieve(RoleRelation::class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * Returns a list of roles for a user
     *
     * @param int $userId
     *
     * @return Role[]
     */
    public function findRolesForUser($userId)
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                RoleRelation::class_name(), new EqualityCondition(
                    new PropertyConditionVariable(RoleRelation::class_name(), RoleRelation::PROPERTY_ROLE_ID),
                    new PropertyConditionVariable(Role::class_name(), Role::PROPERTY_ID)
                )
            )
        );

        return DataManager::retrieves(
            Role::class_name(),
            new DataClassRetrievesParameters(
                $this->getConditionForUser($userId), null, null, array(), $joins
            )
        )->as_array();
    }

    /**
     * Returns a list of users by a given role
     *
     * @param int $roleId
     *
     * @return User[]
     */
    public function findUsersForRole($roleId)
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                User::class_name(), new EqualityCondition(
                    new PropertyConditionVariable(RoleRelation::class_name(), RoleRelation::PROPERTY_USER_ID),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID)
                )
            )
        );

        return \Chamilo\Core\User\Storage\DataManager::retrieves(
            User::class_name(),
            new DataClassRetrievesParameters(
                $this->getConditionForRole($roleId), null, null, array(), $joins
            )
        )->as_array();
    }

    /**
     * Builds a condition for the role relation with the property user id
     *
     * @param int $userId
     *
     * @return EqualityCondition
     */
    protected function getConditionForUser($userId)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(RoleRelation::class_name(), RoleRelation::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );
    }

    /**
     * Builds a condition for the role relation with the property role id
     *
     * @param int $roleId
     *
     * @return EqualityCondition
     */
    protected function getConditionForRole($roleId)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(RoleRelation::class_name(), RoleRelation::PROPERTY_ROLE_ID),
            new StaticConditionVariable($roleId)
        );
    }
}
