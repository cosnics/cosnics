<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassRetrieveParameters extends DataClassParameters
{

    /**
     * The ordering of the DataClass objects to be applied to the result set
     *
     * @var \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    private $order_by;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     */
    public function __construct($condition = null, $order_by = array(), Joins $joins = null)
    {
        parent :: __construct($condition, $joins);
        $this->order_by = $order_by;
    }

    /**
     * Get the ordering of the DataClass objects to be applied to the result set
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    public function get_order_by()
    {
        return $this->order_by;
    }

    /**
     * Set the ordering of the DataClass objects to be applied to the result set
     *
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     */
    public function set_order_by($order_by)
    {
        $this->order_by = $order_by;
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
            $hash_parts[] = $this->get_order_by();

            $this->set_hash(parent :: hash($hash_parts));
        }

        return $this->get_hash();
    }

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters
     *
     * @throws Exception
     */
    public static function generate($parameter)
    {
        // So you think you're being funny, eh? Right back at you ... you
        // dog-blasted, ornery, no-account, long-eared
        // varmint!
        if (is_object($parameter) && $parameter instanceof DataClassRetrieveParameters)
        {
            return $parameter;
        }

        // If the parameter is a Condition, generate a new
        // DataClassRetrieveParameters instance using the Condition
        // provided by the context
        elseif (is_object($parameter) && $parameter instanceof Condition)
        {
            return new self($parameter);
        }

        // If it's an integer, generate an EqualityCondition using the unique
        // identifier
        elseif (is_numeric($parameter))
        {
            debug_print_backtrace();

            throw new Exception(
                'Please use retrieve_by_id instead of retrieve or retrieves when retrieving a DataClass by it\'s identifier');
        }

        // If the parameter is an array, determine whether it's an array of
        // ObjectTableOrder objects and if so generate
        // a DataClassResultParameters
        elseif (is_array($parameter) && count($parameter) > 0 && $parameter[0] instanceof OrderBy)
        {
            return new self(null, $parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof Joins)
        {
            return new self(null, null, $parameter);
        }
        elseif (is_null($parameter))
        {
            return new self();
        }
        else
        {
            throw new Exception('Illegal parameter passed to the DataManager :: retrieve() method.');
        }
    }
}
