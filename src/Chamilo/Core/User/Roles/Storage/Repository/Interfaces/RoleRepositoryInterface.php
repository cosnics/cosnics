<?php
namespace Chamilo\Core\User\Roles\Storage\Repository\Interfaces;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Repository to manage the data of roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface RoleRepositoryInterface
{
    /**
     * Returns a role by a given name
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function findRoleByName($roleName);

    /**
     * Retrieves the roles
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return Role[]
     */
    public function findRoles(Condition $condition = null, $count = null, $offset = null, $orderBy = array());

    /**
     * Counts the roles
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function countRoles(Condition $condition = null);
}