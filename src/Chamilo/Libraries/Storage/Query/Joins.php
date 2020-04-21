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
    private $joins;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Join[] $joins
     */
    public function __construct($joins = array())
    {
        $this->joins = (is_array($joins) ? $joins : func_get_args());
    }

    /**
     * Gets the joins
     *
     * @return \Chamilo\Libraries\Storage\Query\Join[]
     */
    public function get()
    {
        return $this->joins;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Join $join
     */
    public function add($join)
    {
        $this->joins[] = $join;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Join $join
     */
    public function prepend($join)
    {
        array_unshift($this->joins, $join);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts()
    {
        $hashes = array();

        foreach ($this->joins as $join)
        {
            $hashes[] = $join->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }

    /**
     *
     * @see \Countable::count()
     */
    public function count()
    {
        return count($this->joins);
    }

    /**
     * Merges the given dataclass properties into this one
     *
     * @param \Chamilo\Libraries\Storage\Query\Joins $joinsToMerge
     */
    public function merge(Joins $joinsToMerge = null)
    {
        if (! $joinsToMerge instanceof Joins)
        {
            return;
        }

        foreach ($joinsToMerge->get() as $join)
        {
            $this->add($join);
        }
    }
}
