<?php
namespace Chamilo\Core\Rights\Structure\Storage\Repository;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocationRole;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRepositoryInterface;
use Chamilo\Libraries\Storage\DataManager\Repository\DataManagerRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage the data of roles
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocationRepository extends DataManagerRepository implements StructureLocationRepositoryInterface
{

    /**
     * Returns a structure location by a given context and action
     * 
     * @param string $context
     * @param string $action
     *
     * @return StructureLocation
     */
    public function findStructureLocationByContextAndAction($context, $action = null)
    {
        $conditions = [];
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(StructureLocation::class, StructureLocation::PROPERTY_CONTEXT), 
            new StaticConditionVariable($context));
        
        $value = ! empty($action) ? new StaticConditionVariable($action) : null;
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(StructureLocation::class, StructureLocation::PROPERTY_ACTION),
            $value
        );

        $condition = new AndCondition($conditions);
        
        return DataManager::retrieve(StructureLocation::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * Truncates the structure locations and roles for the structure locations
     * 
     * @return bool
     */
    public function truncateStructureLocationsAndRoles()
    {
        if (! DataManager::truncate_storage_unit(StructureLocation::getStorageUnitName()))
        {
            return false;
        }
        
        return DataManager::truncate_storage_unit(StructureLocationRole::getStorageUnitName());
    }
}
