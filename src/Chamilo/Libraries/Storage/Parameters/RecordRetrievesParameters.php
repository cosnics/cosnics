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
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be> - Added GroupBy
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class RecordRetrievesParameters extends DataClassRetrievesParameters
{

    public function __construct(
        RetrieveProperties $retrieveProperties = new RetrieveProperties(), ?Condition $condition = null,
        ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy(), Joins $joins = new Joins(),
        GroupBy $groupBy = new GroupBy(), ?Condition $havingCondition = null
    )
    {
        parent::__construct(
            condition: $condition, count: $count, offset: $offset, orderBy: $orderBy, joins: $joins, groupBy: $groupBy,
            havingCondition: $havingCondition, retrieveProperties: $retrieveProperties
        );
    }
}
