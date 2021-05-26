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
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $dataClassProperties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     */
    public function __construct(
        DataClassProperties $dataClassProperties, Condition $condition = null, $orderBy = array(), Joins $joins = null,
        GroupBy $groupBy = null
    )
    {
        DataClassParameters::__construct($condition, $joins, $dataClassProperties, $orderBy, $groupBy);
    }

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     *
     * @return \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters
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
        else
        {
            throw new Exception('Illegal parameter passed to the DataManager::retrieves() method.');
        }
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
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     * @deprecated Use getDataClassProperties() now
     */
    public function get_properties()
    {
        return $this->getDataClassProperties();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     *
     * @deprecated Use setGroupBy() now
     */
    public function set_group_by(GroupBy $groupBy)
    {
        $this->setGroupBy($groupBy);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     *
     * @deprecated Use setDataClassProperties() now
     */
    public function set_properties(DataClassProperties $dataClassProperties = null)
    {
        $this->setDataClassProperties($dataClassProperties);
    }
}
