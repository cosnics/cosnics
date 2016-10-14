<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be> - Added GroupBy
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RecordRetrievesParameters extends DataClassRetrievesParameters
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    private $properties;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\GroupBy
     */
    private $group_by;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $group_by
     */
    public function __construct(DataClassProperties $properties = null, $condition = null, $count = null, $offset = null,
        $order_by = array(), Joins $joins = null, GroupBy $group_by = null)
    {
        parent :: __construct($condition, $count, $offset, $order_by, $joins);

        if(!is_null($properties) && !$properties instanceof DataClassProperties)
        {
            throw new \Exception(
                sprintf(
                    'The given parameter $properties should be of type ' .
                    '\Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties but an object of type %s was given',
                    gettype($properties)
                )
            );
        }

        $this->properties = $properties;
        $this->group_by = $group_by;
    }

    /**
     * Get properties
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     * Set properties
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     */
    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Returns the group by parameter
     *
     * @return \Chamilo\Libraries\Storage\Query\GroupBy
     */
    public function get_group_by()
    {
        return $this->group_by;
    }

    /**
     * Sets the group by parameter
     *
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $group_by
     */
    public function set_group_by(GroupBy $group_by)
    {
        $this->group_by = $group_by;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Parameters\DataClassParameters::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent :: getHashParts();

        $hashParts[] = ($this->get_properties() instanceof DataClassProperties ? $this->get_properties()->getHashParts() : null);
        $hashParts[] = ($this->get_group_by() instanceof GroupBy ? $this->get_group_by()->getHashParts() : null);

        return $hashParts;
    }

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     * @return \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters
     *
     * @throws Exception
     */
    public static function generate($parameter = null)
    {
        // So you think you're being funny, eh? Right back at you ... you dog-blasted, ornery, no-account, long-eared
        // varmint!
        if (is_object($parameter) && $parameter instanceof DataClassRetrievesParameters)
        {
            return $parameter;
        }

        // If the parameter is a Condition, generate a new DataClassRetrievesParameters instance using the Condition
        // provided by the context
        elseif (is_object($parameter) && $parameter instanceof Condition)
        {
            return new self(null, $parameter);
        }

        // If it's an integer, assume it will be the count and generate a new DataClassRetrievesParameters
        elseif (is_integer($parameter))
        {
            return new self(null, null, $parameter);
        }

        // If the parameter is an array, determine whether it's an array of ObjectTableOrder objects and if so generate
        // a DataClassResultParameters
        elseif (is_array($parameter) && count($parameter) > 0 && $parameter[0] instanceof OrderBy)
        {
            return new self(null, null, null, null, $parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof Joins)
        {
            return new self(null, null, null, null, null, $parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof GroupBy)
        {
            return new self(null, null, null, null, null, null, $parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof DataClassProperties)
        {
            return new self($parameter);
        }
        elseif (is_null($parameter))
        {
            return new self();
        }
        else
        {
            throw new Exception('Illegal parameter passed to the DataManager :: retrieves() method.');
        }
    }
}
