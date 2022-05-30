<?php
namespace Chamilo\Libraries\Storage\DataClass\Property;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;

/**
 *
 * @package Chamilo\Libraries\Storage\DataClass\Property
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassProperties implements Hashable
{
    use HashableTrait;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty[]|\Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    private array $properties;

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty[]|\Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $properties
     */
    public function __construct(array $properties = [])
    {
        $this->properties = $properties;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty|\Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $property
     */
    public function add($property): DataClassProperties
    {
        $this->properties[] = $property;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty[]|\Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    public function get(): array
    {
        return $this->properties;
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty|\Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function getFirst()
    {
        return $this->properties[0];
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\Hashable::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = __CLASS__;

        foreach ($this->get() as $property)
        {
            $hashParts[] = $property->getHashParts();
        }

        sort($hashParts);

        return $hashParts;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $dataClassPropertiesToMerge
     */
    public function merge(DataClassProperties $dataClassPropertiesToMerge)
    {
        foreach ($dataClassPropertiesToMerge->get() as $property)
        {
            $this->add($property);
        }
    }
}
