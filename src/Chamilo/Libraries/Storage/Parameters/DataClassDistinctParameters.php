<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;

/**
 * @package Chamilo\Libraries\Storage\Parameters
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassDistinctParameters extends DataClassParameters
{

    public function __construct(
        ?Condition $condition = null, ?RetrieveProperties $retrieveProperties = null, ?Joins $joins = null,
        ?OrderBy $orderBy = null
    )
    {
        parent::__construct($condition, $joins, $retrieveProperties, $orderBy);
    }

    /**
     * @deprecated Use DataClassProperties and getRetrieveProperties() now
     */
    public function get_property(): ?RetrieveProperties
    {
        return $this->getRetrieveProperties();
    }

    /**
     * @deprecated Use DataClassProperties and setRetrieveProperties() now
     */
    public function set_property(?RetrieveProperties $properties = null): static
    {
        return $this->setRetrieveProperties($properties);
    }
}
