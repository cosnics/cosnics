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
class RecordRetrieveParameters extends DataClassRetrieveParameters
{

    public function __construct(
        ?RetrieveProperties $retrieveProperties = null, ?Condition $condition = null, ?OrderBy $orderBy = null,
        ?Joins $joins = null, ?GroupBy $groupBy = null
    )
    {
        parent::__construct($condition, $orderBy, $joins);
        $this->setRetrieveProperties($retrieveProperties);
        $this->setGroupBy($groupBy);
    }

    /**
     * @deprecated Use getRetrieveProperties() now
     */
    public function get_properties(): ?RetrieveProperties
    {
        return $this->getRetrieveProperties();
    }

    /**
     * @deprecated Use setRetrieveProperties() now
     */
    public function set_properties(?RetrieveProperties $retrieveProperties = null): static
    {
        return $this->setRetrieveProperties($retrieveProperties);
    }
}
