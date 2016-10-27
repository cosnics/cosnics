<?php

namespace Chamilo\Core\User\Roles\Storage\Repository;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\DataManager;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\RoleRepositoryInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage the data of roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Returns a role by a given name
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function findRoleByName($roleName)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Role::class_name(), Role::PROPERTY_ROLE),
            new StaticConditionVariable(
                $roleName
            )
        );

        return DataManager::retrieve(Role::class_name(), new DataClassRetrieveParameters($condition));
    }
}
