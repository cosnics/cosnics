<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Countable;

/**
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Joins implements Countable, Hashable
{
    use HashableTrait;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Join[]
     */
    private array $joins;

    /**
     * @param \Chamilo\Libraries\Storage\Query\Join[] $joins
     */
    public function __construct(array $joins = [])
    {
        $this->joins = $joins;
    }

    public function addJoin(Join $join): Joins
    {
        $this->joins[] = $join;

        return $this;
    }

    public function count(): int
    {
        return count($this->joins);
    }

    public function getHashParts(): array
    {
        $hashes = [];

        foreach ($this->joins as $join)
        {
            $hashes[] = $join->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Join[]
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    public function mergeWith(Joins $joinsToMerge): Joins
    {
        foreach ($joinsToMerge->getJoins() as $join)
        {
            $this->addJoin($join);
        }

        return $this;
    }

    public function prependJoin(Join $join): Joins
    {
        array_unshift($this->joins, $join);

        return $this;
    }
}
