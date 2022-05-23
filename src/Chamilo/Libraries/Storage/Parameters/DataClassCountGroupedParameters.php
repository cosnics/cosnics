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

    public function __construct(
        ?Condition $condition = null, ?DataClassProperties $dataClassProperties = null,
        ?Condition $havingCondition = null, ?Joins $joins = null, ?GroupBy $groupBy = null
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
     * @deprecated Use getHavingCondition() now
     */
    public function get_having(): ?Condition
    {
        return $this->getHavingCondition();
    }

    /**
     *
     * @deprecated Use getDataClassProperties() now
     */
    public function get_properties(): ?DataClassProperties
    {
        return $this->getDataClassProperties();
    }

    /**
     * @deprecated Use setHavingCondition() now
     */
    public function set_having(?Condition $havingCondition = null)
    {
        $this->setHavingCondition($havingCondition);
    }

    /**
     * @deprecated Use setDataClassProperties() now
     */
    public function set_properties(?DataClassProperties $dataClassProperties = null)
    {
        $this->setDataClassProperties($dataClassProperties);
    }
}
