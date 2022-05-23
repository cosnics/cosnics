<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Countable;

/**
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OrderBy implements Countable, Hashable
{
    use HashableTrait;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\OrderProperty[]
     */
    private array $orderProperties;

    /**
     * @param \Chamilo\Libraries\Storage\Query\OrderProperty[] $orderProperties
     */
    public function __construct(array $orderProperties = [])
    {
        $this->orderProperties = $orderProperties;
    }

    public function add(OrderProperty $orderProperty): OrderBy
    {
        $this->orderProperties[] = $orderProperty;

        return $this;
    }

    public function count(): int
    {
        return count($this->orderProperties);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\OrderProperty[]
     */
    public function get(): array
    {
        return $this->orderProperties;
    }

    public function getFirst(): OrderProperty
    {
        return $this->orderProperties[0];
    }

    public function getHashParts(): array
    {
        $hashes = [];

        foreach ($this->orderProperties as $orderProperty)
        {
            $hashes[] = $orderProperty->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }

    public function merge(OrderBy $orderByToMerge): OrderBy
    {
        foreach ($orderByToMerge->get() as $orderProperty)
        {
            $this->add($orderProperty);
        }

        return $this;
    }

    public function prepend(OrderProperty $orderProperty): OrderBy
    {
        array_unshift($this->orderProperties, $orderProperty);

        return $this;
    }
}
