<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassCountParameters extends DataClassParameters
{

    public function __construct(?Condition $condition = null, ?Joins $joins = null, $retrieveProperties = null)
    {
        if ($retrieveProperties instanceof ConditionVariable)
        {
            $retrieveProperties = new RetrieveProperties(array($retrieveProperties));
        }

        parent::__construct($condition, $joins, $retrieveProperties);
    }

    /**
     * @deprecated Use getRetrieveProperties()->getFirst() now
     */
    public function get_property(): ?ConditionVariable
    {
        return $this->getRetrieveProperties()->getFirst();
    }

    /**
     * @deprecated Use getRetrieveProperties()->add now
     */
    public function set_property(?ConditionVariable $property)
    {
        $this->getRetrieveProperties()->add($property);
    }
}
