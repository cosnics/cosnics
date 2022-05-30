<?php
namespace Chamilo\Libraries\Storage\DataClass\Property;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
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
class DataClassProperty implements Hashable
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
        return array(__CLASS__, $this->getPropertyConditionVariable(), $this->getValueConditionVariable());
    }

    public function getPropertyConditionVariable(): ConditionVariable
    {
        return $this->propertyConditionVariable;
    }

    public function setPropertyConditionVariable(ConditionVariable $propertyConditionVariable): DataClassProperty
    {
        $this->propertyConditionVariable = $propertyConditionVariable;

        return $this;
    }

    public function getValueConditionVariable(): ConditionVariable
    {
        return $this->valueConditionVariable;
    }

    public function setValueConditionVariable(ConditionVariable $valueConditionVariable): DataClassProperty
    {
        $this->valueConditionVariable = $valueConditionVariable;

        return $this;
    }

    /**
     * @deprecated Use DataClassProperty::getPropertyConditionVariable() now
     */
    public function get_property(): ConditionVariable
    {
        return $this->getPropertyConditionVariable();
    }

    /**
     * @deprecated Use DataClassProperty::getValueConditionVariable() now
     */
    public function get_value(): ConditionVariable
    {
        return $this->getValueConditionVariable();
    }

    /**
     * @deprecated Use DataClassProperty::setPropertyConditionVariable() now
     */
    public function set_property(ConditionVariable $propertyConditionVariable): DataClassProperty
    {
        return $this->setPropertyConditionVariable($propertyConditionVariable);
    }

    /**
     * @deprecated Use DataClassProperty::setValueConditionVariable() now
     */
    public function set_value(ConditionVariable $valueConditionVariable): DataClassProperty
    {
        return $this->setValueConditionVariable($valueConditionVariable);
    }
}
