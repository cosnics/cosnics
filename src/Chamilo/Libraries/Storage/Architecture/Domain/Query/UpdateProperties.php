<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;
use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements Collection<TKey,\Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperty>
 * @template-implements Selectable<TKey,\Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperty>
 * @psalm-consistent-constructor
 */
class UpdateProperties extends ArrayCollection implements HashableInterface
{
    use HashableTrait;

    public function getFirst(?UpdateProperty $defaultUpdateProperty = null): ?UpdateProperty
    {
        if (!$this->isEmpty()) {
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

        foreach ($this as $updateProperty) {
            $hashParts[] = $updateProperty->getHashParts();
        }

        sort($hashParts);

        return $hashParts;
    }

    public function merge(UpdateProperties $updatePropertiesToMerge): UpdateProperties
    {
        foreach ($updatePropertiesToMerge as $updateProperty) {
            $this->add($updateProperty);
        }

        return $this;
    }
}
