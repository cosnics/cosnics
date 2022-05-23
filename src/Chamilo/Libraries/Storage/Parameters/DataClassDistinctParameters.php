<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassDistinctParameters extends DataClassParameters
{

    public function __construct(
        ?Condition $condition = null, ?DataClassProperties $dataClassProperties = null, ?Joins $joins = null,
        ?OrderBy $orderBy = null
    )
    {
        parent::__construct($condition, $joins, $dataClassProperties, $orderBy);
    }

    /**
     * @deprecated Use DataClassProperties and getDataClassProperties() now
     */
    public function get_property(): ?DataClassProperties
    {
        return $this->getDataClassProperties();
    }

    /**
     * @deprecated Use DataClassProperties and setDataClassProperties() now
     */
    public function set_property(?DataClassProperties $properties = null)
    {
        $this->setDataClassProperties($properties);
    }
}
