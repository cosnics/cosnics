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
class RecordRetrieveParameters extends DataClassRetrieveParameters
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
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $group_by
     */
    public function __construct(DataClassProperties $properties, $condition = null, $order_by = array(), Joins $joins = null,
        GroupBy $group_by = null)
    {
        parent :: __construct($condition, $order_by, $joins);

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
     * @param string[] $hash_parts
     * @return string
     */
    public function hash($hash_parts = array())
    {
        if (! $this->get_hash())
        {
            $hash_parts[] = ($this->get_properties() instanceof DataClassProperties ? $this->get_properties()->hash() : null);
            $hash_parts[] = ($this->get_group_by() instanceof GroupBy ? $this->get_group_by()->hash() : null);

            $this->set_hash(parent :: hash($hash_parts));
        }

        return $this->get_hash();
    }

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     * @return \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters
     *
     * @throws Exception
     */
    public static function generate($parameter = null)
    {
        // So you think you're being funny, eh? Right back at you ... you dog-blasted, ornery, no-account, long-eared
        // varmint!
        if (is_object($parameter) && $parameter instanceof RecordRetrieveParameters)
        {
            return $parameter;
        }

        // If the parameter is a Condition, generate a new DataClassRetrievesParameters instance using the Condition
        // provided by the context
        elseif (is_object($parameter) && $parameter instanceof Condition)
        {
            return new self(null, $parameter);
        }

        // If the parameter is an array, determine whether it's an array of ObjectTableOrder objects and if so generate
        // a DataClassResultParameters
        elseif (is_array($parameter) && count($parameter) > 0 && $parameter[0] instanceof OrderBy)
        {
            return new self(null, null, $parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof Joins)
        {
            return new self(null, null, null, $parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof GroupBy)
        {
            return new self(null, null, null, null, $parameter);
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
