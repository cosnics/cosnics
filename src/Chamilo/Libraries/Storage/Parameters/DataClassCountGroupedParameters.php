<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;

/**
 * @package Chamilo\Libraries\Storage\Parameters
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassCountGroupedParameters extends DataClassParameters
{

    public function __construct(
        ?Condition $condition = null, ?RetrieveProperties $retrieveProperties = null,
        ?Condition $havingCondition = null, ?Joins $joins = null, ?GroupBy $groupBy = null
    )
    {
        if (is_null($groupBy))
        {
            $groupBy = new GroupBy($retrieveProperties->get());
        }

        parent::__construct($condition, $joins, $retrieveProperties, null, $groupBy, $havingCondition);
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
