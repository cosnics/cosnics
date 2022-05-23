<?php

namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassRetrievesParameters extends DataClassParameters
{

    public function __construct(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null,
        ?Joins $joins = null, ?bool $distinct = false, ?GroupBy $groupBy = null, ?Condition $havingCondition = null
    )
    {
        parent::__construct(
            $condition, $joins, null, $orderBy, $groupBy, $havingCondition, $count, $offset, $distinct
        );
    }

    /**
     * @deprecated Use getCount() now
     */
    public function get_count(): ?int
    {
        return $this->getCount();
    }

    /**
     * @deprecated Use getDistinct() now
     */
    public function get_distinct(): bool
    {
        return $this->getDistinct();
    }

    /**
     * @deprecated Use getOffset() now
     */
    public function get_offset(): ?int
    {
        return $this->getOffset();
    }

    /**
     * @deprecated Use getCount() now
     */
    public function set_count(?int $count)
    {
        $this->setCount($count);
    }

    /**
     * @deprecated Use setDistinct() now
     */
    public function set_distinct(bool $distinct = false)
    {
        $this->setDistinct($distinct);
    }

    /**
     * @deprecated Use setOffset() now
     */
    public function set_offset(?int $offset)
    {
        $this->setOffset($offset);
    }
}
