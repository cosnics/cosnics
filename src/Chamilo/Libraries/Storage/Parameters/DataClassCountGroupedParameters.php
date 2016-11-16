<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
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
        parent::__construct($condition, $property, $joins);
        
        if (! is_null($having) && ! $having instanceof Condition)
        {
            throw new \Exception(
                sprintf(
                    'The given parameter $having should be of type ' .
                         '\Chamilo\Libraries\Storage\Query\Condition\Condition but an object of type %s was given', 
                        gettype($having)));
        }
        
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
     * @see \Chamilo\Libraries\Storage\Parameters\DataClassPropertyParameters::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();
        
        $hashParts[] = $this->get_having();
        
        return $hashParts;
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
