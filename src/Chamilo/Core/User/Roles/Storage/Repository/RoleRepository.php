<?php
namespace Chamilo\Core\User\Roles\Storage\Repository;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\RoleRepositoryInterface;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\Repository\DataManagerRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Roles\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RoleRepository extends DataManagerRepository implements RoleRepositoryInterface
{
    protected DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function countRoles(?Condition $condition = null): int
    {
        return $this->getDataClassRepository()->count(Role::class, new DataClassCountParameters(condition: $condition));
    }

    public function createRole(Role $role): bool
    {
        return $this->getDataClassRepository()->create($role);
    }

    public function deleteRole(Role $role): bool
    {
        return $this->getDataClassRepository()->delete($role);
    }

    public function findRoleByName(string $roleName): ?Role
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Role::class, Role::PROPERTY_ROLE), new StaticConditionVariable($roleName)
        );

        return $this->getDataClassRepository()->retrieve(Role::class, new RetrieveParameters(condition: $condition));
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Roles\Storage\DataClass\Role>
     */
    public function findRoles(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            Role::class, new RetrievesParameters(
                condition: $condition, count: $count, offset: $offset, orderBy: $orderBy
            )
        );
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }
}
