<?php

namespace Chamilo\Core\Rights\Structure\Storage\Repository;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocationRole;
use Chamilo\Core\Rights\Structure\Storage\DataManager;
use Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRoleRepositoryInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataManagerRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage the data of roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocationRoleRepository extends DataManagerRepository implements StructureLocationRoleRepositoryInterface
{

    /**
     * Returns a structure location role object by a given structure location and role
     *
     * @param int $structureLocationId
     * @param int $roleId
     *
     * @return StructureLocationRole|\Chamilo\Libraries\Storage\DataClass\DataClass|\Chamilo\Libraries\Storage\DataManager\Ambiguous
     */
    public function findStructureLocationRoleByStructureLocationAndRole($structureLocationId, $roleId)
    {
        $conditions = array();

        $conditions[] = $this->getStructureLocationIdCondition($structureLocationId);
        $conditions[] = $this->getRoleIdCondition($roleId);

        $condition = new AndCondition($conditions);

        return DataManager::retrieve(StructureLocationRole::class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * @return array|bool
     */
    public function retrieveStructureLocationsAndRoles()
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable(StructureLocation::class, StructureLocation::PROPERTY_CONTEXT));
        $properties->add(new PropertyConditionVariable(StructureLocation::class, StructureLocation::PROPERTY_ACTION));
        $properties->add(new PropertyConditionVariable(Role::class, Role::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(Role::class, Role::PROPERTY_ROLE));

        $joins = new Joins();

        $joins->add(
            new Join(
                StructureLocationRole::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        StructureLocationRole::class_name(),
                        StructureLocationRole::PROPERTY_STRUCTURE_LOCATION_ID
                    ),
                    new PropertyConditionVariable(StructureLocation::class_name(), StructureLocation::PROPERTY_ID)
                )
            )
        );

        $joins->add(
            new Join(
                Role::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        StructureLocationRole::class_name(),
                        StructureLocationRole::PROPERTY_ROLE_ID
                    ),
                    new PropertyConditionVariable(Role::class_name(), Role::PROPERTY_ID)
                )
            )
        );

        return DataManager::records(
            StructureLocation::class, new RecordRetrievesParameters($properties, null, null, null, [], $joins)
        )->as_array();
    }

    /**
     * Returns a condition for the structure location id
     *
     * @param int $structureLocationId
     *
     * @return EqualityCondition
     */
    protected function getStructureLocationIdCondition($structureLocationId)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                StructureLocationRole::class_name(),
                StructureLocationRole::PROPERTY_STRUCTURE_LOCATION_ID
            ),
            new StaticConditionVariable($structureLocationId)
        );
    }

    /**
     * Returns a condition for the role id
     *
     * @param int $roleId
     *
     * @return EqualityCondition
     */
    protected function getRoleIdCondition($roleId)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(StructureLocationRole::class_name(), StructureLocationRole::PROPERTY_ROLE_ID),
            new StaticConditionVariable($roleId)
        );
    }
}
