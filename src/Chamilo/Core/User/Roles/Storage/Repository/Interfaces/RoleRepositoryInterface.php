<?php
namespace Chamilo\Core\User\Roles\Storage\Repository\Interfaces;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Roles\Storage\Repository\Interfaces
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface RoleRepositoryInterface extends DataManagerRepositoryInterface
{

    public function countRoles(?Condition $condition = null): int;

    public function createRole(Role $role): bool;

    public function deleteRole(Role $role);

    public function findRoleByName(string $roleName): ?Role;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Roles\Storage\DataClass\Role>
     */
    public function findRoles(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection;
}