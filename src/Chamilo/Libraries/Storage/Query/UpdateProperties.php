<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
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
 * @template-implements Collection<TKey,\Chamilo\Libraries\Storage\Query\UpdateProperty>
 * @template-implements Selectable<TKey,\Chamilo\Libraries\Storage\Query\UpdateProperty>
 * @psalm-consistent-constructor
 */
class UpdateProperties extends ArrayCollection implements HashableInterface
{
    use HashableTrait;

    public function getFirst(?UpdateProperty $defaultUpdateProperty = null): ?UpdateProperty
    {
        if (!$this->isEmpty())
        {
            $this->first();

            return $this->current();
        }

        return $defaultUpdateProperty;
    }

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = __CLASS__;

        foreach ($this as $updateProperty)
        {
            $hashParts[] = $updateProperty->getHashParts();
        }

        sort($hashParts);

        return $hashParts;
    }

    public function merge(UpdateProperties $updatePropertiesToMerge): UpdateProperties
    {
        foreach ($updatePropertiesToMerge as $updateProperty)
        {
            $this->add($updateProperty);
        }

        return $this;
    }
}
