<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class describes a single property for a data class with the name and the value This class can be used in query
 * structures
 *
 * @package Chamilo\Libraries\Storage\DataClass\Property
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

    /**
     * @deprecated Use DataClassUpdateProperty::getPropertyConditionVariable() now
     */
    public function get_property(): ConditionVariable
    {
        return $this->getPropertyConditionVariable();
    }

    /**
     * @deprecated Use DataClassUpdateProperty::getValueConditionVariable() now
     */
    public function get_value(): ConditionVariable
    {
        return $this->getValueConditionVariable();
    }

    /**
     * @deprecated Use DataClassUpdateProperty::setPropertyConditionVariable() now
     */
    public function set_property(ConditionVariable $propertyConditionVariable): UpdateProperty
    {
        return $this->setPropertyConditionVariable($propertyConditionVariable);
    }

    /**
     * @deprecated Use DataClassUpdateProperty::setValueConditionVariable() now
     */
    public function set_value(ConditionVariable $valueConditionVariable): UpdateProperty
    {
        return $this->setValueConditionVariable($valueConditionVariable);
    }
}
