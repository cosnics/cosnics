<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Joins;

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
    public function __construct(DataClassProperties $dataClassProperties, Condition $condition = null, $orderBy = array(),
        Joins $joins = null, GroupBy $groupBy = null)
    {
        DataClassParameters::__construct($condition, $joins, $dataClassProperties, $orderBy, $groupBy);
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
}
