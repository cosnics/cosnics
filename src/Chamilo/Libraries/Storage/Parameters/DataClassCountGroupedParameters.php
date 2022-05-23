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
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassCountGroupedParameters extends DataClassParameters
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $dataClassProperties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $havingCondition
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     */
    public function __construct(
        Condition $condition = null, DataClassProperties $dataClassProperties = null, $havingCondition = null,
        Joins $joins = null, GroupBy $groupBy = null
    )
    {
        if (is_null($groupBy))
        {
            $groupBy = new GroupBy($dataClassProperties->get());
        }

        parent::__construct($condition, $joins, $dataClassProperties, null, $groupBy, $havingCondition);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     * @deprecated Use getHavingCondition() now
     */
    public function get_having()
    {
        return $this->getHavingCondition();
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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $havingCondition
     *
     * @deprecated Use setHavingCondition() now
     */
    public function set_having(Condition $havingCondition = null)
    {
        $this->setHavingCondition($havingCondition);
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
