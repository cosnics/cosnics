<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * A single property for a data class with the name and the value This class can be used in queries
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdateProperty implements HashableInterface
{

    use HashableTrait;

    private ConditionVariable $propertyConditionVariable;

    private ConditionVariable $valueConditionVariable;

    public function __construct(ConditionVariable $propertyConditionVariable, ConditionVariable $valueConditionVariable)
    {
        $this->propertyConditionVariable = $propertyConditionVariable;
        $this->valueConditionVariable = $valueConditionVariable;
    }

    public function getHashParts(): array
    {
        return [__CLASS__, $this->getPropertyConditionVariable(), $this->getValueConditionVariable()];
    }

    public function getPropertyConditionVariable(): ConditionVariable
    {
        return $this->propertyConditionVariable;
    }

    public function setPropertyConditionVariable(ConditionVariable $propertyConditionVariable): UpdateProperty
    {
        $this->propertyConditionVariable = $propertyConditionVariable;

        return $this;
    }

    public function getValueConditionVariable(): ConditionVariable
    {
        return $this->valueConditionVariable;
    }

    public function setValueConditionVariable(ConditionVariable $valueConditionVariable): UpdateProperty
    {
        $this->valueConditionVariable = $valueConditionVariable;

        return $this;
    }
}
