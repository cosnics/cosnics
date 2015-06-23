<?php
namespace Chamilo\Libraries\Storage\Query;

/**
 *
 * @package Chamilo\Libraries\Storage\Query$Joins
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Joins implements \Countable
{

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
     * @return string
     */
    public function hash()
    {
        $hashes = array();

        foreach ($this->joins as $join)
        {
            $hashes[] = $join->hash();
        }

        sort($hashes);

        return md5(serialize($hashes));
    }

    /**
     *
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->joins);
    }
}
