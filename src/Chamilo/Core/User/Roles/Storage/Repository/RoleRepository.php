<?php
namespace Chamilo\Core\User\Roles\Storage\Repository;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\DataManager;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\RoleRepositoryInterface;
use Chamilo\Libraries\Storage\DataManager\Repository\DataManagerRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage the data of roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RoleRepository extends DataManagerRepository implements RoleRepositoryInterface
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
            new PropertyConditionVariable(Role::class, Role::PROPERTY_ROLE),
            new StaticConditionVariable($roleName));

        return DataManager::retrieve(Role::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieves the roles
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param $orderBy
     *
     * @return Role[]
     */
    public function findRoles(Condition $condition = null, $count = null, $offset = null, $orderBy = null)
    {
        return DataManager::retrieves(
            Role::class,
            new DataClassRetrievesParameters($condition, $count, $offset, $orderBy));
    }

    /**
     * Counts the roles
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function countRoles(Condition $condition = null)
    {
        return DataManager::count(Role::class, new DataClassCountParameters($condition));
    }
}
