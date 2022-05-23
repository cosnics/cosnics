<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be> - Added GroupBy
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RecordRetrievesParameters extends DataClassParameters
{

    public function __construct(
        ?DataClassProperties $dataClassProperties = null, ?Condition $condition = null, ?int $count = null,
        ?int $offset = null, ?OrderBy $orderBy = null, ?Joins $joins = null, ?GroupBy $groupBy = null,
        ?Condition $havingCondition = null
    )
    {
        parent::__construct(
            $condition, $joins, $dataClassProperties, $orderBy, $groupBy, $havingCondition, $count, $offset
        );
    }

    /**
     * @deprecated Use getGroupBy() now
     */
    public function get_group_by(): ?GroupBy
    {
        return $this->getGroupBy();
    }

    /**
     * @deprecated Use getDataClassProperties() now
     */
    public function get_properties(): ?DataClassProperties
    {
        return $this->getDataClassProperties();
    }

    /**
     * @deprecated Use setGroupBy() now
     */
    public function set_group_by(?GroupBy $groupBy = null)
    {
        $this->setGroupBy($groupBy);
    }

    /**
     * @deprecated Use setDataClassProperties() now
     */
    public function set_properties(?DataClassProperties $dataClassProperties = null)
    {
        $this->setDataClassProperties($dataClassProperties);
    }
}
