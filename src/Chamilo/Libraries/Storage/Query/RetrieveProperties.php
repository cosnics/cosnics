<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataClass\Property
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RetrieveProperties implements Hashable
{
    use HashableTrait;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    private array $conditionVariables;

    /**
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $conditionVariables
     */
    public function __construct(array $conditionVariables = [])
    {
        $this->conditionVariables = $conditionVariables;
    }

    public function add(ConditionVariable $conditionVariable): RetrieveProperties
    {
        $this->conditionVariables[] = $conditionVariable;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    public function get(): array
    {
        return $this->conditionVariables;
    }

    public function getFirst(): ConditionVariable
    {
        return $this->conditionVariables[0];
    }

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = __CLASS__;

        foreach ($this->get() as $property)
        {
            $hashParts[] = $property->getHashParts();
        }

        sort($hashParts);

        return $hashParts;
    }

    public function merge(RetrieveProperties $retrievePropertiesToMerge)
    {
        foreach ($retrievePropertiesToMerge->get() as $conditionVariable)
        {
            $this->add($conditionVariable);
        }
    }
}
