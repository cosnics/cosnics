<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;
use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdateProperty implements HashableInterface
{
    use HashableTrait;

    private ConditionVariableInterface $propertyConditionVariable;

    private ConditionVariableInterface $valueConditionVariable;

    public function __construct(
        ConditionVariableInterface $propertyConditionVariable, ConditionVariableInterface $valueConditionVariable
    )
    {
        $this->propertyConditionVariable = $propertyConditionVariable;
        $this->valueConditionVariable = $valueConditionVariable;
    }

    public function getHashParts(): array
    {
        return [__CLASS__, $this->getPropertyConditionVariable(), $this->getValueConditionVariable()];
    }

    public function getPropertyConditionVariable(): ConditionVariableInterface
    {
        return $this->propertyConditionVariable;
    }

    public function setPropertyConditionVariable(ConditionVariableInterface $propertyConditionVariable): UpdateProperty
    {
        $this->propertyConditionVariable = $propertyConditionVariable;

        return $this;
    }

    public function getValueConditionVariable(): ConditionVariableInterface
    {
        return $this->valueConditionVariable;
    }

    public function setValueConditionVariable(ConditionVariableInterface $valueConditionVariable): UpdateProperty
    {
        $this->valueConditionVariable = $valueConditionVariable;

        return $this;
    }
}
