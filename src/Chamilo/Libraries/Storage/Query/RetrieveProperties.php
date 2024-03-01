<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataClass\Property
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements Collection<TKey,\Chamilo\Libraries\Storage\Query\Variable\ConditionVariable>
 * @template-implements Selectable<TKey,\Chamilo\Libraries\Storage\Query\Variable\ConditionVariable>
 * @psalm-consistent-constructor
 */
class RetrieveProperties extends ArrayCollection implements HashableInterface
{
    use HashableTrait;

    public function getFirst(?ConditionVariable $defaultConditionVariable = null): ?ConditionVariable
    {
        if (!$this->isEmpty())
        {
            $this->first();

            return $this->current();
        }

        return $defaultConditionVariable;
    }

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = __CLASS__;

        foreach ($this as $property)
        {
            $hashParts[] = $property->getHashParts();
        }

        sort($hashParts);

        return $hashParts;
    }

    public function merge(RetrieveProperties $retrievePropertiesToMerge): void
    {
        foreach ($retrievePropertiesToMerge as $conditionVariable)
        {
            $this->add($conditionVariable);
        }
    }
}
