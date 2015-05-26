<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Joins;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassCountGroupedParameters extends DataClassPropertyParameters
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private $having;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $having
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function __construct($condition = null, $property = array(), $having = null, Joins $joins = null)
    {
        parent :: __construct($condition, $property, $joins);
        $this->having = $having;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_having()
    {
        return $this->having;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $having
     */
    public function set_having($having)
    {
        $this->having = $having;
    }

    /**
     *
     * @param string[] $hash_parts
     * @return string
     */
    public function hash($hash_parts = array())
    {
        if (! $this->get_hash())
        {
            $hash_parts[] = $this->get_having();

            $this->set_hash(parent :: hash($hash_parts));
        }

        return $this->get_hash();
    }

    /**
     * Throw an exception if the DataClassPropertyParameters object is invalid
     *
     * @throws \Exception
     */
    public static function invalid()
    {
        throw new Exception('Illegal parameter(s) passed to the DataManager :: count_grouped() method.');
    }
}
