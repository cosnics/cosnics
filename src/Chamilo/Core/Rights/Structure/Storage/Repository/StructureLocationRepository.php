<?php

namespace Chamilo\Core\Rights\Structure\Storage\Repository;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\DataManager;
use Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRepositoryInterface;
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
class StructureLocationRepository implements StructureLocationRepositoryInterface
{
    /**
     * Returns a structure location by a given context and component
     *
     * @param string $context
     * @param string $component
     *
     * @return StructureLocation
     */
    public function findStructureLocationByContextAndComponent($context, $component = null)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(StructureLocation::class_name(), StructureLocation::PROPERTY_CONTEXT),
            new StaticConditionVariable($context)
        );

        if(!empty($component))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(StructureLocation::class_name(), StructureLocation::PROPERTY_COMPONENT),
                new StaticConditionVariable($component)
            );
        }

        $condition = new AndCondition($conditions);

        return DataManager::retrieve(StructureLocation::class_name(), new DataClassRetrieveParameters($condition));
    }
}
