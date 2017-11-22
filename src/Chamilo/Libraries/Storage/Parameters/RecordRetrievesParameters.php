<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

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
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $dataClassProperties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     */
    public function __construct(DataClassProperties $dataClassProperties = null, Condition $condition = null, $count = null,
        $offset = null, $orderBy = array(), Joins $joins = null, GroupBy $groupBy = null)
    {
        DataClassParameters::__construct(
            $condition,
            $joins,
            $dataClassProperties,
            $orderBy,
            $groupBy,
            null,
            $count,
            $offset);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     * @deprecated Use getDataClassProperties() now
     */
    public function get_properties()
    {
        return $this->getDataClassProperties();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     * @deprecated Use setDataClassProperties() now
     */
    public function set_properties(DataClassProperties $dataClassProperties = null)
    {
        $this->setDataClassProperties($dataClassProperties);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\GroupBy
     * @deprecated Use getGroupBy() now
     */
    public function get_group_by()
    {
        return $this->getGroupBy();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     * @deprecated Use setGroupBy() now
     */
    public function set_group_by(GroupBy $groupBy)
    {
        $this->setGroupBy($groupBy);
    }

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     * @return \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters
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
            throw new \Exception('Illegal parameter passed to the DataManager :: retrieves() method.');
        }
    }
}
