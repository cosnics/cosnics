<?php
namespace Chamilo\Core\User\Roles\Storage\Repository\Interfaces;

use Chamilo\Core\User\Roles\Storage\DataClass\RoleRelation;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Roles\Storage\Repository\Interfaces
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserRoleRepositoryInterface extends DataManagerRepositoryInterface
{

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Roles\Storage\DataClass\Role>
     */
    public function findRolesForUser(string $userId): ArrayCollection;

    public function findUserRoleRelationByRoleAndUser(string $roleId, string $userId): ?RoleRelation;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     */
    public function findUsersForRole(string $roleId): ArrayCollection;
}