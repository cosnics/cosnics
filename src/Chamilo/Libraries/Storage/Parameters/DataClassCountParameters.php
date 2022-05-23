<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
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

    public function __construct(?Condition $condition = null, ?Joins $joins = null, $dataClassProperties = null)
    {
        if ($dataClassProperties instanceof ConditionVariable)
        {
            $dataClassProperties = new DataClassProperties(array($dataClassProperties));
        }

        parent::__construct($condition, $joins, $dataClassProperties);
    }

    /**
     * @deprecated Use DataClassProperties and getDataClassProperties() now
     */
    public function get_property(): ?ConditionVariable
    {
        $dataClassProperties = $this->getDataClassProperties()->get();

        return array_shift($dataClassProperties);
    }

    /**
     * @deprecated Use DataClassProperties and setDataClassProperties() now
     */
    public function set_property(?ConditionVariable $property)
    {
        $this->getDataClassProperties()->add($property);
    }
}
