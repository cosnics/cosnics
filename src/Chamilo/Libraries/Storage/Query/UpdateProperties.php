<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;

/**
 *
 * @package Chamilo\Libraries\Storage\DataClass\Property
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdateProperties implements HashableInterface
{
    use HashableTrait;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\UpdateProperty[]
     */
    private array $updateProperties;

    /**
     * @param \Chamilo\Libraries\Storage\Query\UpdateProperty[] $updateProperties
     */
    public function __construct(array $updateProperties = [])
    {
        $this->updateProperties = $updateProperties;
    }

    public function add(UpdateProperty $updateProperty): UpdateProperties
    {
        $this->updateProperties[] = $updateProperty;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\UpdateProperty[]
     */
    public function get(): array
    {
        return $this->updateProperties;
    }

    public function getFirst(): UpdateProperty
    {
        return $this->updateProperties[0];
    }

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = __CLASS__;

        foreach ($this->get() as $updateProperty)
        {
            $hashParts[] = $updateProperty->getHashParts();
        }

        sort($hashParts);

        return $hashParts;
    }

    public function merge(UpdateProperties $updatePropertiesToMerge): UpdateProperties
    {
        foreach ($updatePropertiesToMerge->get() as $updateProperty)
        {
            $this->add($updateProperty);
        }

        return $this;
    }
}
