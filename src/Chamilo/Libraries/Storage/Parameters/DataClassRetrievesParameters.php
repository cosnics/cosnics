<?php

namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;

/**
 * @package Chamilo\Libraries\Storage\Parameters
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassRetrievesParameters extends DataClassBasicRetrieveParameters
{

    public function __construct(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null,
        ?Joins $joins = null, ?GroupBy $groupBy = null, ?Condition $havingCondition = null,
        ?RetrieveProperties $retrieveProperties = null
    )
    {
        parent::__construct(
            $condition, $joins, $retrieveProperties, $orderBy, $groupBy, $havingCondition, $count, $offset
        );
    }
}
