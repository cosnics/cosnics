<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements Collection<TKey,\Chamilo\Libraries\Storage\Query\Join>
 * @template-implements Selectable<TKey,\Chamilo\Libraries\Storage\Query\Join>
 * @psalm-consistent-constructor
 */
class Joins extends ArrayCollection implements HashableInterface
{
    use HashableTrait;

    public function getHashParts(): array
    {
        $hashes = [];

        foreach ($this as $join)
        {
            $hashes[] = $join->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }

    public function merge(Joins $joinsToMerge): static
    {
        foreach ($joinsToMerge as $join)
        {
            $this->add($join);
        }

        return $this;
    }
}
