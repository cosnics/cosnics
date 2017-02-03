<?php

namespace Chamilo\Core\Repository\Instance\Storage;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InstanceRepository
{
    /**
     * Returns an instance by a given implementation class
     *
     * @param string $implementation
     *
     * @return Instance
     */
    public function getInstanceByImplementation($implementation)
    {
        if (empty($implementation) || !is_string($implementation))
        {
            throw new \InvalidArgumentException(
                'The given implementation must be a valid string and must not be empty'
            );
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_IMPLEMENTATION),
            new StaticConditionVariable($implementation)
        );

        return DataManager::retrieve(Instance::class_name(), new DataClassRetrieveParameters($condition));
    }
}