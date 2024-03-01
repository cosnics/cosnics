<?php
namespace Chamilo\Libraries\Storage\Query;

use Chamilo\Libraries\Architecture\Interfaces\HashableInterface;
use Chamilo\Libraries\Architecture\Traits\HashableTrait;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements Collection<TKey,\Chamilo\Libraries\Storage\Query\OrderProperty>
 * @template-implements Selectable<TKey,\Chamilo\Libraries\Storage\Query\OrderProperty>
 * @psalm-consistent-constructor
 */
class OrderBy extends ArrayCollection implements HashableInterface
{
    use HashableTrait;

    public static function generate(string $dataClassName, string $propertyName, ?int $direction = SORT_ASC): OrderBy
    {
        return new OrderBy([new OrderProperty(new PropertyConditionVariable($dataClassName, $propertyName), $direction)]
        );
    }

    public function getFirst(?OrderProperty $defaultOrderProperty = null): ?OrderProperty
    {
        if (!$this->isEmpty())
        {
            $this->first();

            return $this->current();
        }

        return $defaultOrderProperty;
    }

    public function getHashParts(): array
    {
        $hashes = [];

        foreach ($this as $orderProperty)
        {
            $hashes[] = $orderProperty->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }

    public function merge(OrderBy $orderByToMerge): OrderBy
    {
        foreach ($orderByToMerge as $orderProperty)
        {
            $this->add($orderProperty);
        }

        return $this;
    }
}
